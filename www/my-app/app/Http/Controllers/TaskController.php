<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    public function index(Request $request) {
        $tasks = QueryBuilder::for(Task::class)
        ->allowedFilters(
            'is_done',
            'scheduled_at',
            'due_at',
            AllowedFilter::scope('scheduled_between'),
            AllowedFilter::scope('due_between'),
            AllowedFilter::scope('due', null, 'past,today'),
        )
        ->defaultSort('-created_at')
        ->allowedSorts(['created_at', 'name', 'is_done'])
        ->paginate();
        
        return new TaskCollection($tasks);
    }

    // Laravel model binding
    public function show(Request $request, Task $task) {
        return new TaskResource($task);
    }

    public function store(StoreTaskRequest $request) {
        $validated = $request->validated();

        $task = Auth::user()->tasks()->create($validated);

        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task) {
        $validated = $request->validated();

        $task->update($validated);

        return new TaskResource($task);
    }

    public function destroy(DestroyTaskRequest $request, Task $task) {
        $request->validated();
        $task->delete();

        return response()->noContent();
    }
}
