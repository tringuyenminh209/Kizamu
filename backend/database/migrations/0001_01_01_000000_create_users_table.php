<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment("ユーザー名");
            $table->string('email')->unique()->comment("メールアドレス");
            $table->string("fcm_token", 500)->nullable()->comment("Firebase Cloud Messagingトークン");
            $table->timestamp('email_verified_at')->nullable()->comment("メール確認日時");
            $table->string('password', 255)->comment("パスワード（ハッシュ化）");
            $table->enum('language', ['vi', 'en', 'ja'])->default('ja')->comment('UI言語');
            $table->string('timezone',50)->default('Asia/Tokyo')->comment('タイムゾーン');
            $table->rememberToken();
            $table->timestamps();

            // Indexes
            $table->index('email');
            $table->index('fcm_token');
            $table->index('created_at');
        });

        // パスワードリセットトークンテーブル
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // セッションテーブル（Laravel HTTP sessions用）
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * マイグレーションをロールバック
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
