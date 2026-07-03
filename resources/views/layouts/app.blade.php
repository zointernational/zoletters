<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ZO Letters') - Professional Document Automation</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --zo-primary: #0d6efd;
            --zo-secondary: #6c757d;
            --zo-success: #198754;
            --zo-danger: #dc3545;
            --zo-warning: #ffc107;
            --zo-info: #0dcaf0;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar-brand {
            font-weight: 600;
        }
        
        .sidebar {
            min-height: calc(100vh - 56px);
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        
        .sidebar .nav-link {
            color: #333;
            border-radius: 0.25rem;
            margin: 2px 8px;
        }
        
        .sidebar .nav-link:hover {
            background: #e9ecef;
        }
        
        .sidebar .nav-link.active {
            background: var(--zo-primary);
            color: white;
        }
        
        .sidebar .nav-link i {
            width: 24px;
        }
        
        main {
            flex: 1;
        }
        
        .card-stat {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            transition: transform 0.2s;
        }
        
        .card-stat:hover {
            transform: translateY(-2px);
        }
        
        .card-stat .card-body {
            padding: 1.5rem;
        }
        
        .card-stat .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .btn-group-actions .btn {
            padding: 0.25rem 0.5rem;
        }
        
        .table-actions {
            white-space: nowrap;
        }
        
        footer {
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        @media (max-width: 991.98px) {
            .sidebar {
                min-height: auto;
                border-right: none;
                border-bottom: 1px solid #dee2e6;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-envelope-paper-fill me-2"></i>ZO Letters
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="navbar-text text-white">
                            <i class="bi bi-person-circle me-1"></i>Admin
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse show" id="sidebarMenu">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('templates.index') }}">
                                <i class="bi bi-file-earmark-text"></i> Templates
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('documents.index') }}">
                                <i class="bi bi-file-earmark"></i> Documents
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-auto py-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <span class="text-muted">&copy; {{ date('Y') }} ZO International. All Rights Reserved.</span>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted">Professional Document Automation Platform</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (required for some features) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    @stack('scripts')
</body>
</html>
