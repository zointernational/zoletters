@extends('install.layout')

@section('title', 'Install - Administrator Account')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-person-badge me-2 text-primary"></i>Administrator Account
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Create your administrator account. This will be used to manage the application.
                    </div>
                    
                    <form id="admin-form" method="POST" action="{{ route('install.process') }}">
                        @csrf
                        
                        {{-- Hidden fields for database data --}}
                        <input type="hidden" name="db_host" value="{{ request()->input('db_host', session('db_host', 'localhost')) }}">
                        <input type="hidden" name="db_port" value="{{ request()->input('db_port', session('db_port', 3306)) }}">
                        <input type="hidden" name="db_name" value="{{ request()->input('db_name', session('db_name')) }}">
                        <input type="hidden" name="db_user" value="{{ request()->input('db_user', session('db_user')) }}">
                        <input type="hidden" name="db_pass" value="{{ request()->input('db_pass', session('db_pass')) }}">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Administrator Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('admin_name') is-invalid @enderror" 
                                       name="admin_name" 
                                       value="{{ old('admin_name', 'admin') }}" 
                                       required>
                                @error('admin_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                       name="admin_email" 
                                       value="{{ old('admin_email', 'admin@zointernational.in') }}" 
                                       required>
                                @error('admin_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('admin_password') is-invalid @enderror" 
                                       name="admin_password" 
                                       minlength="8" 
                                       required>
                                @error('admin_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimum 8 characters</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" 
                                       name="admin_password_confirmation" 
                                       minlength="8" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Remember this password!</strong> It will be displayed only once after installation.
                        </div>
                        
                        @if($errors->any())
                            <div class="alert alert-danger mt-3">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('install.database') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>Install Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
