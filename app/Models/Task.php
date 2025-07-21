<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

   protected $fillable = [
    'user_id',
    'task_date',
    'shift',
    'type',
    'title',
    'supervisor',
    'status',
    'priority', 
    'progress',
    'file_link',
];

public function shift()
{
    return $this->belongsTo(Shift::class, 'shift');
}
public function type()
{
    return $this->belongsTo(TaskType::class, 'type');
}
public function title()
{
    return $this->belongsTo(TaskTitle::class, 'title');
}
public function supervisor()
{
    return $this->belongsTo(Supervisor::class, 'supervisor');
}
public function status()
{
    return $this->belongsTo(Status::class, 'status');
}

}
