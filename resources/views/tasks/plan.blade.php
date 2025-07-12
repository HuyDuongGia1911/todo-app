@extends('layouts.app')

@section('content')
<h1 class="mb-4">Lên kế hoạch công việc</h1>

<form method="POST" action="/plan">
    @csrf

    <div class="mb-3">
        <label>Ngày thực hiện:</label>
        <input type="date" name="task_date" class="form-control">
    </div>

    @foreach ([
        ['name' => 'shift', 'label' => 'Ca', 'data' => $shifts],
        ['name' => 'type', 'label' => 'Loại', 'data' => $types],
        ['name' => 'title', 'label' => 'Tên task', 'data' => $titles],
        ['name' => 'supervisor', 'label' => 'Người phụ trách', 'data' => $supervisors],
        ['name' => 'status', 'label' => 'Trạng thái', 'data' => $statuses],
    ] as $dropdown)
        <div class="mb-3">
            <label>{{ $dropdown['label'] }}:</label>
            <select name="{{ $dropdown['name'] }}" class="form-control select2" id="{{ $dropdown['name'] }}-select">
                @foreach($dropdown['data'] as $item)
                    <option value="{{ $item[ $dropdown['name'].'_name' ] }}">{{ $item[ $dropdown['name'].'_name' ] }}</option>
                @endforeach
            </select>
        </div>
    @endforeach

    <button type="submit" class="btn btn-primary">Lên kế hoạch</button>
</form>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({
        tags: true,
        placeholder: "Chọn hoặc nhập...",
        width: '100%'
    });
});
</script>
@endsection
