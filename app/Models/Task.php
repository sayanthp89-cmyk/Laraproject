<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    public const STATUSES = ['pending', 'in_progress', 'completed'];
    public const PRIORITIES = ['low', 'medium', 'high'];

    protected $fillable = ['title', 'description', 'due_date', 'priority', 'status', 'assigned_to', 'created_by'];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $query) use ($user): void {
            $query->where('created_by', $user->id)->orWhere('assigned_to', $user->id);
        });
    }
}
