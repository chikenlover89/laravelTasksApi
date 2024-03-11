<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_tasks_can_have_comments(): void
    {
        $task = Task::factory()->create();
        $comment = $task->comments()->make([
            'content' => 'Task Comment',
        ]);

        $comment->user()->associate($task->creator);
        $comment->save();

        $this->assertModelExists($comment);
    }

    public function test_projects_can_have_comments(): void
    {
        $project = Project::factory()->create();
        $comment = $project->comments()->make([
            'content' => 'Task Comment',
        ]);

        $comment->user()->associate($project->creator);
        $comment->save();

        $this->assertModelExists($comment);
    }
}
