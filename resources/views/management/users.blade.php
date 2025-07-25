@extends('layouts.app')

@section('content')
<div class="container">
    <div id="users-app"></div>
</div>
@endsection

@push('scripts')
@viteReactRefresh
@vite('resources/js/app.jsx')
@endpush
