@extends('install.layout')

@section('title', 'Install - Folder Permissions')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-folder-check me-2 text-primary"></i>Folder Permissions
                    </h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Checking if required folders are writable...</p>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Directory</th>
                                    <th>Path</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions as $key => $perm)
                                    <tr>
                                        <td><strong>{{ $perm['name'] }}</strong></td>
                                        <td><code>{{ $perm['path'] }}</code></td>
                                        <td>
                                            @if($perm['status'])
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Writable
                                                </span>
                                            @elseif(!$perm['exists'])
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle me-1"></i>Missing
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-exclamation-circle me-1"></i>Not Writable
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if(!$allPassed)
                        <div class="alert alert-warning mt-3">
                            <h5><i class="bi bi-exclamation-triangle me-2"></i>Action Required</h5>
                            <p class="mb-0">Please set the correct permissions for the folders above. You can do this via FTP or SSH:</p>
                            <pre class="mb-0 mt-2">chmod -R 755 storage bootstrap/cache public/uploads</pre>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('install.requirements') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back
                        </a>
                        @if($allPassed)
                            <a href="{{ route('install.database') }}" class="btn btn-primary">
                                Next<i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        @else
                            <button class="btn btn-primary" disabled>
                                Next<i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
