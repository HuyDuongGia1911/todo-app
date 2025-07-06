@extends('layouts.app')

@section('content')
    <h1 class="mb-4">Dashboard</h1>
    <p>Chào, {{ Auth::user()->name }}</p>
    <p>Bạn đã tạo <strong>{{ $taskCount }}</strong> công việc.</p>
@endsection
