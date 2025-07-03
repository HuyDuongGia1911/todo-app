

@php
    $user = session()->get('user');
@endphp

@if($user)
    <h1>Welcome, {{ $user->name }}</h1>
@endif
<a href="/logout">Logout</a>
@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif