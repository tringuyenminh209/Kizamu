<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskController extends Controller
{
    /**
     * リソースの一覧を表示する
     * GET /api/tasks
     */
    public function index(Request $request){

        $user = $request->user();

        $query = Task::with(['project', 'learningMilestone', 'subtasks', 'tags'])
            ->where('user_id', $user->id);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
    }

    /**
     * 新しく作成したリソースを保存する
     * POST /api/tasks
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category' => 'nullable|in:study,work,personal,other',
            'description' => 'nullable|string|max:1000',
            'priority' => 'required|integer|min:1|max:5',
            'energy_level' => 'required|in:low,medium,high',
            'estimated_minutes' => 'nullable|integer|min:1|max:600',
            'deadline' => 'nullable|date', // Removed after_or_equal:today to allow past dates
            'scheduled_time' => 'nullable|date_format:H:i:s',
            'project_id' => 'nullable|exists:projects,id',
            'learning_milestone_id' => 'nullable|exists:learning_milestones,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,id',
            // Focus enhancement fields
            'requires_deep_focus' => 'nullable|boolean',
            'allow_interruptions' => 'nullable|boolean',
            'focus_difficulty' => 'nullable|integer|min:1|max:5',
            'warmup_minutes' => 'nullable|integer|min:0|max:60',
            'cooldown_minutes' => 'nullable|integer|min:0|max:60',
            'recovery_minutes' => 'nullable|integer|min:0|max:120',
        ]);

        if ($validator->fails()) {
            Log::error('タクスのバリデーションに失敗しました', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->except(['password'])
            ]);
            return response()->json([
                'success' => false,
                'message' => 'バリデーションエラー',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $task = Task::create([
                'user_id' => $request->user()->id,
                'title' => $request->title,
                'category' => $request->category ?? 'other',
                'description' => $request->description,
                'priority' => $request->priority,
                'energy_level' => $request->energy_level,
                'estimated_minutes' => $request->estimated_minutes,
                'deadline' => $request->deadline,
                'scheduled_time' => $request->scheduled_time,
                // 'project_id' => $request->project_id,
                'learning_milestone_id' => $request->learning_milestone_id,
                'status' => 'pending',
                'ai_breakdown_enabled' => false,
                // Focus enhancement fields
                'requires_deep_focus' => $request->requires_deep_focus ?? false,
                'allow_interruptions' => $request->allow_interruptions ?? true,
                'focus_difficulty' => $request->focus_difficulty ?? 3,
                'warmup_minutes' => $request->warmup_minutes,
                'cooldown_minutes' => $request->cooldown_minutes,
                'recovery_minutes' => $request->recovery_minutes,
            ]);

            // Attach tags if provided
            if ($request->has('tag_ids')) {
                $task->tags()->attach($request->tag_ids);
            }

            DB::commit();

            $task->load(['project', 'learningMilestone', 'subtasks', 'tags']);

            return response()->json([
                'success' => true,
                'data' => $task,
                'message' => 'タスクを作成しました！'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'タスクの作成に失敗しました',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    /**
     * 指定さてたリソースを表示する
     * GET /api/task/{id}
     */
    public function show(Request $request, string $id): JsonResponse{

        $task = Task::with([
            'project',
            'learningMilestone',
            'subtasks',
            'tags',
            'focusSessions' => function($query) {
                $query->orderBy('started_at', 'desc')->limit(10);
            }
        ])
        ->where('user_id', $request->user()->id)
        ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $task,
            'message' => 'タスク詳細を取得しました'
        ]);
    }
}
