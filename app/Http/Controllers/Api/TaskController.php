<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\TaskIndexRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $tasks)
    {
    }

    public function index(TaskIndexRequest $request): AnonymousResourceCollection
    {
        return TaskResource::collection($this->tasks->paginateFor($request->user(), $request->validated()));
    }

    public function store(StoreTaskRequest $request): TaskResource
    {
        return new TaskResource($this->tasks->create($request->user(), $request->validated()));
    }

    public function show(Task $task): TaskResource
    {
        return new TaskResource($this->tasks->findVisible(request()->user(), $task->id));
    }

    public function update(StoreTaskRequest $request, Task $task): TaskResource
    {
        $task = $this->tasks->findVisible($request->user(), $task->id);
        Gate::authorize('update', $task);

        return new TaskResource($this->tasks->update($task, $request->validated()));
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): TaskResource
    {
        $task = $this->tasks->findVisible($request->user(), $task->id);
        Gate::authorize('updateStatus', $task);
        $task->update($request->validated());

        return new TaskResource($task->load(['assignee:id,name,email', 'creator:id,name']));
    }

    public function destroy(Task $task): Response
    {
        $task = $this->tasks->findVisible(request()->user(), $task->id);
        Gate::authorize('delete', $task);
        $task->delete();

        return response()->noContent();
    }
}
