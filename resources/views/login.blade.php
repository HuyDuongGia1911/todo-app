<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- MDB CSS -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.0.0/mdb.min.css"
        rel="stylesheet" />

    <!-- Font Awesome (for social icons) -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        rel="stylesheet" />

    <style>
        body {
            background-color: #f5f5f5;
        }

        .container {
            margin-top: 100px;
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center">
        <div class="card p-4 shadow" style="width: 400px;">
            <h3 class="text-center mb-4">Login form</h3>

            @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="/login">
                @csrf

                <!-- Email input -->
                <!-- <div data-mdb-input-init class="form-outline mb-4">
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required />
                    <label class="form-label" for="email">Email address</label>
                </div> -->
                <div class="mb-4">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" name="email" id="email" class="form-control" required />
                </div>
                <!-- Password input -->
                <!-- <div data-mdb-input-init class="form-outline mb-4">
                    <input type="password" name="password" id="password" class="form-control" required />
                    <label class="form-label" for="password">Password</label>
                </div> -->
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required />
                </div>

                <!-- Remember me and forgot -->
                <div class="row mb-4">
                    <div class="col d-flex justify-content-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" />
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>

                    <div class="col text-end">
                        <a href="#">Forgot password?</a>
                    </div>
                </div>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary btn-block mb-4">Sign in</button>

                <!-- Register link -->
                <div class="text-center">
                    <p>Not a member? <a href="/register">Register</a></p>
                    <p>or sign up with:</p>

                    <button type="button" class="btn btn-secondary btn-floating mx-1">
                        <i class="fab fa-facebook-f"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-floating mx-1">
                        <i class="fab fa-google"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-floating mx-1">
                        <i class="fab fa-twitter"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-floating mx-1">
                        <i class="fab fa-github"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MDB JS (CDN) -->
    <script
        type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.0.0/mdb.min.js"></script>

</body>

</html>