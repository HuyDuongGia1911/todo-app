<?php

namespace App\Http\Controllers;

use App\Exports\MonthlySummariesExport;
use App\Models\MonthlySummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\Task;
use App\Exports\SingleMonthlySummaryExport;
class MonthlySummaryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return MonthlySummary::where('user_id', Auth::id())
                ->orderBy('month', 'desc')
                ->get();
        }
        return view('summaries.index');
    }
    public function show(MonthlySummary $summary)
{
    $this->authorizeOwner($summary);
    return response()->json($summary);
}

   public function store(Request $request)
{
    $data = $this->validateData($request);
    $data['user_id'] = Auth::id();

    // TÍNH task/thống kê
    $computed = $this->computeTasksForMonth($data['month'], Auth::id());
    $data['tasks_cache'] = $computed['items'];
    $data['stats'] = [
        'total'        => $computed['total'],
        'by_type'      => $computed['by_type'],
        'by_priority'  => $computed['by_priority'],
        'avg_progress' => $computed['avg_progress'],
    ];
    $data['total_tasks'] = $computed['total'];

    $summary = MonthlySummary::create($data);

    return response()->json($summary, 201);
}


    public function update(Request $request, MonthlySummary $summary)
{
    $this->authorizeOwner($summary);
    if ($summary->isLocked()) {
        return response()->json(['message' => 'Báo cáo đã chốt, không thể sửa!'], 423);
    }

    $summary->update(['content' => $request->input('content')]);
    return response()->json($summary);
}


    public function destroy(MonthlySummary $summary)
    {
        $this->authorizeOwner($summary);
        if ($summary->isLocked()) {
            return response()->json(['message' => 'Báo cáo đã chốt, không thể xoá!'], 423);
        }

        $summary->delete();
        return response()->json(['success' => true]);
    }

    public function lock(MonthlySummary $summary)
    {
        $this->authorizeOwner($summary);
        if ($summary->isLocked()) {
            return response()->json(['message' => 'Đã chốt rồi!'], 409);
        }

        $summary->lock();
        return response()->json(['success' => true, 'locked_at' => $summary->locked_at]);
    }
public function exportById(MonthlySummary $summary)
{
    $this->authorizeOwner($summary);
    return Excel::download(new SingleMonthlySummaryExport($summary), 'summary_'.$summary->month.'.xlsx');
}


    private function validateData(Request $request): array
    {
        return $request->validate([
            'month'   => 'required|date_format:Y-m',
            'title'   => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'stats'   => 'nullable|array',
        ]);
    }
    private function buildMonthRange(string $ym): array
{
    $start = Carbon::createFromFormat('Y-m', $ym)->startOfMonth();
    $end   = Carbon::createFromFormat('Y-m', $ym)->endOfMonth();
    return [$start, $end];
}

private function computeTasksForMonth(string $ym, int $userId): array
{
    [$start, $end] = $this->buildMonthRange($ym);

    $tasks = Task::where('user_id', $userId)
        ->whereBetween('task_date', [$start->toDateString(), $end->toDateString()])
        ->orderBy('task_date')
        ->get([
            'id', 'title', 'type', 'priority', 'progress', 'task_date'
        ]);

    // vài thống kê mẫu
    $byType = $tasks->groupBy('type')->map->count();
    $byPriority = $tasks->groupBy('priority')->map->count();
    $avgProgress = round($tasks->avg('progress') ?? 0, 2);

    return [
        'items'       => $tasks->toArray(),
        'total'       => $tasks->count(),
        'by_type'     => $byType,
        'by_priority' => $byPriority,
        'avg_progress'=> $avgProgress,
    ];
}
    public function regenerate(MonthlySummary $summary)
{
    $this->authorizeOwner($summary);

    // Lấy tháng từ summary
    $month = Carbon::parse($summary->month);
    $start = $month->copy()->startOfMonth()->toDateString();
    $end = $month->copy()->endOfMonth()->toDateString();

    // Lấy tất cả task trong tháng
    $tasks = Task::where('user_id', $summary->user_id)
        ->whereBetween('task_date', [$start, $end])
        ->get();

    // Gộp task theo title
    $merged = [];
    foreach ($tasks as $task) {
        $title = $task->title ?? '(Không tên)';
        if (!isset($merged[$title])) {
            $merged[$title] = [
                'title' => $title,
                'progress' => $task->progress ?? 0,
                'dates' => [$task->task_date],
            ];
        } else {
            $merged[$title]['progress'] += $task->progress ?? 0;
            $merged[$title]['dates'][] = $task->task_date;
        }
    }

    // Cập nhật thống kê
    $today = Carbon::today();
    $doneCount = $tasks->where('status', 'Đã hoàn thành')->count();
    $overdueCount = $tasks->filter(fn($t) => $t->status !== 'Đã hoàn thành' && Carbon::parse($t->task_date)->lt($today))->count();
    $pendingCount = $tasks->filter(fn($t) => $t->status !== 'Đã hoàn thành' && Carbon::parse($t->task_date)->gte($today))->count();

    $summary->tasks_cache = array_values($merged);
    $summary->stats = [
        'total'   => $tasks->count(),
        'done'    => $doneCount,
        'pending' => $pendingCount,
        'overdue' => $overdueCount,
    ];

    $summary->save();

    return response()->json($summary);
}
    private function authorizeOwner(MonthlySummary $summary): void
    {
        abort_if($summary->user_id !== Auth::id(), 403);
    }
}
