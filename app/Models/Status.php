<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
   protected $table = 'statuses'; // Khai báo rõ bảng cần dùng

    protected $fillable = ['status_name'];
}
