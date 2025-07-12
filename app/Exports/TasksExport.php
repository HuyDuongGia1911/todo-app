<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TasksExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Task::select('id', 'title', 'user_id', 'task_date', 'shift', 'type', 'supervisor', 'status', 'detail', 'progress', 'file_link', 'created_at', 'updated_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'User ID',
            'Task Date',
            'Shift',
            'Type',
            'Supervisor',
            'Status',
            'Detail',
            'Progress',
            'File Link',
            'Created At',
            'Updated At',
        ];
    }
}
