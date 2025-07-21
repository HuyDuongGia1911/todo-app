@extends('layouts.app')

@section('content')
<h2>Danh sách KPI</h2>

<!-- Filter -->
<form action="{{ route('kpis.index') }}" method="GET" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
    </div>
    <div class="col-auto">
        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
    </div>
    <div class="col-auto">
        <button class="btn btn-primary">Lọc</button>
        <a href="{{ route('kpis.index') }}" class="btn btn-secondary">Xoá lọc</a>
    </div>
</form>

<div class="mb-3">
    <a href="{{ route('kpis.create') }}" class="btn btn-success">Thêm KPI</a>
    <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#exportKPIModal">Xuất Excel</button>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Ngày bắt đầu</th>
            <th>Ngày đến hạn</th>
            <th>Tên Deadline</th>
            <th>Tiến độ</th>
            <th>Chi tiết</th>
            <th>Hành động</th>
            <th>Trạng thái</th>
        </tr>
    </thead>
    <tbody>
    @foreach($kpis as $kpi)
        @php
            $totalTasks = $kpi->tasks->count();
            $completedTasks = 0;

            foreach ($kpi->tasks as $kpiTask) {
                $actual = \App\Models\Task::where('title', $kpiTask->task_title)
                            ->whereBetween('task_date', [$kpi->start_date, $kpi->end_date])
                            ->sum('progress');

                if ($actual >= $kpiTask->target_progress) {
                    $completedTasks++;
                }
            }

            $progressPercent = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        @endphp

        <tr id="kpi-row-{{ $kpi->id }}" class="{{ $kpi->status === 'Đã hoàn thành' ? 'opacity-50' : '' }}">
            <td>{{ $kpi->start_date }}</td>
            <td>{{ $kpi->end_date }}</td>
            <td>{{ $kpi->name }}</td>
            <td>{{ $progressPercent }}%</td>
            <td>
                <a href="{{ route('kpis.show', $kpi->id) }}" class="btn btn-primary btn-sm">Chi tiết</a>
            </td>
            <td>
               <form action="{{ route('kpis.edit', $kpi->id) }}" method="GET" style="display:inline;">
    <button type="submit" class="btn btn-warning btn-sm edit-btn" {{ $kpi->status === 'Đã hoàn thành' ? 'disabled' : '' }}>
        Sửa
    </button>
</form>
                <form action="{{ route('kpis.destroy', $kpi->id) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm delete-btn" onclick="return confirm('Xoá deadline này?')" {{ $kpi->status === 'Đã hoàn thành' ? 'disabled' : '' }}>Xoá</button>
                </form>
            </td>
            <td>
                <label>
                    <input type="radio" name="status_{{ $kpi->id }}" value="Chưa hoàn thành"
                        onchange="updateKPIStatus({{ $kpi->id }}, 'Chưa hoàn thành')"
                        {{ $kpi->status === 'Chưa hoàn thành' ? 'checked' : '' }}>
                    Chưa hoàn thành
                </label><br>
                <label>
                    <input type="radio" name="status_{{ $kpi->id }}" value="Đã hoàn thành"
                        onchange="updateKPIStatus({{ $kpi->id }}, 'Đã hoàn thành')"
                        {{ $kpi->status === 'Đã hoàn thành' ? 'checked' : '' }}>
                    Đã hoàn thành
                </label>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- Modal xuất Excel -->
<div class="modal fade" id="exportKPIModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xuất Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Chọn tùy chọn xuất:</p>
                <a href="{{ route('kpis.export', ['type' => 'all']) }}" class="btn btn-primary w-100 mb-2">Xuất toàn bộ</a>
                <a href="{{ route('kpis.export', ['type' => 'filtered', 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="btn btn-secondary w-100">Xuất bảng hiện tại</a>
            </div>
        </div>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@endsection

@section('scripts')
<script>
function updateKPIStatus(kpiId, statusValue) {
    fetch(`/kpis/${kpiId}/status`, {
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
        const row = document.getElementById(`kpi-row-${kpiId}`);
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
    .catch(() => alert('Lỗi khi cập nhật trạng thái KPI!'));
}
</script>
@endsection
