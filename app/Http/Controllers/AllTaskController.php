<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class AllTaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query();
        if ($request->filled('start_date')) $query->whereDate('task_date', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('task_date', '<=', $request->end_date);

        $tasks = $query->orderBy('task_date', 'desc')->get();
        return view('tasks.all', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.all_create');
    }

   public function store(Request $request)
{
    // Thêm Shift nếu chưa có và không rỗng
    if (!empty($request->shift) && !\App\Models\Shift::where('shift_name', $request->shift)->exists()) {
        \App\Models\Shift::create(['shift_name' => $request->shift]);
    }

    // Thêm Type nếu chưa có và không rỗng
    if (!empty($request->type) && !\App\Models\TaskType::where('type_name', $request->type)->exists()) {
        \App\Models\TaskType::create(['type_name' => $request->type]);
    }

    // Thêm Title nếu chưa có và không rỗng
    if (!empty($request->title) && !\App\Models\TaskTitle::where('title_name', $request->title)->exists()) {
        \App\Models\TaskTitle::create(['title_name' => $request->title]);
    }

    // Thêm Supervisor nếu chưa có và không rỗng
    if (!empty($request->supervisor) && !\App\Models\Supervisor::where('supervisor_name', $request->supervisor)->exists()) {
        \App\Models\Supervisor::create(['supervisor_name' => $request->supervisor]);
    }

    // Thêm Status nếu chưa có và không rỗng
    if (!empty($request->status) && !\App\Models\Status::where('status_name', $request->status)->exists()) {
        \App\Models\Status::create(['status_name' => $request->status]);
    }

    $data = $request->all();
    $data['user_id'] = auth()->id(); // Nếu có user

    Task::create($data);

    $redirect = $request->redirect_back ?? route('all.index');
    return redirect($redirect)->with('success', 'Đã thêm công việc vào danh sách tất cả!');
}
    public function edit(Task $task)
    {
        return view('tasks.all_edit', compact('task'));
    }

    public function update(Request $request, Task $task)
{
    // Thêm Shift nếu chưa có và không rỗng
    if (!empty($request->shift) && !\App\Models\Shift::where('shift_name', $request->shift)->exists()) {
        \App\Models\Shift::create(['shift_name' => $request->shift]);
    }

    // Thêm Type nếu chưa có và không rỗng
    if (!empty($request->type) && !\App\Models\TaskType::where('type_name', $request->type)->exists()) {
        \App\Models\TaskType::create(['type_name' => $request->type]);
    }

    // Thêm Title nếu chưa có và không rỗng
    if (!empty($request->title) && !\App\Models\TaskTitle::where('title_name', $request->title)->exists()) {
        \App\Models\TaskTitle::create(['title_name' => $request->title]);
    }

    // Thêm Supervisor nếu chưa có và không rỗng
    if (!empty($request->supervisor) && !\App\Models\Supervisor::where('supervisor_name', $request->supervisor)->exists()) {
        \App\Models\Supervisor::create(['supervisor_name' => $request->supervisor]);
    }

    // Thêm Status nếu chưa có và không rỗng
    if (!empty($request->status) && !\App\Models\Status::where('status_name', $request->status)->exists()) {
        \App\Models\Status::create(['status_name' => $request->status]);
    }

    $task->update($request->all());

    $redirect = $request->redirect_back ?? route('all.index');
    return redirect($redirect)->with('success', 'Đã cập nhật!');
}

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('all.index')->with('success', 'Đã xoá!');
    }
}
