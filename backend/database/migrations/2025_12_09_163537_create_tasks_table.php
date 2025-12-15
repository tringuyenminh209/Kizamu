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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Foreign Keys
            $table->foreignId('project_id')
                ->nullable()
                // ->constrained('projects')
                ->onDelete('set null')
                ->comment('プロジェクトID');

            $table->foreignId('learning_milestone_id')
                ->nullable()
                // ->constrained('learning_milestones')
                ->onDelete('cascade')
                ->comment('学習マイルストーンID（Learning Path機能）');

            // Task Information
            $table->string('title', 255)->comment('タスクタイトル');
            $table->enum('category', ['study', 'work', 'personal', 'other'])
                ->default('other')
                ->comment('タスクカテゴリー（学習/仕事/個人/その他）');
            $table->text('description')->nullable()->comment('詳細説明');

            // Priority & Energy
            $table->tinyInteger('priority')->default(3)
                ->comment('優先度（1-5、5が最高）');
            $table->enum('energy_level', ['low', 'medium', 'high'])
                ->default('medium')
                ->comment('必要なエネルギーレベル');

            // Time Estimation
            $table->integer('estimated_minutes')->nullable()
                ->comment('予想時間（分）');
            $table->timestamp('deadline')->nullable()
                ->comment('締め切り');
            $table->timestamp('scheduled_time')->nullable()
                ->comment('予定開始時刻 (タスクを開始する予定の時刻)');

            // Status
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])
                ->default('pending')
                ->comment('タスクステータス');
            $table->boolean('is_abandoned')->default(false)
                ->comment('放棄されたタスク');

            // AI Features
            $table->boolean('ai_breakdown_enabled')->default(false)
                ->comment('AIによる分解済み');

            // Deep Work Mode features
            $table->boolean('requires_deep_focus')->default(false)
                ->comment('Deep Work Modeが必要');
            $table->boolean('allow_interruptions')->default(true)
                ->comment('割り込みを許可するか');
            $table->integer('focus_difficulty')->default(3)
                ->comment('集中難易度（1-5: shallow to ultra-deep focus）');

            // Time management features
            $table->integer('warmup_minutes')->nullable()
                ->comment('タスク前の準備時間（分）');
            $table->integer('cooldown_minutes')->nullable()
                ->comment('タスク後の振り返り時間（分）');
            $table->integer('recovery_minutes')->nullable()
                ->comment('タスク完了後の休息時間（分）');

            // Context tracking
            $table->timestamp('last_focus_at')->nullable()
                ->comment('最後にこのタスクに集中した時刻');
            $table->timestamp('last_active_at')->nullable()
                ->comment('最後にアクティブだった時刻（heartbeat更新）');
            $table->integer('total_focus_minutes')->default(0)
                ->comment('集中に費やした合計時間（分）');
            $table->integer('distraction_count')->default(0)
                ->comment('記録された気が散った回数');
            $table->integer('abandonment_count')->default(0)
                ->comment('放棄された回数');

            $table->timestamps();

            // Indexes for Performance
            $table->index(['user_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index('learning_milestone_id');
            $table->index('deadline');
            $table->index('priority');
            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'scheduled_time']);
            $table->index(['status', 'last_active_at']);
            $table->index(['user_id', 'is_abandoned']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
