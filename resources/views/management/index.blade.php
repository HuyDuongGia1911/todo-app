@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Quản lý hệ thống</h1>
    <div id="management-app"></div> {{-- React sẽ mount vào đây --}}
</div>
@endsection

@push('scripts')
    @viteReactRefresh
    @vite('resources/js/pages/ManagementIndex.jsx')
@endpush
