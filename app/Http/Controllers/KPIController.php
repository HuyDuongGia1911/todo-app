<?php
namespace App\Http\Controllers;

use App\Models\KPI;
use App\Models\KPITask;
use App\Models\Task;
use App\Models\TaskTitle;
use Illuminate\Http\Request;
use App\Exports\KPIsExport;
use Maatwebsite\Excel\Facades\Excel;
class KPIController extends Controller
{
    public function index(Request $request)
    {
        $query = KPI::query();

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        $kpis = $query->orderBy('end_date')->get();
        return view('kpis.index', compact('kpis'));
    }

    public function create()
    {
        $tasks = TaskTitle::pluck('title_name');
        return view('kpis.create', compact('tasks'));
    }

    public function store(Request $request)
    {
        $taskNames = implode(',', $request->task_titles ?? []);

        $kpi = KPI::create([
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'name'   => $request->name,
            'task_names' => $taskNames,
            'note'       => $request->note,
        ]);

        foreach ($request->task_titles as $index => $title) {
            if (!TaskTitle::where('title_name', $title)->exists()) {
                TaskTitle::create(['title_name' => $title]);
            }

            $kpi->tasks()->create([
                'task_title' => $title,
                'target_progress' => $request->target_progresses[$index] ?? 0,
            ]);
        }

        return redirect()->route('kpis.index')->with('success', 'Đã tạo KPI!');
    }

    public function show(KPI $kpi)
    {
        $tasks = [];
        $start = min($kpi->start_date, $kpi->end_date);
        $end = max($kpi->start_date, $kpi->end_date);

        foreach ($kpi->tasks as $dlTask) {
            $actual = Task::where('title', $dlTask->task_title)
                          ->whereBetween('task_date', [$start, $end])
                          ->sum('progress');

            $tasks[] = [
                'title' => $dlTask->task_title,
                'target' => $dlTask->target_progress,
                'actual' => $actual,
            ];
        }

        return view('kpis.show', compact('kpi', 'tasks'));
    }

    public function edit(KPI $kpi)
    {
        $tasks = TaskTitle::pluck('title_name');
        $selectedTasks = $kpi->tasks->pluck('task_title')->toArray();

        return view('kpis.edit', compact('kpi', 'tasks', 'selectedTasks'));
    }

    public function update(Request $request, KPI $kpi)
    {
        $taskNames = implode(',', $request->task_titles ?? []);

        $kpi->update([
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'name'   => $request->name,
            'task_names' => $taskNames,
            'note'       => $request->note,
        ]);

        $kpi->tasks()->delete();

        foreach ($request->task_titles as $index => $title) {
            if (!TaskTitle::where('title_name', $title)->exists()) {
                TaskTitle::create(['title_name' => $title]);
            }

            $kpi->tasks()->create([
                'task_title' => $title,
                'target_progress' => $request->target_progresses[$index] ?? 0,
            ]);
        }

        return redirect()->route('kpis.index')->with('success', 'Đã cập nhật KPI!');
    }

    public function destroy(KPI $kpi)
    {
        $kpi->delete();
        return redirect()->route('kpis.index')->with('success', 'Đã xoá KPI!');
    }
    public function updateStatus(Request $request, KPI $kpi)
{
    $validated = $request->validate([
        'status' => 'required|string|in:Chưa hoàn thành,Đã hoàn thành'
    ]);

    $kpi->status = $validated['status'];
    $kpi->save();

    return response()->json(['success' => true]);
}

    public function export(Request $request)
{
    $type = $request->query('type', 'all');
    $query = KPI::query();

    if ($type === 'filtered') {
        if (!$request->filled('start_date') && !$request->filled('end_date')) {
            return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một ngày bắt đầu hoặc ngày kết thúc để lọc!');
        }
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }
    }

    return Excel::download(new KPIsExport($query), 'kpis_' . $type . '.xlsx');
}
  
}
