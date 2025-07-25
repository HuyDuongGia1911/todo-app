@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Báo cáo tổng kết</h1>
    <div id="summary-app"></div> {{-- React mount tại đây --}}
</div>
@endsection

@push('scripts')
    @viteReactRefresh
    @vite('resources/js/pages/SummaryIndex.jsx')
@endpush
