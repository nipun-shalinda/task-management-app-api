<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // GET /api/tasks
    public function index()
    {
        // Retrieve all tasks for the authenticated user
        $tasks = Auth::user()->tasks()->with('category')->get();
        return response()->json($tasks);
    }

    // POST /api/tasks
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'completed' => 'boolean',
        ]);

        // Create a new task for the authenticated user
        $task = Auth::user()->tasks()->create($validated);

        return response()->json($task, 201);
    }

    // PUT /api/tasks/{id}
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Ensure the authenticated user owns the task
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'completed' => 'boolean',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    // DELETE /api/tasks/{id}
    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        // Ensure the authenticated user owns the task
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
