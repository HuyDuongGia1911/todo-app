<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = 'progresses'; // Khai báo rõ bảng cần dùng

    protected $fillable = ['progress_value'];
}
