<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $route = route('tasks.store');
        $response = $this->postJson($route, [
            'name' => 'foo',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('tasks', [
            'name'       => 'foo',
            'creator_id' => $user->id,
        ]);
    }

    public function test_title_is_required(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $route = route('tasks.store');
        $response = $this->postJson($route, []);

        $response->assertJsonValidationErrors(['name' => 'required']);
    }

    public function test_cannot_create_tasks_for_other_projects(): void
    {
        $user    = User::factory()->create();
        $project = Project::factory()->create();
        Sanctum::actingAs($user);

        $route = route('tasks.store');
        $response = $this->postJson($route, [
            'name'       => 'foo',
            'project_id' => $project->id
        ]);

        $response->assertJsonValidationErrors(['project_id' => 'in']);
    }
}
