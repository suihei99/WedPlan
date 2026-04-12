<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Task extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'task_name',
        'description',
        'deadline',
        'is_completed',
        'priority',
    ];

    protected function casts()
    {
        return [
            'deadline' => 'date',
            'is_completed' => 'boolean',
            'priority' => 'integer',
        ];
    }

    // Priority levels
    const PRIORITY_LOW = 0;

    const PRIORITY_MEDIUM = 1;

    const PRIORITY_HIGH = 2;

    // Relationship to User model
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope overdue tasks
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())->where('is_completed', false);
    }

    public function scopeByPriority($query, ?int $priority = null)
    {
        if ($priority !== null) {
            return $query->where('priority', $priority);
        }

        return $query->orderByDesc('priority');
    }
}
