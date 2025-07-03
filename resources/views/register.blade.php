<h2>Register</h2>
<form method="POST" action="/register">
    @csrf
    <input name="name" placeholder="Name"><br>
    <input name="email" placeholder="Email"><br>
    <input name="password" type="password" placeholder="Password"><br>
    <button type="submit">Register</button>
</form>
@if(session('error'))
    <p style="color: red">{{ session('error') }}</p>
@endif
<p>Already have an account? <a href="/login">Login here</a></p>