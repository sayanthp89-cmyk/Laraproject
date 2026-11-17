<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Taylor Morgan', 'password' => Hash::make('password')]
        );
        $alex = User::updateOrCreate(
            ['email' => 'alex@example.com'],
            ['name' => 'Alex Chen', 'password' => Hash::make('password')]
        );

        $samples = [
            ['title' => 'Prepare product launch brief', 'description' => 'Align messaging and launch milestones with the team.', 'due_date' => now()->addDays(2)->toDateString(), 'priority' => 'high', 'status' => 'in_progress', 'assigned_to' => $alex->id],
            ['title' => 'Review Q3 customer feedback', 'description' => 'Summarize common requests and share key insights.', 'due_date' => now()->addDays(5)->toDateString(), 'priority' => 'medium', 'status' => 'pending', 'assigned_to' => $admin->id],
            ['title' => 'Update onboarding checklist', 'description' => 'Add the new account setup steps.', 'due_date' => now()->subDay()->toDateString(), 'priority' => 'low', 'status' => 'pending', 'assigned_to' => $alex->id],
            ['title' => 'Publish release notes', 'description' => 'Document recent improvements and fixes.', 'due_date' => now()->subDays(2)->toDateString(), 'priority' => 'medium', 'status' => 'completed', 'assigned_to' => $admin->id],
        ];

        foreach ($samples as $index => $sample) {
            Task::updateOrCreate(
                ['title' => $sample['title'], 'created_by' => $index % 2 === 0 ? $admin->id : $alex->id],
                $sample
            );
        }
    }
}
