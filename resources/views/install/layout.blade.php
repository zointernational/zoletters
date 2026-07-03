<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Install - ZO Letters')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --zo-primary: #4f46e5;
            --zo-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        body {
            min-height: 100vh;
            background: var(--zo-bg);
            padding: 40px 20px;
        }
        
        .install-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .install-card .card-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            border-radius: 1rem 1rem 0 0 !important;
        }
        
        .install-card .card-body {
            background: white;
            border-radius: 0 0 1rem 1rem;
        }
        
        .install-card .card-footer {
            background: white;
            border-top: 1px solid #e5e7eb;
            border-radius: 0 0 1rem 1rem !important;
        }
        
        .footer-text {
            color: rgba(255, 255, 255, 0.8);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container" style="max-width: 900px;">
        @yield('content')
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
