<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'learning_milestone_id',
        'title',
        'category',
        'description',
        'priority',
        'energy_level',
        'estimated_minutes',
        'deadline',
        'scheduled_time',
        'status',
        'ai_breakdown_enabled',
        // Focus enhancement features
        'requires_deep_focus',
        'allow_interruptions',
        'focus_difficulty',
        'warmup_minutes',
        'cooldown_minutes',
        'recovery_minutes',
        'last_focus_at',
        'total_focus_minutes',
        'distraction_count',
        // Abandonment tracking
        'last_active_at',
        'is_abandoned',
        'abandonment_count',
    ];

        protected $casts = [
        // deadline is cast as 'datetime' but will be formatted as date-only in serialization
        'deadline' => 'datetime',
        // scheduled_time is TIME type (HH:MM:SS) - no casting needed, returns as string
        'priority' => 'integer',
        'estimated_minutes' => 'integer',
        'ai_breakdown_enabled' => 'boolean',
        // Focus enhancement features
        'requires_deep_focus' => 'boolean',
        'allow_interruptions' => 'boolean',
        'focus_difficulty' => 'integer',
        'warmup_minutes' => 'integer',
        'cooldown_minutes' => 'integer',
        'recovery_minutes' => 'integer',
        'last_focus_at' => 'datetime',
        'total_focus_minutes' => 'integer',
        'distraction_count' => 'integer',
        // Abandonment tracking
        'last_active_at' => 'datetime',
        'is_abandoned' => 'boolean',
        'abandonment_count' => 'integer',
    ];

    /**
     * Attributes to append to JSON serialization
     */
    protected $appends = [
        'remaining_minutes',
        'learning_path_id',
    ];

    /**
     * Serialize deadline as date-only (Y-m-d) to avoid timezone issues
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
