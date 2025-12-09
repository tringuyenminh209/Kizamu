<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     *登録処理
     *POST /api/register
     */
    public function register(Request $request){
        // 詳細なバリデーション
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/'
        ], [
            'name.required' => '名前が必須です',
            'name.min' => '名前は2文字以上です',
            'email.required' => 'メールアドレスが必須です',
            'email.email' => 'メールアドレスの形式が正しくありません',
            'email.unique' => 'このメールアドレスは既に登録されています',
            'password.required' => 'パスワードが必須です',
            'password.min' => 'パスワードは8文字以上である必要があります',
            'password.regex' => 'パスワードは大文字、小文字、数字を含む必要があります'
        ]);

        try{
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => null // 登録時が未確認
            ]);

            // メール確認リンクを送信（本番環境の場合）
            if(env('APP_ENV') === 'production'){
                // Mail verificationを呼び出す
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            // セキュリティログ
            Log::info("ユーザー登録が成功しました",[
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'user' => $user,
                'token' => $token,
                'message' => '登録成功！'
            ], 201);
        }catch(\Exception $e){
            Log::error("登録に失敗しました", [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => '登録に失敗しました',
                'errors' => ['server' => ['サーバーエラーが発生しました']]
            ], 500);
        }
    }

    /**
     * ログイン処理
     * POST /api/login
     */
    public function login(Request $request){
        // バリデーション
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ],[
            'email.required' => 'メールアドレスは必須です',
            'email.email' => 'メールの形式が正しありません',
            'password.required' => 'パスワードは必須です'
        ]);

        // ログイン試行回数のチェック
        $attemptKey = 'login_attempts:' . $request->ip();
        $attempts = Cache::get( $attemptKey, 0);

        if($attempts > 5){
            Log::warning("試行回数超過によりログインがブロックされました", [
                'email' => $request->email,
                'ip' => $request->ip(),
                'attempts' => $attempts,
            ]);

            return response()->json([
                'message' => 'ログイン試行回数が上限に達しました。10分後に再試行してください',
                'errors' => ['login' => ['アカウントが一時的にロックされています']],
            ], 429);
        }

        // 認証試行
        if (!Auth::attempt($request->only('email', 'password'))){
            // 試行回数をインクリメント
            Cache::put($attemptKey, $attempts + 1, now()->addMinutes(10));

            //セキュリティログ
            Log::warning('ログインに失敗しました', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'attempts' => $attempts + 1,
            ]);

            return response()->json([
                'message' => 'メールアドレスまたはパスワードが正しくありません',
                'errors' => ['credentials' => ['認証情報が正しくありません']],
            ], 401);
        }

        // 成功時: 試行回数をリセット
        Cache::forget($attemptKey);

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        // セキュリティログ
        Log::info('ユーザーが正常にログインしました', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'ログイン成功！'
        ], 200);
    }

    /**
     * ログアウト処理
     * POST api/logout
     */
    public function logout(Request $request){
        $user = $request->user();

        // セキュリティログ
        Log::info("ユーザーがログアウトしました。", [
            'user_id'   => $user->id,
            'email'     => $user->email,
            'ip'        => $request->ip(),
        ]);

        //　現在のトークンを削除
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'ログアウト成功'
        ], 200);
    }

    /**
     * トークンをリフレッシュ
     * POST /api/refresh-token
     */
    public function refreshToken(Request $request){
        $user = $request->user();

        // 現在のトークンを削除
        $request->user()->currentAccessToken()->delete();

        // 新しいトークンを発行
        $token = $user->createToken('auth_token')->plainTextToken;

        // セキュリティログ
        Log::info('トークンがリフレッシュされました',[
            'user_id' => $user->id,
            'email'   => $user->email,
            'ip'      => $request->ip(),
        ]);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'トークンが更新されました'
        ],200);
    }

    /**
     * 現在のユーザー情報を取得
     * GET /api/user
     */
    public function getUser(Request $request){

        $user = $request->user();

        Log::info('getUser()が呼び出されまし', [
            'user' => $user ? $user->toArray() : null

        ]);

        if(!$user){
            $response = [
                'success' => false,
                'data' => null,
                'message' => 'ユーザーが見つかりません',
                'error' => 'ユーザーが見つかりません'
            ];
            Log::info('getUser()のレスポンス (見つかりません)', ['response' => $response]);

            return response()->json($response, 404);
        }

        $response = [
            'success' => true,
            'data' => $user->toArray(),
            'message' => 'ユーザー情報を取得しました',
            'error' => null
        ];

        Log::info("getUser()のレスポンス (成功)", ['response' => $response]);
        return response()->json($response, 200);
    }

    /**
     * Update FCM Token
     * POST /api/user/fcm-token
     */

    public function updateFCMToken(Request $request){
        $request->validate([
            'fmc_token' => 'required|string|max:500',
        ]);

        $user = $request->user();
        $user->update(['fmc_token' => $request->fcm_token]);

        Log::info("FCM token updated", [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FCM token updated successfully',
            'data' => [
                'fcm_token' => $user->fcm_token
            ]
        ], 200);
    }

}
