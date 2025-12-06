<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category = $request->query('category_id')) {
            $query->where('category_id', (int)$category);
        }

        $sortBy = $request->query('sort_by', 'created_at');
        $order = $request->query('order', 'desc');
        $allowedSorts = ['title', 'priority', 'due_date', 'created_at', 'status'];
        if (! in_array($sortBy, $allowedSorts)) $sortBy = 'created_at';
        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $order);

        $limit = (int) $request->query('limit', 10);
        $result = $query->paginate($limit)->appends($request->query());

        return response()->json($result);
    }

    public function show($id)
    {
        $task = Task::find($id);
        if (!$task) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        return response()->json($task);
    }

    public function store(StoreTaskRequest $request)
    {
        $data = $request->only(['title', 'description', 'status', 'priority', 'due_date', 'category_id']);
        $user = auth()->user();
        $data['created_by'] = $user ? $user->id : null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('tasks', config('filesystems.default')); // 'public' or 's3'
            $data['attachment_path'] = $path;
            $data['attachment_type'] = $file->getClientMimeType();
        }

        $task = Task::create($data);
        return response()->json(['success' => true, 'task' => $task], 201);
    }

    public function update(StoreTaskRequest $request, $id)
    {
        $task = Task::find($id);
        if (!$task) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        $data = $request->only(['title', 'description', 'status', 'priority', 'due_date', 'category_id']);

        if ($request->hasFile('attachment')) {
            if ($task->attachment_path && Storage::exists($task->attachment_path)) {
                Storage::delete($task->attachment_path);
            }
            $file = $request->file('attachment');
            $path = $file->store('tasks', config('filesystems.default'));
            $data['attachment_path'] = $path;
            $data['attachment_type'] = $file->getClientMimeType();
        }

        $task->update($data);
        return response()->json(['success' => true, 'task' => $task]);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        if (!$task) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        if ($task->attachment_path && Storage::exists($task->attachment_path)) {
            Storage::delete($task->attachment_path);
        }
        $task->delete();
        return response()->json(['success' => true, 'message' => 'Deleted']);
    }
}
