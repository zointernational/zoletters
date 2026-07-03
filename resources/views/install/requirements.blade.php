@extends('install.layout')

@section('title', 'Install - System Requirements')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-clipboard-check me-2 text-primary"></i>System Requirements
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Checking if your server meets the requirements for ZO Letters...</p>
                    
                    {{-- PHP Version --}}
                    <h5 class="mt-4 mb-3"><i class="bi bi-cpu me-2"></i>PHP Version</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Requirement</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>PHP Version</strong></td>
                                    <td>
                                        @if($requirements['php_version']['status'])
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Pass</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Fail</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">Required: {{ $requirements['php_version']['required'] }}</span><br>
                                        <span>Current: <strong>{{ $requirements['php_version']['current'] }}</strong></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- PHP Extensions --}}
                    <h5 class="mt-4 mb-3"><i class="bi bi-box me-2"></i>PHP Extensions</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Extension</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requirements['extensions'] as $key => $ext)
                                    <tr>
                                        <td><strong>{{ $ext['name'] }}</strong></td>
                                        <td>
                                            @if($ext['status'])
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Installed</span>
                                            @else
                                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Missing</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- PHP Functions --}}
                    <h5 class="mt-4 mb-3"><i class="bi bi-code me-2"></i>PHP Functions</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Function</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requirements['functions'] as $key => $func)
                                    <tr>
                                        <td><code>{{ $func['name'] }}()</code></td>
                                        <td>
                                            @if($func['status'])
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Available</span>
                                            @else
                                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Disabled</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('install.welcome') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back
                        </a>
                        @if($allPassed)
                            <a href="{{ route('install.permissions') }}" class="btn btn-primary">
                                Next<i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        @else
                            <button class="btn btn-primary" disabled>
                                Next<i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        @endif
                    </div>
                    
                    @if(!$allPassed)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Please fix the failed requirements before proceeding.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
