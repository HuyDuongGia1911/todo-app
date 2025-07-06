@extends('layouts.app')

@section('content')
<h1 class="mb-4">Lên kế hoạch công việc</h1>

<form method="POST" action="/plan">
    @csrf

    <div class="mb-3">
        <label>Ngày thực hiện:</label>
        <input type="date" name="task_date" class="form-control">
    </div>

    <div class="mb-3">
        <label>Ca:</label>
        <select name="shift" class="form-control">
            @foreach($shifts as $shift)
                <option value="{{ $shift->shift_name }}">{{ $shift->shift_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Loại:</label>
        <select name="type" class="form-control">
            @foreach($types as $type)
                <option value="{{ $type->type_name }}">{{ $type->type_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Tên task:</label>
        <select name="title" class="form-control">
            @foreach($titles as $title)
                <option value="{{ $title->title_name }}">{{ $title->title_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Người phụ trách:</label>
        <select name="supervisor" class="form-control">
            @foreach($supervisors as $supervisor)
                <option value="{{ $supervisor->supervisor_name }}">{{ $supervisor->supervisor_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Trạng thái:</label>
        <select name="status" class="form-control">
            @foreach($statuses as $status)
                <option value="{{ $status->status_name }}">{{ $status->status_name }}</option>
            @endforeach
        </select>
    </div>



    <button type="submit" class="btn btn-primary">Lên kế hoạch</button>
</form>
@endsection
