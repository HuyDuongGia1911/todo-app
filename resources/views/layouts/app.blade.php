<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Todo App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + Icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.jsx'])

    <style>
        
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            transition: margin-left 0.3s;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
        }
        .sidebar .nav-link {
    color: #f8f9fa; /* gần trắng hơn trắng xám mặc định */
    font-weight: 600; /* đậm hơn bình thường */
    font-size: 15px; /* tùy chỉnh kích thước chữ */
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    border-radius: 4px;
    transition: all 0.2s ease-in-out;
}

.sidebar .nav-link i {
    font-size: 1.1rem; /* tăng icon */
    color: #dee2e6; /* sáng hơn xíu */
}
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            font-weight: bold;
        }

        .sidebar .menu-icon {
            margin-right: 8px;
        }

        .sidebar.hidden {
            margin-left: -250px;
        }

        .sidebar .nav-link:hover {
            background-color: #495057;
            color: white !important;
            /* ⚠️ Ghi đè màu chữ xanh mặc định */
        }
       .sidebar .nav-link.active:hover {
    background-color: #0d6efd;
    font-weight: 700;
    color: white !important;
}


    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@stack('scripts')
<body>
    <div class="d-flex min-vh-100">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar d-flex flex-column p-3">
            <button class="btn btn-outline-light mb-3" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>

            <div class="text-center mb-4">
                <i class="bi bi-check2-square fs-3"></i>
                <div class="fw-bold mt-2">TODO APP</div>
            </div>

            <ul class="nav nav-pills flex-column gap-2">
    <li>
        <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door-fill menu-icon"></i> Trang chủ
        </a>
    </li>
    <li>
        <a href="/tasks" class="nav-link {{ request()->is('tasks*') ? 'active' : '' }}">
            <i class="bi bi-journal-text menu-icon"></i> Công việc
        </a>
    </li>
    <li>
        <a href="/kpis" class="nav-link {{ request()->is('kpis*') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 menu-icon"></i> KPI
        </a>
    </li>
    <li>
        <a href="/summaries" class="nav-link {{ request()->is('summaries*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check"></i> Báo cáo
        </a>
    </li>

    {{-- Hiển thị riêng cho admin --}}
   @auth
    @if(Auth::user()->is_admin)
        <li><a href="{{ route('management') }}" class="nav-link {{ request()->is('management') ? 'active' : '' }}">
            <i class="bi bi-gear-fill menu-icon"></i> Quản lý
        </a></li>
    @endif
@endauth


    <li class="mt-4">
        <a href="/logout" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right menu-icon"></i> Logout
        </a>
    </li>
</ul>

        </div>

        <!-- Content -->
        <div class="flex-grow-1 p-4">
            <!-- Nút mở lại sidebar -->
            <div id="outsideMenuButton" style="display:none;">
                <button class="btn btn-outline-secondary mb-3" onclick="toggleSidebar()">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const outsideButton = document.getElementById('outsideMenuButton');

            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                outsideButton.style.display = 'none';
            } else {
                sidebar.classList.add('hidden');
                outsideButton.style.display = 'block';
            }
        }

        window.onload = () => {
            const sidebar = document.getElementById('sidebar');
            const outsideButton = document.getElementById('outsideMenuButton');

            if (!sidebar.classList.contains('hidden')) {
                outsideButton.style.display = 'none';
            }
        };
    </script>

    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>

</html>