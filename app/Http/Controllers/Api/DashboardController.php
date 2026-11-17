<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(TaskService $tasks): JsonResponse
    {
        $dashboard = $tasks->dashboardFor(request()->user());
        $stats = $dashboard['stats'];

        return response()->json([
            'stats' => [
                'total' => (int) $stats->total,
                'pending' => (int) $stats->pending,
                'in_progress' => (int) $stats->in_progress,
                'completed' => (int) $stats->completed,
                'overdue' => (int) $stats->overdue,
            ],
            'recent_tasks' => TaskResource::collection($dashboard['recent_tasks'])->resolve(),
        ]);
    }
}
