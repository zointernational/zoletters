@extends('install.layout')

@section('title', 'Install - Database Configuration')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">
                        <i class="bi bi-database me-2 text-primary"></i>Database Configuration
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Enter your MySQL database credentials. The database must already exist.
                    </div>
                    
                    <form id="database-form" method="GET" action="{{ route('install.administrator') }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Database Host <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="db_host" value="{{ old('db_host', 'localhost') }}" required>
                                <small class="text-muted">Usually 'localhost' for shared hosting</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Database Port <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="db_port" value="{{ old('db_port', 3306) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Database Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="db_name" value="{{ old('db_name') }}" placeholder="zoletters_db" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Database Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="db_user" value="{{ old('db_user') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Database Password</label>
                                <input type="password" class="form-control" name="db_pass" value="{{ old('db_pass') }}">
                            </div>
                        </div>
                        
                        <div id="db-test-result" class="mt-3"></div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('install.permissions') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-primary" id="test-btn">
                                    <i class="bi bi-plug me-2"></i>Test Connection
                                </button>
                                <button type="submit" class="btn btn-primary" id="next-btn" disabled>
                                    Next<i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let dbVerified = false;

document.getElementById('test-btn').addEventListener('click', function() {
    const form = document.getElementById('database-form');
    const formData = new FormData(form);
    
    const testResult = document.getElementById('db-test-result');
    testResult.innerHTML = '<div class="alert alert-info"><i class="bi bi-hourglass-split me-2"></i>Testing connection...</div>';
    
    fetch('{{ route("install.database.verify") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            db_host: formData.get('db_host'),
            db_port: formData.get('db_port'),
            db_name: formData.get('db_name'),
            db_user: formData.get('db_user'),
            db_pass: formData.get('db_pass'),
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            testResult.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>' + data.message + '</div>';
            document.getElementById('next-btn').disabled = false;
            dbVerified = true;
        } else {
            testResult.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>' + data.message + '</div>';
            document.getElementById('next-btn').disabled = true;
            dbVerified = false;
        }
    })
    .catch(error => {
        testResult.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>Connection test failed: ' + error + '</div>';
    });
});

// Form validation
document.getElementById('database-form').addEventListener('submit', function(e) {
    if (!dbVerified) {
        e.preventDefault();
        const testResult = document.getElementById('db-test-result');
        testResult.innerHTML = '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Please test the database connection before proceeding.</div>';
    }
});
</script>
@endsection
