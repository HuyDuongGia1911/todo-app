<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deadline extends Model
{
    protected $fillable = ['start_date', 'end_date', 'name', 'task_names', 'target_progress', 'note'];

    // Helper để xử lý danh sách task (nếu lưu json hoặc csv)
    public function getTaskNamesArray()
    {
        return explode(',', $this->task_names);
    }
    public function tasks()
{
    return $this->hasMany(DeadlineTask::class);
}
}

