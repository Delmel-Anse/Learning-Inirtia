<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // Ensure the authenticated user is correctly retrieved
        $user = $request->user();

        // If no user is authenticated, redirect to login (optional)
        if (!$user) {
            return redirect()->route('login')->withErrors(['message' => 'You need to log in first.']);
        }

        // Fetch tasks for the authenticated user
        $tasks = Task::where('user_id', $user->id)->get();

        // Return tasks to the Inertia view
        return inertia('Task/Index', [
            'tasks' => $tasks
        ]);
    }

    public function store(Request $request)
    {
       // Validate input
       $validated = $request->validate([
        'description' => 'required|string|min:10|max:255',
    ]);


        // Create the task and automatically assign the logged-in user's id
        $task = Task::create([
            'user_id' => $request->user()->id, // Automatically bind the user_id
            'description' => $validated['description'],
            'completed' => false,
        ]);

        return response()->json($task, 201);
    }

    public function toggle(Task $task, Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'completed' => 'required|boolean',
        ]);

        // Update the task
        $task->update([
            'completed' => $validated['completed'],
        ]);

        return response()->json($task, 200);
    }

    public function destroy(Task $task)
    {
    // Delete the task
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully.'], 200);
    }

    public function edit(Task $task, Request $request)
    {
    // Validate input
        $validated = $request->validate([
        'description' => 'required|string|max:255',
        'completed' => 'nullable|boolean',
        ]);

    // Update the task
        $task->update($validated);

        return response()->json($task, 200);
    }
}
