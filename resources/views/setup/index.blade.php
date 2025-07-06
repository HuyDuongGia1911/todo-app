@extends('layouts.app')

@section('content')
<h1 class="mb-4">Tạo mới dữ liệu cho dropdown</h1>

<form action="/setup" method="POST">
    @csrf

    <div class="row">
        <div class="col">
            <label>Ca làm:</label>
            <input name="shift_name" class="form-control">
        </div>
        <div class="col">
            <label>Loại task:</label>
            <input name="type_name" class="form-control">
        </div>
        <div class="col">
            <label>Tên task:</label>
            <input name="title_name" class="form-control">
        </div>
    </div>

    <div class="row mt-3">
        <div class="col">
            <label>Người phụ trách:</label>
            <input name="supervisor_name" class="form-control">
        </div>
        <div class="col">
            <label>Trạng thái:</label>
            <input name="status_name" class="form-control">
        </div>
    </div>

    <button class="btn btn-primary mt-4">Thêm mới</button>
</form>

<hr>

<h4 class="mt-5">Dữ liệu đã có:</h4>

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#shift">Ca làm</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#type">Loại task</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#title">Tên task</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#supervisor">Người phụ trách</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#status">Trạng thái</a>
    </li>
</ul>

<div class="tab-content mt-3">
    <div class="tab-pane fade show active" id="shift">  
        <ul>
            @foreach($shifts as $shift)
                <li>
                    @if(request('edit_shift') == $shift->id)
                        <form method="POST" action="{{ route('shifts.update', $shift->id) }}" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <input type="text" name="shift_name" value="{{ $shift->shift_name }}" class="form-control d-inline-block w-auto">
                            <button class="btn btn-sm btn-success">Lưu</button>
                            <a href="{{ url('/setup#shift') }}" class="btn btn-sm btn-secondary">Hủy</a>
                        </form>
                    @else
                        {{ $shift->shift_name }}
                        <a href="{{ url('/setup?edit_shift=' . $shift->id . '#shift') }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <form method="POST" action="{{ route('shifts.destroy', $shift->id) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Xóa ca này?')" class="btn btn-sm btn-outline-danger">Xóa</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    <div class="tab-pane fade" id="type">
        <ul>
            @foreach($types as $type)
                <li>
                    @if(request('edit_type') == $type->id)
                        <form method="POST" action="{{ route('types.update', $type->id) }}" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <input type="text" name="type_name" value="{{ $type->type_name }}" class="form-control d-inline-block w-auto">
                            <button class="btn btn-sm btn-success">Lưu</button>
                            <a href="{{ url('/setup#type') }}" class="btn btn-sm btn-secondary">Hủy</a>
                        </form>
                    @else
                        {{ $type->type_name }}
                        <a href="{{ url('/setup?edit_type=' . $type->id . '#type') }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <form method="POST" action="{{ route('types.destroy', $type->id) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Xóa loại này?')" class="btn btn-sm btn-outline-danger">Xóa</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    <div class="tab-pane fade" id="title">
        <ul>
            @foreach($titles as $title)
                <li>
                    @if(request('edit_title') == $title->id)
                        <form method="POST" action="{{ route('titles.update', $title->id) }}" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <input type="text" name="title_name" value="{{ $title->title_name }}" class="form-control d-inline-block w-auto">
                            <button class="btn btn-sm btn-success">Lưu</button>
                            <a href="{{ url('/setup#title') }}" class="btn btn-sm btn-secondary">Hủy</a>
                        </form>
                    @else
                        {{ $title->title_name }}
                        <a href="{{ url('/setup?edit_title=' . $title->id . '#title') }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <form method="POST" action="{{ route('titles.destroy', $title->id) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Xóa task này?')" class="btn btn-sm btn-outline-danger">Xóa</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    <div class="tab-pane fade" id="supervisor"> 
        <ul>
            @foreach($supervisors as $supervisor)
                <li>
                    @if(request('edit_supervisor') == $supervisor->id)
                        <form method="POST" action="{{ route('supervisors.update', $supervisor->id) }}" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <input type="text" name="supervisor_name" value="{{ $supervisor->supervisor_name }}" class="form-control d-inline-block w-auto">
                            <button class="btn btn-sm btn-success">Lưu</button>
                            <a href="{{ url('/setup#supervisor') }}" class="btn btn-sm btn-secondary">Hủy</a>
                        </form>
                    @else
                        {{ $supervisor->supervisor_name }}
                        <a href="{{ url('/setup?edit_supervisor=' . $supervisor->id . '#supervisor') }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <form method="POST" action="{{ route('supervisors.destroy', $supervisor->id) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Xóa người này?')" class="btn btn-sm btn-outline-danger">Xóa</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    <div class="tab-pane fade" id="status">
        <ul>
            @foreach($statuses as $status)
                <li>
                    @if(request('edit_status') == $status->id)
                        <form method="POST" action="{{ route('statuses.update', $status->id) }}" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <input type="text" name="status_name" value="{{ $status->status_name }}" class="form-control d-inline-block w-auto">
                            <button class="btn btn-sm btn-success">Lưu</button>
                            <a href="{{ url('/setup#status') }}" class="btn btn-sm btn-secondary">Hủy</a>
                        </form>
                    @else
                        {{ $status->status_name }}
                        <a href="{{ url('/setup?edit_status=' . $status->id . '#status') }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                        <form method="POST" action="{{ route('statuses.destroy', $status->id) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Xóa trạng thái này?')" class="btn btn-sm btn-outline-danger">Xóa</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hash = window.location.hash;
        if (hash) {
            const trigger = document.querySelector(`a[href=\"${hash}\"]`);
            if (trigger) {
                const tab = new bootstrap.Tab(trigger);
                tab.show();
            }
        }
    });
</script>
@endsection
