<?php
namespace App\Http\Controllers;

use App\Models\Deadline;
use App\Models\Task;
use App\Models\TaskTitle;
use Illuminate\Http\Request;

class DeadlineController extends Controller
{
    public function index()
    {
        $deadlines = Deadline::orderBy('end_date')->get();
        return view('deadlines.index', compact('deadlines'));
    }

    public function create()
{
    $tasks = TaskTitle::pluck('title_name'); // Lấy từ task_titles
    return view('deadlines.create', compact('tasks'));
}

   public function store(Request $request)
{
   // Lấy danh sách task_titles → nối thành chuỗi
    $taskNames = implode(',', $request->task_titles ?? []);

    // Tạo deadline
    $deadline = Deadline::create([
        'start_date'      => $request->start_date,
        'end_date'        => $request->end_date,
        'name'            => $request->name,
        'task_names'      => $taskNames, // THÊM VÀO ĐÂY
        'note'            => $request->note,
    ]);

    // Lưu từng task
    foreach ($request->task_titles as $index => $title) {
        // Nếu chưa có task thì tạo mới
            if (!TaskTitle::where('title_name', $title)->exists()) {
        TaskTitle::create(['title_name' => $title]);
    }
        // if (!Task::where('title', $title)->exists()) {
        //     Task::create([
        //         'title' => $title,
        //         'user_id' => auth()->id(),
        //         'task_date' => now()->toDateString(),
        //         'shift' => '',
        //         'type' => '',
        //         'supervisor' => '',
        //         'status' => '',
        //         'detail' => '',
        //         'progress' => 0,
        //         'file_link' => '',
        //     ]);
        // }

        // Lưu vào bảng deadline_tasks
        $deadline->tasks()->create([
            'task_title' => $title,
            'target_progress' => $request->target_progresses[$index] ?? 0,
        ]);
    }

    return redirect()->route('deadlines.index')->with('success', 'Đã tạo deadline!');
}

public function show(Deadline $deadline)
{
    $tasks = [];
    $start = min($deadline->start_date, $deadline->end_date);
    $end = max($deadline->start_date, $deadline->end_date);

    foreach ($deadline->tasks as $dlTask) {
        $actual = Task::where('title', $dlTask->task_title)
                      ->whereBetween('task_date', [$start, $end])
                      ->sum('progress');

        $tasks[] = [
            'title' => $dlTask->task_title,
            'target' => $dlTask->target_progress,
            'actual' => $actual,
        ];
    }

    return view('deadlines.show', compact('deadline', 'tasks'));
}


public function edit(Deadline $deadline)
{
    $tasks = TaskTitle::pluck('title_name'); // Đúng: lấy từ task_titles
    $selectedTasks = $deadline->tasks->pluck('task_title')->toArray(); // Lấy task liên quan deadline

    return view('deadlines.edit', compact('deadline', 'tasks', 'selectedTasks'));
}


    public function update(Request $request, Deadline $deadline)
{
    // Cập nhật deadline chính
    $taskNames = implode(',', $request->task_titles ?? []);
    

    $deadline->update([
        'start_date'      => $request->start_date,
        'end_date'        => $request->end_date,
        'name'            => $request->name,
        'task_names'      => $taskNames,
        'note'            => $request->note,
    ]);

    // Xóa hết task cũ (simple, nếu cần giữ task cũ thì làm khác)
    $deadline->tasks()->delete();

    // Lưu lại từng task mới
    foreach ($request->task_titles as $index => $title) {

          if (!TaskTitle::where('title_name', $title)->exists()) {
        TaskTitle::create(['title_name' => $title]);
    }
        // Nếu chưa có task thì tạo mới
        // if (!Task::where('title', $title)->exists()) {
        //     Task::create([
        //         'title' => $title,
        //         'user_id' => auth()->id(),
        //         'task_date' => now()->toDateString(),
        //         'shift' => '',
        //         'type' => '',
        //         'supervisor' => '',
        //         'status' => '',
        //         'detail' => '',
        //         'progress' => 0,
        //         'file_link' => '',
        //     ]);
        // }

        $deadline->tasks()->create([
            'task_title'      => $title,
            'target_progress' => $request->target_progresses[$index] ?? 0,
        ]);
    }

    return redirect()->route('deadlines.index')->with('success', 'Đã cập nhật deadline!');
}
    public function destroy(Deadline $deadline)
    {
        $deadline->delete();
        return redirect()->route('deadlines.index')->with('success', 'Đã xoá deadline!');
    }
}
