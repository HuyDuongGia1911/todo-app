@extends('layouts.app')
@section('content')
<h1>Tất cả công việc</h1>

<!-- Filter -->
<form action="{{ route('all.index') }}" method="GET" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Từ ngày">
    </div>
    <div class="col-auto">
        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="Đến ngày">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Lọc</button>
        <a href="{{ route('all.index') }}" class="btn btn-secondary">Xoá lọc</a>
    </div>
</form>

<a href="{{ route('all.create') }}" class="btn btn-success mb-3">Thêm công việc</a>

<table class="table">
    <thead>
        <tr>
            <th>Ngày</th>
            <th>Ca</th>
            <th>Loại</th>
            <th>Tên task</th>
            <th>Người phụ trách</th>
            <th>Trạng thái</th>
            <th>Tiến độ</th>
            <th>Chi tiết</th>
            <th>File</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
    @foreach($tasks as $task)
        <tr>
            <td>{{ $task->task_date }}</td>
            <td>{{ $task->shift }}</td>
            <td>{{ $task->type }}</td>
            <td>{{ $task->title }}</td>
            <td>{{ $task->supervisor }}</td>
            <td>{{ $task->status }}</td>
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
                <a href="{{ route('all.edit', $task->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                <form action="{{ route('all.destroy', $task->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xoá task này?')">Xoá</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
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

</script>

<!-- Modal quản lý -->
<div class="modal fade" id="manageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageModalTitle">Quản lý</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="manageModalBody">
                <!-- Nội dung sẽ load qua JS -->
            </div>
        </div>
    </div>
</div>
@endsection
