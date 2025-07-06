<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\TaskType;
use App\Models\TaskTitle;
use App\Models\Supervisor;
use App\Models\Status;
use App\Models\Progress;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    public function index()
    {
        return view('setup.index', [
            'shifts' => Shift::all(),
            'types' => TaskType::all(),
            'titles' => TaskTitle::all(),
            'supervisors' => Supervisor::all(),
            'statuses' => Status::all(),
            'progresses' => Progress::all(),
        ]);
    }

    public function store(Request $request)
    {
        if ($request->filled('shift_name')) Shift::create(['shift_name' => $request->shift_name]);
        if ($request->filled('type_name')) TaskType::create(['type_name' => $request->type_name]);
        if ($request->filled('title_name')) TaskTitle::create(['title_name' => $request->title_name]);
        if ($request->filled('supervisor_name')) Supervisor::create(['supervisor_name' => $request->supervisor_name]);
        if ($request->filled('status_name')) Status::create(['status_name' => $request->status_name]);
        if ($request->filled('progress_value')) Progress::create(['progress_value' => $request->progress_value]);

        return redirect('/setup')->with('success', 'Thêm thành công!');
    }
}

