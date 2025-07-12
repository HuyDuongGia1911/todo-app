@extends('layouts.app')
@section('content')

<h2>Danh sách Deadline</h2>
<a href="{{ route('deadlines.create') }}" class="btn btn-success mb-3">Thêm Deadline</a>

<table class="table">
    <thead>
        <tr>
            <th>Ngày bắt đầu</th>
            <th>Ngày đến hạn</th>
            <th>Tên Deadline</th>
            <th>Tiến độ</th>
            <th>Chi tiết</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
    @foreach($deadlines as $dl)
        <tr>
            <td>{{ $dl->start_date }}</td>
            <td>{{ $dl->end_date }}</td>
            <td>{{ $dl->name }}</td>
            <td>
                <a href="{{ route('deadlines.show', $dl->id) }}" class="btn btn-info btn-sm">Xem tiến độ</a>
            </td>
            <td>
                <a href="{{ route('deadlines.show', $dl->id) }}" class="btn btn-primary btn-sm">Chi tiết</a>
            </td>
            <td>
                <a href="{{ route('deadlines.edit', $dl->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                <form action="{{ route('deadlines.destroy', $dl->id) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Xoá deadline này?')">Xoá</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection
