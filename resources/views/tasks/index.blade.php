@extends('layouts.app')

@section('content')
<h1>Tất cả công việc</h1>

<!-- Filter -->
<form action="{{ route('tasks.index') }}" method="GET" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
    </div>
    <div class="col-auto">
        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
    </div>
    <div class="col-auto">
        <button class="btn btn-primary">Lọc</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Xoá lọc</a>
    </div>
</form>

<!-- Nút chức năng -->
<div class="mb-3">
    <a href="{{ route('tasks.create') }}" class="btn btn-success">Thêm công việc</a>
    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exportTaskModal">Xuất Excel</button>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Ngày</th>
            <th>Ca</th>
            <th>Loại</th>
            <th>Tên task</th>
            <th>Người phụ trách</th>
            <th>Ưu tiên</th>
            <th>Tiến độ</th>
            <th>Chi tiết</th>
            <th>File</th>
            <th>Hành động</th>
            <th>Trạng thái</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tasks as $task)
        <tr id="task-row-{{ $task->id }}" class="{{ $task->status === 'Đã hoàn thành' ? 'opacity-50' : '' }}">
            <td>{{ $task->task_date }}</td>
            <td>{{ $task->shift }}</td>
            <td>{{ $task->type }}</td>
            <td>{{ $task->title }}</td>
            <td>{{ $task->supervisor }}</td>
            <td>{{ $task->priority }}</td>
            <td>{{ $task->progress }}</td>
            <td>{{ $task->detail }}</td>
            <td>
                @if($task->file_link)
                    @foreach(explode(',', $task->file_link) as $link)
                        <a href="{{ trim($link) }}" target="_blank">Link</a><br>
                    @endforeach
                @else
                    -
                @endif
            </td>
            <td>
                <form action="{{ route('tasks.edit', $task->id) }}" method="GET" style="display:inline;">
    <button type="submit" class="btn btn-warning btn-sm edit-btn" {{ $task->status === 'Đã hoàn thành' ? 'disabled' : '' }}>
        Sửa
    </button>
</form>
                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm delete-btn" onclick="return confirm('Xoá task này?')" {{ $task->status === 'Đã hoàn thành' ? 'disabled' : '' }}>Xoá</button>
                </form>
            </td>
            <td>
                <label>
                    <input type="radio" name="status_{{ $task->id }}" value="Chưa hoàn thành"
                           onchange="updateStatus({{ $task->id }}, 'Chưa hoàn thành')"
                           {{ $task->status === 'Chưa hoàn thành' ? 'checked' : '' }}>
                    Chưa hoàn thành
                </label><br>
                <label>
                    <input type="radio" name="status_{{ $task->id }}" value="Đã hoàn thành"
                           onchange="updateStatus({{ $task->id }}, 'Đã hoàn thành')"
                           {{ $task->status === 'Đã hoàn thành' ? 'checked' : '' }}>
                    Đã hoàn thành
                </label>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@section('scripts')
<script>
function updateStatus(taskId, statusValue) {
    fetch(`/tasks/${taskId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: statusValue })
    })
    .then(res => {
        if (!res.ok) throw new Error();
        return res.json();
    })
    .then(() => {
        const row = document.getElementById(`task-row-${taskId}`);
        const editBtn = row.querySelector('.edit-btn');
        const deleteBtn = row.querySelector('.delete-btn');

        if (statusValue === 'Đã hoàn thành') {
            row.classList.add('opacity-50');
            editBtn.disabled = true;
            deleteBtn.disabled = true;
        } else {
            row.classList.remove('opacity-50');
            editBtn.disabled = false;
            deleteBtn.disabled = false;
        }
    })
    .catch(() => alert('Lỗi khi cập nhật trạng thái!'));
}
</script>
@endsection




@section('scripts')
<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Reusable dropdown
function setupDropdown(selectId, apiUrl, fieldName, modalTitle) {
    const select = $('#' + selectId);

    async function loadOptions() {
        try {
            const res = await fetch(apiUrl);
            const data = await res.json();
            select.empty();
            data.forEach(item => select.append(new Option(item[fieldName], item[fieldName])));
            select.select2({ tags: true, placeholder: 'Chọn hoặc nhập...', width: '100%' });
        } catch { alert('Lỗi khi load dữ liệu!'); }
    }

    select.on('select2:select', async (e) => {
        const value = e.params.data.id;
        const exists = Array.from(select[0].options).some(opt => opt.value === value);
        if (!exists && confirm(`Thêm mới: "${value}"?`)) {
            await fetch(apiUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ [fieldName]: value })
            });
            await loadOptions();
            alert('Đã thêm!');
        }
    });

    loadOptions();
}

// Open dropdown management modal
async function openManageModal(apiUrl, fieldName, title) {
    document.getElementById('manageModalTitle').textContent = title;
    const body = document.getElementById('manageModalBody');
    body.innerHTML = 'Đang tải...';

    try {
        const res = await fetch(apiUrl);
        const data = await res.json();
        body.innerHTML = '';
        data.forEach(item => {
            const div = document.createElement('div');
            div.classList.add('d-flex', 'align-items-center', 'mb-2');
            div.innerHTML = `
                <input type="text" class="form-control me-2" value="${item[fieldName]}" onchange="updateItem('${apiUrl}', ${item.id}, '${fieldName}', this.value)">
                <button class="btn btn-danger btn-sm" onclick="deleteItem('${apiUrl}', ${item.id})">Xoá</button>
            `;
            body.appendChild(div);
        });
    } catch { body.innerHTML = 'Lỗi khi tải!'; }

    new bootstrap.Modal(document.getElementById('manageModal')).show();
}

// Update
async function updateItem(apiUrl, id, fieldName, value) {
    await fetch(`${apiUrl}/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ [fieldName]: value })
    });
    alert('Đã cập nhật!');
}

// Delete
async function deleteItem(apiUrl, id) {
    if (!confirm('Xóa mục này?')) return;
    await fetch(`${apiUrl}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
    alert('Đã xoá!');
    document.querySelector('.modal.show')?.querySelector('.btn-close')?.click();
}

// Init (nếu có dropdown thì gọi tại đây)
// setupDropdown('shift-select', '/api/shifts', 'shift_name', 'Quản lý Ca Làm');
function updateStatus(taskId, statusValue) {
    console.log("Sending status update:", taskId, statusValue);

    fetch(`/tasks/${taskId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: statusValue })
    })
    .then(res => {
        if (!res.ok) throw new Error('Validation failed');
        return res.json();
    })
    .then(data => {
        const row = document.getElementById(`task-row-${taskId}`);
        const editBtn = row.querySelector('.edit-btn');
        const deleteBtn = row.querySelector('.delete-btn');

        if (statusValue === 'Đã hoàn thành') {
            row.classList.add('opacity-50');
            editBtn.disabled = true;
            deleteBtn.disabled = true;
        } else {
            row.classList.remove('opacity-50');
            editBtn.disabled = false;
            deleteBtn.disabled = false;
        }
    })
    .catch((e) => {
        console.error(e);
        alert('Lỗi khi cập nhật trạng thái!');
    });
}

</script>
@endsection
