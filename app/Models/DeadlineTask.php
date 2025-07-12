<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeadlineTask extends Model
{
    protected $fillable = ['deadline_id', 'task_title', 'target_progress'];

    public function deadline()
    {
        return $this->belongsTo(Deadline::class);
    }
}
