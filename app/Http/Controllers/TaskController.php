<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TaskController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view tasks', only: ['index']),
            new Middleware('permission:edit tasks', only: ['edit']),
            new Middleware('permission:create tasks', only: ['create']),
            new Middleware('permission:delete tasks', only: ['destroy']),
        ];
    }
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            $tasks = Task::all();
        } else {
            $tasks = Task::where('assigned_to', $user->id)->get();
        }

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();

        return view('tasks.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required',
            'description' => 'nullable|string',
            'assigned_to' => 'required',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls',
            'due_date' => 'required|date',
            'status' => 'required|string|in:pending,in_progress,completed',
        ]);

        // Proses simpan data
        // Misalnya simpan file jika ada
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('documents', 'public');
            $validate['file'] = $filePath;
        }

        // Simpan ke database
        Task::create($validate);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    
    public function edit(string $id)
    {
        $task = Task::findOrFail($id);
        $users = User::all();
        return view('tasks.edit', compact('task', 'users'));
    }


    public function update(Request $request, string $id)
    {
        $validate = $request->validate([
            'title' => 'required',
            'description' => 'nullable|string',
            'assigned_to' => 'required',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls',
            'due_date' => 'required|date',
            'status' => 'required|string|in:pending,in_progress,completed',
        ]);

        $task = Task::findOrFail($id);
        if ($request->hasFile('file')) {
            if ($task->file) {
                // Hapus file lama jika ada
                \Storage::disk('public')->delete($task->file);
            }

            //upload file baru
            $filePath = $request->file('file')->store('documents', 'public');
            $validate['file'] = $filePath;
        } else {
            // Jika tidak ada file baru, tetap gunakan file lama
            $validate['file'] = $task->file;
        }

        // Update task
        $task->update($validate);
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function pending(int $id)
    {
        $task = Task::find($id);
        $task->update(['status' => 'pending']);

        return redirect()->route('tasks.index')->with('success', 'Tugas Belum Terselesaikan.');
    }

    public function selesai(int $id)
    {
        $task = Task::find($id);
        $task->update(['status' => 'selesai']);

        return redirect()->route('tasks.index')->with('success', 'Tugas Sudah Terselesaikan.');
    }

    public function destroy(string $id)
    {

        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }


}
