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
    $totalActual = 0;
    $totalTarget = 0;

    foreach ($kpi->tasks as $kpiTask) {
        $actual = \App\Models\Task::where('title', $kpiTask->task_title)
                    ->whereBetween('task_date', [$kpi->start_date, $kpi->end_date])
                    ->where('user_id', auth()->id()) // nếu bạn dùng đa người
                    ->sum('progress');

        $target = $kpiTask->target_progress ?? 0;

        $totalActual += $actual;
        $totalTarget += $target;
    }

    $progressPercent = $totalTarget > 0 ? round($totalActual / $totalTarget * 100) : 0;
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
    <div class="toggler">
        <input id="kpi-toggle-{{ $kpi->id }}" type="checkbox"
               onchange="updateKPIStatus({{ $kpi->id }}, this.checked ? 'Đã hoàn thành' : 'Chưa hoàn thành')"
               {{ $kpi->status === 'Đã hoàn thành' ? 'checked' : '' }}>
        <label for="kpi-toggle-{{ $kpi->id }}">
            <svg class="toggler-on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                <polyline class="path check" points="100.2,40.2 51.5,88.8 29.8,67.5"></polyline>
            </svg>
            <svg class="toggler-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                <line class="path line" x1="34.4" y1="34.4" x2="95.8" y2="95.8"></line>
                <line class="path line" x1="95.8" y1="34.4" x2="34.4" y2="95.8"></line>
            </svg>
        </label>
    </div>
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
<style>
/* Switch toggle (Uiverse - mobinkakei) */
.toggler {
  width: 72px;
  margin: auto;
}

.toggler input {
  display: none;
}

.toggler label {
  display: block;
  position: relative;
  width: 72px;
  height: 36px;
  border: 1px solid #d6d6d6;
  border-radius: 36px;
  background: #e4e8e8;
  cursor: pointer;
}

.toggler label::after {
  display: block;
  position: absolute;
  top: 50%;
  left: 25%;
  width: 26px;
  height: 26px;
  background-color: #d7062a;
  content: '';
  border-radius: 100%;
  transform: translate(-50%, -50%);
  transition: 0.25s ease-in-out;
  animation: toggler-size 0.15s ease-out forwards;
}

.toggler input:checked + label::after {
  background-color: #50ac5d;
  left: 75%;
  animation-name: toggler-size2;
}

.toggler label .toggler-on,
.toggler label .toggler-off {
  position: absolute;
  top: 50%;
  left: 25%;
  width: 26px;
  height: 26px;
  transform: translate(-50%, -50%);
  transition: all 0.15s ease-in-out;
  z-index: 2;
}

.toggler .toggler-on,
.toggler .toggler-off {
  opacity: 1;
}

.toggler input:checked + label .toggler-off,
.toggler input:not(:checked) + label .toggler-on {
  width: 0;
  height: 0;
  opacity: 0;
}

.toggler .path {
  fill: none;
  stroke: #fff;
  stroke-width: 7px;
  stroke-linecap: round;
  stroke-miterlimit: 10;
}

@keyframes toggler-size {
  0%, 100% { width: 26px; height: 26px; }
  50% { width: 20px; height: 20px; }
}

@keyframes toggler-size2 {
  0%, 100% { width: 26px; height: 26px; }
  50% { width: 20px; height: 20px; }
}
</style>
@endsection
