<?php

// app/Exports/SingleMonthlySummaryExport.php
namespace App\Exports;

use App\Models\MonthlySummary; //dữ liệu 
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SingleMonthlySummaryExport implements FromView //Làm cho Excel hiểu rằng dữ liệu sẽ được lấy từ một file Blade (có HTML <table>).
{
    public function __construct(protected MonthlySummary $summary) {} //nhận 1 bản ghi, lưu vào biến summary

    public function view(): View
    {
        return view('exports.summary', [ //Trả về view exports.summary với dữ liệu
            'summary'     => $this->summary,
            'mergedTasks' => $this->summary->tasks_cache ?? [],
        ]);
    }
}
