<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Todo App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nav-link {
            font-weight: 500;
            transition: background-color 0.3s, color 0.3s;
        }
        .nav-link:hover {
            opacity: 0.85;
        }
        .sidebar {
            transition: all 0.3s ease;
        }
        .sidebar.hidden {
            margin-left: -250px;
        }
    </style>
</head>

<body class="bg-light">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<div class="d-flex min-vh-100">
    <!-- Sidebar -->
    <div id="sidebar" class="bg-white shadow-sm p-3 sidebar" style="width: 230px;">
        <!-- Nút menu trong sidebar -->
        <button class="btn btn-outline-secondary mb-3 w-100" onclick="toggleSidebar()">
            ->
        </button>

        <h4 class="mb-4">📋 Menu</h4>
        <ul class="nav flex-column gap-2">
            <li class="nav-item"><a href="/dashboard" class="nav-link bg-primary text-white rounded px-3 py-2">🏠 Dashboard</a></li>
            <li class="nav-item"><a href="/tasks" class="nav-link bg-success text-white rounded px-3 py-2">📅 Hôm nay có gì?</a></li>
            <li class="nav-item"><a href="/plan" class="nav-link bg-warning text-dark rounded px-3 py-2">📝 Lên kế hoạch</a></li>
            <li class="nav-item"><a href="/setup" class="nav-link bg-info text-white rounded px-3 py-2">➕ Tạo mới</a></li>
            <li class="nav-item"><a href="/all" class="nav-link bg-secondary text-white rounded px-3 py-2">📂 Tất cả công việc</a></li>
            <li class="nav-item"><a href="/deadline" class="nav-link bg-danger text-white rounded px-3 py-2">⏰ Deadline</a></li>
            <li class="nav-item"><a href="/export" class="nav-link bg-dark text-white rounded px-3 py-2">📤 Xuất Excel</a></li>
            <li class="nav-item mt-4"><a href="/logout" class="nav-link bg-danger bg-opacity-75 text-white rounded px-3 py-2">🚪 Logout</a></li>
        </ul>
    </div>

    <!-- Nội dung -->
    <div class="p-4 flex-grow-1">
        <!-- Nút menu ngoài -->
        <div id="outsideMenuButton">
            <button class="btn btn-outline-secondary mb-3" onclick="toggleSidebar()">
               <-
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<!-- Script toggle -->
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const outsideMenuButton = document.getElementById('outsideMenuButton');

        if (sidebar.classList.contains('hidden')) {
            sidebar.classList.remove('hidden');
            outsideMenuButton.style.display = 'none';
        } else {
            sidebar.classList.add('hidden');
            outsideMenuButton.style.display = 'block';
        }
    }

    // Load lần đầu → nếu sidebar hiện thì ẩn nút ngoài
    window.onload = function() {
        const sidebar = document.getElementById('sidebar');
        const outsideMenuButton = document.getElementById('outsideMenuButton');

        if (!sidebar.classList.contains('hidden')) {
            outsideMenuButton.style.display = 'none';
        }
    }
</script>
@yield('scripts') //dac biet quan trong , phai co, no gan script trang con vao layout
</body>
</html>
