<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;

class TaskControllerIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_fetch(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Task::factory()->for($user, 'creator')->create();

        $route    = route('tasks.index');
        $response = $this->getJson($route);

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [ // applys to all array elements (tasks can be many with same structure)
                        'id',
                        'name',
                        'is_done',
                        'status',
                    ]
                ]
            ]);
    }

    /**
     * @dataProvider filterFields
     */
    public function test_filterable_fields($field, $value, $expectedCode): void 
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Task::factory()->for($user, 'creator')->create();

        $route    = route('tasks.index', ["filter[{$field}]" => $value]);
        $response = $this->getJson($route);
        $response->assertStatus($expectedCode);
    }

     /**
     * @dataProvider sortableFields
     */
    public function test_sortable_fields($field, $expectedCode): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        Task::factory()->for($user, 'creator')->create();

        $route    = route('tasks.index', ["sort" => $field]);
        $response = $this->getJson($route);
        $response->assertStatus($expectedCode);
    }

    public static function sortableFields(): array
    {
        return [
            ['is_done', 200],
            ['name', 200],
            ['created_at', 200],
            ['updated_at', 400],
        ];
    }

    public static function filterFields(): array
    {
        return [
            ['id', 1, 400],
            ['name', 'foo', 400],
            ['is_done', 1, 200],
        ];
    }

    public function test_unauthenticated_users_can_not_fetch(): void
    {
        $route    = route('tasks.index');
        $response = $this->getJson($route);

        $response->assertUnauthorized();
    }
}
