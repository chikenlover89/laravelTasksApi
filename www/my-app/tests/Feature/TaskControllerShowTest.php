<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_creators_can_see_created_tasks(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user, 'creator')->create();

        Sanctum::actingAs($user);
        $route = route('tasks.show', $task);
        $response = $this->getJson($route);
        $response->assertOk()
            ->assertJson([
                'data' => [
                    'id'         => $task->id,
                    'creator_id' => $user->id,
                    'project_id' => null,
                    //'created_at' => $task->created_at->jsonSerialize(),
                ]
            ]);
    }

    public function test_authenticated_response(): void
    {
        $task = Task::factory()->create();

        $route = route('tasks.show', $task);
        $response = $this->getJson($route);
        $response->assertUnauthorized();
    }

    public function test_no_access_response(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        Sanctum::actingAs($user);
        $route = route('tasks.show', $task);
        $response = $this->getJson($route);
        $response->assertNotFound();
    }
}
