@extends('install.layout')

@section('title', 'Install - Welcome')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="display-1 text-primary mb-3">📄</div>
                        <h1 class="display-6 fw-bold">ZO Letters</h1>
                        <p class="lead text-muted">Professional Letterhead Management System</p>
                        <span class="badge bg-primary px-3 py-2">Version 1.4.0</span>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="alert alert-info">
                        <h5 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>Welcome to the Installation Wizard
                        </h5>
                        <p class="mb-0">This wizard will guide you through setting up ZO Letters on your server. 
                        Please ensure you have your database credentials ready before proceeding.</p>
                    </div>
                    
                    <h5 class="mt-4 mb-3"><i class="bi bi-clipboard-check me-2"></i>What You'll Need</h5>
                    
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex align-items-center">
                            <i class="bi bi-database me-3 text-primary fs-4"></i>
                            <div>
                                <strong>Database Credentials</strong>
                                <small class="text-muted d-block">MySQL database name, username, and password</small>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="bi bi-person-badge me-3 text-primary fs-4"></i>
                            <div>
                                <strong>Administrator Account</strong>
                                <small class="text-muted d-block">Your admin login details (defaults provided)</small>
                            </div>
                        </li>
                    </ul>
                    
                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('install.requirements') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-arrow-right-circle me-2"></i>Get Started
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4 text-muted">
                <small>&copy; {{ date('Y') }} ZO International. All Rights Reserved.</small>
            </div>
        </div>
    </div>
</div>
@endsection
