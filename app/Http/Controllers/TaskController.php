<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;
use App\Models\TaskType;
use App\Models\TaskTitle;
use App\Models\Supervisor;
use App\Models\Status;
use App\Models\Progress;
class TaskController extends Controller
{
    public function dashboard()
    {
        $taskCount = Task::where('user_id', Auth::id())->count();
        return view('dashboard', compact('taskCount'));
    }

    public function index() //lay danh sach theo homnay, trave task
    {
        $tasks = Task::whereDate('task_date', now()->toDateString())->get();
        return view('tasks.index', compact('tasks'));
    }

    public function create() //lay toan bo danh sach cua cac bang, tra ve view lua chon
{
    return view('tasks.create', [
        'shifts' => Shift::all(), //lay tat ca du lieu
        'types' => TaskType::all(),
        'titles' => TaskTitle::all(),
        'supervisors' => Supervisor::all(),
        'statuses' => Status::all(),
        'progresses' => Progress::all(),
    ]);
}


    public function store(Request $request)//lay du lieu nguoi nhap qua request sau do tao du lieu moi
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
        
        Task::create([
            'user_id'    => Auth::id(),
            'task_date'  => $request->task_date,
            'shift'      => $request->shift,
            'type'       => $request->type,
            'title'      => $request->title,
            'supervisor' => $request->supervisor,
            'status'     => $request->status,
            'detail'     => $request->detail,
            'progress'   => $request->progress,
            'file_link'  => $request->file_link,
        ]);
         $redirect = $request->redirect_back ?? route('tasks.index');
        return redirect($redirect)->with('success', 'Đã thêm công việc!');
    }

    public function edit(Task $task)//lay task theo id, tra ve view
{
    return view('tasks.edit', [
        'task' => $task,
        'shifts' => Shift::all(),
        'types' => TaskType::all(),
        'titles' => TaskTitle::all(),
        'supervisors' => Supervisor::all(),
        'statuses' => Status::all(),
    ]);
}


  public function update(Request $request, Task $task)
{
    // Thêm Shift nếu chưa có
    if (!empty($request->shift) && !Shift::where('shift_name', $request->shift)->exists()) {
        Shift::create(['shift_name' => $request->shift]);
    }

    if (!empty($request->type) && !TaskType::where('type_name', $request->type)->exists()) {
        TaskType::create(['type_name' => $request->type]);
    }

    if (!empty($request->title) && !TaskTitle::where('title_name', $request->title)->exists()) {
        TaskTitle::create(['title_name' => $request->title]);
    }

    if (!empty($request->supervisor) && !Supervisor::where('supervisor_name', $request->supervisor)->exists()) {
        Supervisor::create(['supervisor_name' => $request->supervisor]);
    }

    if (!empty($request->status) && !Status::where('status_name', $request->status)->exists()) {
        Status::create(['status_name' => $request->status]);
    }

    // Update task → dùng text trực tiếp
    $task->update([
        'task_date'  => $request->task_date,
        'shift'      => $request->shift,
        'type'       => $request->type,
        'title'      => $request->title,
        'supervisor' => $request->supervisor,
        'status'     => $request->status,
        'detail'     => $request->detail,
        'progress'   => $request->progress,
        'file_link'  => $request->file_link,
    ]);

   $redirect = $request->redirect_back ?? route('tasks.index');
        return redirect($redirect)->with('success', 'Đã cập nhật công việc!');
}



    public function destroy(Task $task)//lay task theo id xong xoa
    {
        $task->delete();
        return redirect()->route('tasks.index');
    }

    // phan nay code sau
   public function plan()
{
    // return view('tasks.plan');
     return view('tasks.plan', [
        'shifts' => Shift::all(),
        'types' => TaskType::all(),
        'titles' => TaskTitle::all(),
        'supervisors' => Supervisor::all(),
        'statuses' => Status::all(),
    ]);
}
    public function storePlan(Request $request)
{
    //luu truc tiep ten
    Task::create([
        'user_id'    => Auth::id(),
        'task_date'  => $request->task_date,
        'shift'      => $request->shift,
        'type'       => $request->type,
        'title'      => $request->title,
        'supervisor' => $request->supervisor,
        'status'     => $request->status,
        'detail'     => $request->detail,
         'progress'   => 0,
        'file_link'  => $request->file_link,
    ]);

    return redirect('/plan')->with('success', 'Đã lên kế hoạch');
}
   public function all(Request $request)
    {
        $query = Task::query();

        if ($request->filled('start_date')) {
            $query->whereDate('task_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('task_date', '<=', $request->end_date);
        }

        $tasks = $query->orderBy('task_date', 'desc')->get();

        return view('tasks.all', compact('tasks'));
    }

    public function deadline() {}
    public function export() {}
}
