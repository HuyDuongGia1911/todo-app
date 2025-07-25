<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskAdminController extends Controller
{
    public function index()
    {
        // Lấy tất cả task của tất cả user (admin)
        // Nếu muốn join user để show tên, có thể with('user:id,name')
        return Task::orderBy('task_date', 'desc')->get();
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        // Nếu muốn cho admin chọn user_id để giao việc:
        // $data['user_id'] = $request->input('user_id');
        // Tạm thời mặc định chính admin là owner:
        $data['user_id'] = auth()->id();

        $task = Task::create($data);
        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        $data = $this->validateData($request, $task->id);

        $task->update($data);
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success' => true]);
    }

    private function validateData(Request $request, $id = null): array
    {
        return $request->validate([
            'title'     => 'required|string|max:255',
            'task_date' => 'required|date',
            'priority'  => 'required|string|in:Low,Normal,High',
            'status'    => 'required|string|in:Chưa hoàn thành,Đã hoàn thành',
            'progress'  => 'nullable|integer|min:0|max:100',
            // 'user_id' => 'required|exists:users,id' // nếu cho chọn người nhận task
        ]);
    }
}
