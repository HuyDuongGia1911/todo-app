@extends('layouts.app')

@section('content')
<h2>📤 Xuất Excel</h2>

<div class="mb-3">
    <a href="{{ route('tasks.export') }}" class="btn btn-success">Xuất tất cả Tasks</a>
</div>

<div class="alert alert-info">
    File Excel sẽ bao gồm toàn bộ danh sách tasks hiện có.
</div>
@endsection
