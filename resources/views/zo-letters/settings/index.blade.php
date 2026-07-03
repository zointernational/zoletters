@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4 mb-4 border-bottom">
    <h1 class="h2">Settings</h1>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="#company" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                <i class="bi bi-building me-2"></i> Company Information
            </a>
            <a href="#document" class="list-group-item list-group-item-action" data-bs-toggle="list">
                <i class="bi bi-file-text me-2"></i> Document Settings
            </a>
        </div>
    </div>
    <div class="col-md-9">
        <form id="settingsForm">
            @csrf
            
            <div class="tab-content">
                <!-- Company Settings -->
                <div class="tab-pane fade show active" id="company">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-building me-2"></i>Company Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="settings[company_name]" 
                                       value="{{ $groupedSettings['company']['company_name'] ?? '' }}">
                            </div>
                            <div class="mb-3">
                                <label for="company_address" class="form-label">Company Address</label>
                                <textarea class="form-control" id="company_address" name="settings[company_address]" rows="3">{{ $groupedSettings['company']['company_address'] ?? '' }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="company_phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="company_phone" name="settings[company_phone]" 
                                           value="{{ $groupedSettings['company']['company_phone'] ?? '' }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="company_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="company_email" name="settings[company_email]" 
                                           value="{{ $groupedSettings['company']['company_email'] ?? '' }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="company_website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="company_website" name="settings[company_website]" 
                                       value="{{ $groupedSettings['company']['company_website'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Settings -->
                <div class="tab-pane fade" id="document">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Document Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="reference_prefix" class="form-label">Reference Prefix</label>
                                    <input type="text" class="form-control" id="reference_prefix" name="settings[reference_prefix]" 
                                           value="{{ $groupedSettings['document']['reference_prefix'] ?? 'ZOI/LTR' }}">
                                    <small class="text-muted">Example: ZOI/LTR</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="default_page_size" class="form-label">Default Page Size</label>
                                    <select class="form-select" id="default_page_size" name="settings[default_page_size]">
                                        <option value="A4" {{ ($groupedSettings['document']['default_page_size'] ?? '') == 'A4' ? 'selected' : '' }}>A4</option>
                                        <option value="A5" {{ ($groupedSettings['document']['default_page_size'] ?? '') == 'A5' ? 'selected' : '' }}>A5</option>
                                        <option value="Letter" {{ ($groupedSettings['document']['default_page_size'] ?? '') == 'Letter' ? 'selected' : '' }}>Letter</option>
                                        <option value="Legal" {{ ($groupedSettings['document']['default_page_size'] ?? '') == 'Legal' ? 'selected' : '' }}>Legal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="default_orientation" class="form-label">Default Orientation</label>
                                    <select class="form-select" id="default_orientation" name="settings[default_orientation]">
                                        <option value="portrait" {{ ($groupedSettings['document']['default_orientation'] ?? '') == 'portrait' ? 'selected' : '' }}>Portrait</option>
                                        <option value="landscape" {{ ($groupedSettings['document']['default_orientation'] ?? '') == 'landscape' ? 'selected' : '' }}>Landscape</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="use_financial_year" name="settings[use_financial_year]" value="1" 
                                               {{ ($groupedSettings['document']['use_financial_year'] ?? '') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="use_financial_year">
                                            Use Financial Year in Reference Number
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('settingsForm');
    const alertPlaceholder = document.createElement('div');
    form.parentNode.insertBefore(alertPlaceholder, form.nextSibling);

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

        fetch('{{ route('settings.store') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alertPlaceholder.innerHTML = '<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>' + data.message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            } else {
                alertPlaceholder.innerHTML = '<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>' + data.message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            }
        })
        .catch(error => {
            alertPlaceholder.innerHTML = '<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>An error occurred. Please try again.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Save Settings';
        });
    });
});
</script>
@endpush
