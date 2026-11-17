<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    public function paginateFor(User $user, array $filters): LengthAwarePaginator
    {
        $query = Task::query()
            ->visibleTo($user)
            ->with(['assignee:id,name,email', 'creator:id,name']);

        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%'.$filters['search'].'%');
        }

        foreach (['status', 'priority'] as $filter) {
            if (!empty($filters[$filter])) {
                $query->where($filter, $filters[$filter]);
            }
        }

        if (!empty($filters['due_date_from'])) {
            $query->whereDate('due_date', '>=', $filters['due_date_from']);
        }

        if (!empty($filters['due_date_to'])) {
            $query->whereDate('due_date', '<=', $filters['due_date_to']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $direction = $filters['sort_direction'] ?? 'desc';

        return $query->orderBy($sortBy, $direction)
            ->orderBy('id', 'desc')
            ->paginate((int) ($filters['per_page'] ?? 10))
            ->withQueryString();
    }

    public function dashboardFor(User $user): array
    {
        $base = Task::query()->visibleTo($user);
        $totals = (clone $base)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN due_date < ? AND status != 'completed' THEN 1 ELSE 0 END) as overdue", [now()->toDateString()])
            ->first();

        $recent = (clone $base)
            ->with(['assignee:id,name,email', 'creator:id,name'])
            ->latest()
            ->limit(6)
            ->get();

        return ['stats' => $totals, 'recent_tasks' => $recent];
    }

    public function findVisible(User $user, int $taskId): Task
    {
        return Task::query()->visibleTo($user)->with(['assignee:id,name,email', 'creator:id,name'])->findOrFail($taskId);
    }

    public function create(User $creator, array $data): Task
    {
        $task = $creator->createdTasks()->create($data);

        return $task->load(['assignee:id,name,email', 'creator:id,name']);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->load(['assignee:id,name,email', 'creator:id,name']);
    }
}
