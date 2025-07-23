<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Shift;
use App\Models\TaskType;
use App\Models\TaskTitle;
use App\Models\Supervisor;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\TasksExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\Kpi;
class TaskController extends Controller
{
   public function dashboard()
{
    $today = Carbon::today();

    $taskToday = Task::whereDate('task_date', $today)->count();

    $taskOverdue = Task::where('task_date', '<', $today)
        ->where('status', '!=', 'Đã hoàn thành')
        ->count();

    $weeklyTasks = Task::whereBetween('task_date', [
        Carbon::now()->startOfWeek(),
        Carbon::now()->endOfWeek()
    ])->count();

    $kpisSoon = Kpi::whereDate('end_date', '<=', $today->copy()->addDays(3))
        ->where('status', '!=', 'Đã hoàn thành')
        ->count();

    return view('dashboard', [
        'taskCount' => Task::count(),
        'userName' => auth()->user()->name,
        'dashboardData' => [
            'taskToday' => $taskToday,
            'taskOverdue' => $taskOverdue,
            'weeklyTasks' => $weeklyTasks,
            'kpisSoon' => $kpisSoon,
        ]
    ]);
}

    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->filled('start_date')) {
            $query->whereDate('task_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('task_date', '<=', $request->end_date);
        }

       $tasks = $query
    ->orderByRaw("FIELD(priority, 'Khẩn cấp', 'Cao', 'Trung bình', 'Thấp')")
    ->orderBy('task_date', 'desc')
    ->get();


        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        return view('tasks.create', [
            'shifts' => Shift::all(),
            'types' => TaskType::all(),
            'titles' => TaskTitle::all(),
            'supervisors' => Supervisor::all(),
            'statuses' => Status::all(),
        ]);
    }

    public function store(Request $request)
    {
        $this->autoCreateMeta($request);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        Task::create($data);

        $redirect = $request->redirect_back ?? route('tasks.index');
        return redirect($redirect)->with('success', 'Đã thêm công việc!');
    }

    public function edit(Task $task)
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
        $this->autoCreateMeta($request);

        $task->update($request->all());

        $redirect = $request->redirect_back ?? route('tasks.index');
        return redirect($redirect)->with('success', 'Đã cập nhật công việc!');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Đã xoá!');
    }
public function export(Request $request)
    {
        $type = $request->query('type', 'all');
        $query = Task::query();

        if ($type === 'filtered') {
             if (!$request->filled('start_date') && !$request->filled('end_date')) {
            return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một ngày bắt đầu hoặc ngày kết thúc để lọc!');
        }
            if ($request->filled('start_date')) {
                $query->whereDate('task_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('task_date', '<=', $request->end_date);
            }
        }

        return Excel::download(new TasksExport($query), 'tasks_' . $type . '.xlsx');
    }
   public function updateStatus(Request $request, Task $task)
{
    
      $status = $request->input('status');
    if (!in_array($status, ['Chưa hoàn thành', 'Đã hoàn thành'])) {
        return response()->json(['error' => 'Trạng thái không hợp lệ!'], 422);
    }

    $task->status = $status;
    $task->save();

    return response()->json(['success' => true]);
}
    private function autoCreateMeta($request)
    {
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
    }
}
