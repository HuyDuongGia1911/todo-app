@extends('layouts.app')
@section('content')
<h2>Chi tiết Deadline: {{ $deadline->name }}</h2>

<ul>
    <li><strong>Ngày bắt đầu:</strong> {{ $deadline->start_date }}</li>
    <li><strong>Ngày đến hạn:</strong> {{ $deadline->end_date }}</li>
    <li><strong>Ghi chú:</strong> {{ $deadline->note ?? '-' }}</li>
</ul>

<h4>Công việc liên quan:</h4>
<table class="table">
    <thead>
        <tr><th>Tên công việc</th><th>Tiến độ thực tế</th><th>Mục tiêu</th></tr>
    </thead>
    <tbody>
        @foreach($tasks as $task)
            <tr>
                <td>{{ $task['title'] }}</td>
                <td>{{ $task['actual'] }}</td>
                <td>{{ $task['target'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
