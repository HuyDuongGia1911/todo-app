<?php

namespace App\Exports;

use App\Models\MonthlySummary;
use App\Models\KPI;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class SingleMonthlySummaryExport implements FromView
{
    public function __construct(protected MonthlySummary $summary) {}

    public function view(): View
    {
        $summary = $this->summary;

        // Dải ngày của tháng đang export
        $month = Carbon::createFromFormat('Y-m', $summary->month);
        $start = $month->copy()->startOfMonth()->toDateString();
        $end   = $month->copy()->endOfMonth()->toDateString();

        // Lấy KPI của user trong tháng (đè logic giống controller show())
        $kpis = KPI::with('tasks')
            ->where('user_id', $summary->user_id)
            ->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start)
            ->get();

        // Chuẩn hóa tasks_cache (nếu null)
        $tasksCache = collect($summary->tasks_cache ?? []);

        // Build các dòng đánh giá KPI (giống web)
        // Mỗi task trong KPI => 1 dòng: [kpi_name, task_title, time_range, target, result, percent, note]
        $kpiRows = [];

        foreach ($kpis as $kpi) {
            foreach ($kpi->tasks as $kpiTask) {
                $title = $kpiTask->task_title;

                // Tìm các entry trong tasks_cache trùng title
                $related = $tasksCache->filter(function ($t) use ($title) {
                    return isset($t['title']) && $t['title'] === $title;
                });

                // Gom ngày và tiến độ thực tế
                $allDates = [];
                $actual   = 0;

                foreach ($related as $item) {
                    $actual += floatval($item['progress'] ?? 0);
                    $dates = $item['dates'] ?? [];
                    foreach ($dates as $d) {
                        if (!empty($d)) $allDates[] = $d;
                    }
                }

                sort($allDates);
                $timeRange = count($allDates) > 0
                    ? ($allDates[0] . ' - ' . end($allDates))
                    : '';

                $target  = floatval($kpiTask->target_progress ?? 0);
                $percent = $target > 0 ? round(($actual / $target) * 100) : 0;
                $note    = $percent >= 100 ? 'Đạt' : 'Không đạt';

                $kpiRows[] = [
                    'kpi_name'   => $kpi->name,
                    'task_title' => $title,
                    'time_range' => $timeRange,
                    'target'     => $target,
                    'result'     => $actual,
                    'percent'    => $percent,
                    'note'       => $note,
                ];
            }
        }

        return view('exports.summary', [
            'summary'     => $summary,
            'mergedTasks' => $summary->tasks_cache ?? [],
            // dữ liệu mới để render phần ĐÁNH GIÁ KPI
            'kpiRows'     => $kpiRows,
        ]);
    }
}
