@extends('layouts.app')

@section('content')
<h1 class="mb-4">Hôm nay có gì?</h1>
<a href="/tasks/create" class="btn btn-success mb-3">Thêm công việc</a>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<table class="table table-striped">
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
    @forelse($tasks as $task)
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
                <a href="/tasks/{{ $task->id }}/edit" class="btn btn-warning btn-sm">Sửa</a>
                <form action="/tasks/{{ $task->id }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xoá task này?')">Xoá</button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="10" class="text-center text-muted">Không có công việc hôm nay.</td>
        </tr>
    @endforelse
    </tbody>
</table>
@endsection
