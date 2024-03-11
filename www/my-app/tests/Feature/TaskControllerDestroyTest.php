<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_destroy_task(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user, 'creator')->create();
        Sanctum::actingAs($user);

        $route = route('tasks.destroy', $task);
        $response = $this->deleteJson($route);

        $response->assertNoContent();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }
}
