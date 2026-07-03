@extends('install.layout')

@section('title', 'Installation Complete')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5 text-center">
                    <div class="display-1 text-success mb-4">✅</div>
                    <h1 class="display-6 mb-3">Installation Complete!</h1>
                    <p class="lead text-muted mb-4">ZO Letters has been successfully installed on your server.</p>
                    
                    <div class="alert alert-success mb-4">
                        <h5><i class="bi bi-shield-check me-2"></i>Your Installation is Secure</h5>
                        <p class="mb-0">The application is now ready for use. No further configuration needed.</p>
                    </div>
                    
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title text-start">
                                <i class="bi bi-key me-2"></i>Your Login Credentials
                            </h5>
                            <table class="table table-borderless mb-0 text-start">
                                <tbody>
                                    <tr>
                                        <td class="text-muted" style="width: 120px;"><strong>URL:</strong></td>
                                        <td><a href="{{ url('/') }}" target="_blank">{{ url('/') }}</a></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Email:</strong></td>
                                        <td><code>{{ $adminEmail }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><strong>Password:</strong></td>
                                        <td><code>********</code></td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="text-muted small mt-3 mb-0 text-start">
                                <i class="bi bi-info-circle me-1"></i>
                                Use the password you entered during installation.
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Go to Dashboard
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="text-muted small text-start">
                        <h6><i class="bi bi-lightning me-2"></i>Quick Start Guide</h6>
                        <ol class="mb-0">
                            <li>Login with your administrator credentials</li>
                            <li>Go to <strong>Settings</strong> to configure your company information</li>
                            <li>Create your first <strong>Template</strong> with letterhead and footer</li>
                            <li>Start creating <strong>Documents</strong>!</li>
                        </ol>
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
