<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:120'],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ]);
        $users = User::query()
            ->when($validated['search'] ?? null, fn ($query, $search) => $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%');
            }))
            ->withCount(['createdTasks', 'assignedTasks'])
            ->orderBy('name')
            ->paginate((int) ($validated['per_page'] ?? 10))
            ->withQueryString();

        return UserResource::collection($users);
    }

    public function show(User $user): JsonResponse
    {
        $user->loadCount(['createdTasks', 'assignedTasks']);
        $tasks = $user->createdTasks()
            ->with(['assignee:id,name,email', 'creator:id,name'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'user' => new UserResource($user),
            'recent_tasks' => TaskResource::collection($tasks)->resolve(),
        ]);
    }
}
