<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_crud_is_scoped_to_creator_and_assignee(): void
    {
        $owner = User::factory()->create();
        $assignee = User::factory()->create();
        $other = User::factory()->create();
        $task = Task::create([
            'title' => 'Prepare report',
            'priority' => 'high',
            'status' => 'pending',
            'created_by' => $owner->id,
            'assigned_to' => $assignee->id,
        ]);

        $this->actingAs($assignee, 'sanctum')->getJson('/api/tasks')->assertOk()->assertJsonPath('data.0.title', 'Prepare report');
        $this->actingAs($other, 'sanctum')->getJson('/api/tasks/'.$task->id)->assertNotFound();
        $this->actingAs($assignee, 'sanctum')->putJson('/api/tasks/'.$task->id, [
            'title' => 'Changed task', 'priority' => 'high', 'status' => 'pending',
        ])->assertForbidden();
        $this->actingAs($assignee, 'sanctum')->patchJson('/api/tasks/'.$task->id.'/status', [
            'status' => 'completed',
        ])->assertOk()->assertJsonPath('data.status', 'completed');
    }

    public function test_creator_can_filter_tasks_and_view_dashboard_totals(): void
    {
        $owner = User::factory()->create();
        Task::create([
            'title' => 'Overdue report', 'due_date' => now()->subDay()->toDateString(),
            'priority' => 'high', 'status' => 'pending', 'created_by' => $owner->id,
        ]);

        $this->actingAs($owner, 'sanctum')
            ->getJson('/api/tasks?search=Overdue&priority=high&sort_by=title')
            ->assertOk()->assertJsonPath('data.0.title', 'Overdue report');
        $this->actingAs($owner, 'sanctum')->getJson('/api/dashboard')
            ->assertOk()->assertJsonPath('stats.total', 1)->assertJsonPath('stats.overdue', 1);
    }
}
