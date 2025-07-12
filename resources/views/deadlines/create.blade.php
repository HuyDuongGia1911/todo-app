@extends('layouts.app')

@section('content')
<h2>Thêm Deadline mới</h2>

<form action="{{ route('deadlines.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Ngày bắt đầu</label>
        <input type="date" name="start_date" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Ngày đến hạn</label>
        <input type="date" name="end_date" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Tên Deadline</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <!-- Danh sách task -->
    <div id="task-list">
       <!-- Để trống, không render sẵn -->
    </div>

    <button type="button" class="btn btn-secondary mb-3" onclick="addTask()">+ Thêm công việc</button>

    <div class="mb-3">
        <label>Ghi chú</label>
        <textarea name="note" class="form-control" placeholder="Ghi chú (nếu có)"></textarea>
    </div>

    <button class="btn btn-primary">Lưu Deadline</button>
</form>

<!-- Template task ẩn -->
<div id="task-template" style="display:none;">
    <div class="task-item row mb-2">
        <div class="col">
            <select name="task_titles[]" class="form-control select2" required>
                <option></option>
                @foreach($tasks as $taskTitle)
                    <option value="{{ $taskTitle }}">{{ $taskTitle }}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <input type="number" name="target_progresses[]" class="form-control" placeholder="Tiến độ mục tiêu" required>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-danger" onclick="removeTask(this)">X</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
function initSelect(container = document) {
    $(container).find('.select2').select2({
        placeholder: 'Chọn công việc',
        tags: true, // Cho phép thêm mới
        allowClear: true,
        width: '100%'
    });
}




function addTask() {
    const template = document.getElementById('task-template');
    const clone = template.querySelector('.task-item').cloneNode(true);

    document.getElementById('task-list').appendChild(clone);

    initSelect(clone);
}
$(document).ready(() => {
  
     addTask(); 
});
function removeTask(el) {
    el.closest('.task-item').remove();
}
</script>
@endsection
