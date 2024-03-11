<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user, 'creator')->create();
        Sanctum::actingAs($user);

        $route = route('tasks.update', $task);
        $response = $this->putJson($route, [
            'name' => 'foo2',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('tasks', [
            'name'       => 'foo2',
            'creator_id' => $user->id,
        ]);
    }

    public function test_cannot_update_as_project_member(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->members()->attach($user);
        $task = Task::factory()->for($project->creator, 'creator')->for($project, 'project')->create();
        
        Sanctum::actingAs($user);

        $route = route('tasks.update', $task);
        $response = $this->putJson($route, [
            'name' => 'foo2',
        ]);

        $response->assertForbidden();
    }

    public function test_unauthorized_update(): void
    {
        $task = Task::factory()->create();

        $route = route('tasks.update', $task);
        $response = $this->putJson($route, [
            'name' => 'foo2',
        ]);

        $response->assertUnauthorized();
    }

    public function test_no_response(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();
        Sanctum::actingAs($user);

        $route = route('tasks.update', $task);
        $response = $this->putJson($route, [
            'name' => 'foo2',
        ]);

        $response->assertNotFound();
    }
}
