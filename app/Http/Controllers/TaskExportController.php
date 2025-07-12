<?php
namespace App\Http\Controllers;

use App\Exports\TasksExport;
use Maatwebsite\Excel\Facades\Excel;

class TaskExportController extends Controller
{   
    // Trang hiển thị nút Export
    public function showExport()
    {
        return view('export.index');  // Đổi đúng view export/index.blade.php
    }
    public function export()
    {
        return Excel::download(new TasksExport, 'tasks.xlsx');
    }
    
}
