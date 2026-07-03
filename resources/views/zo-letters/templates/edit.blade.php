@extends('layouts.app')

@section('title', 'Edit Template')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4 mb-4 border-bottom">
    <h1 class="h2">Edit Template</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="templateForm" method="POST" action="{{ route('templates.update', $template) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-3">
                <!-- Name -->
                <div class="col-12">
                    <label for="name" class="form-label">Template Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $template->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description', $template->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Page Settings -->
                <div class="col-md-6">
                    <label for="page_size" class="form-label">Page Size</label>
                    <select class="form-select @error('page_size') is-invalid @enderror" id="page_size" name="page_size">
                        <option value="A4" {{ old('page_size', $template->page_size) === 'A4' ? 'selected' : '' }}>A4</option>
                        <option value="A5" {{ old('page_size', $template->page_size) === 'A5' ? 'selected' : '' }}>A5</option>
                        <option value="Letter" {{ old('page_size', $template->page_size) === 'Letter' ? 'selected' : '' }}>Letter</option>
                        <option value="Legal" {{ old('page_size', $template->page_size) === 'Legal' ? 'selected' : '' }}>Legal</option>
                    </select>
                    @error('page_size')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="orientation" class="form-label">Orientation</label>
                    <select class="form-select @error('orientation') is-invalid @enderror" id="orientation" name="orientation">
                        <option value="portrait" {{ old('orientation', $template->orientation) === 'portrait' ? 'selected' : '' }}>Portrait</option>
                        <option value="landscape" {{ old('orientation', $template->orientation) === 'landscape' ? 'selected' : '' }}>Landscape</option>
                    </select>
                    @error('orientation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Margins -->
                <div class="col-12"><h6 class="border-bottom pb-2 mb-3">Margins (mm)</h6></div>
                
                <div class="col-md-3">
                    <label for="margin_top" class="form-label">Top</label>
                    <input type="number" class="form-control @error('margin_top') is-invalid @enderror" 
                           id="margin_top" name="margin_top" value="{{ old('margin_top', $template->margin_top) }}" 
                           min="0" max="100" step="0.1">
                    @error('margin_top')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="margin_bottom" class="form-label">Bottom</label>
                    <input type="number" class="form-control @error('margin_bottom') is-invalid @enderror" 
                           id="margin_bottom" name="margin_bottom" value="{{ old('margin_bottom', $template->margin_bottom) }}" 
                           min="0" max="100" step="0.1">
                    @error('margin_bottom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="margin_left" class="form-label">Left</label>
                    <input type="number" class="form-control @error('margin_left') is-invalid @enderror" 
                           id="margin_left" name="margin_left" value="{{ old('margin_left', $template->margin_left) }}" 
                           min="0" max="100" step="0.1">
                    @error('margin_left')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label for="margin_right" class="form-label">Right</label>
                    <input type="number" class="form-control @error('margin_right') is-invalid @enderror" 
                           id="margin_right" name="margin_right" value="{{ old('margin_right', $template->margin_right) }}" 
                           min="0" max="100" step="0.1">
                    @error('margin_right')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Images -->
                <div class="col-12"><h6 class="border-bottom pb-2 mb-3">Images</h6></div>

                <div class="col-md-6">
                    <label for="header_image" class="form-label">Header Image (Letterhead)</label>
                    <input type="file" class="form-control @error('header_image') is-invalid @enderror" 
                           id="header_image" name="header_image" accept=".png,.jpg,.jpeg,.webp">
                    <small class="text-muted">PNG, JPEG, or WEBP. Max 5MB.</small>
                    @error('header_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="headerPreview" class="mt-2">
                        @if($template->header_image)
                            <img src="{{ asset('uploads/templates/' . $template->header_image) }}" 
                                 class="img-thumbnail" style="max-height: 100px;">
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="footer_image" class="form-label">Footer Image</label>
                    <input type="file" class="form-control @error('footer_image') is-invalid @enderror" 
                           id="footer_image" name="footer_image" accept=".png,.jpg,.jpeg,.webp">
                    <small class="text-muted">PNG, JPEG, or WEBP. Max 5MB.</small>
                    @error('footer_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="footerPreview" class="mt-2">
                        @if($template->footer_image)
                            <img src="{{ asset('uploads/templates/' . $template->footer_image) }}" 
                                 class="img-thumbnail" style="max-height: 100px;">
                        @endif
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                        <option value="active" {{ old('status', $template->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $template->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-check-circle me-1"></i> Update Template
                    </button>
                    <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('templateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const submitBtn = document.getElementById('submitBtn');
    const formData = new FormData(form);

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';

    fetch(form.action, {
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
            window.location.href = data.redirect;
        } else {
            alert(data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Update Template';
        }
    })
    .catch(error => {
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Update Template';
    });
});

// Image preview
['header_image', 'footer_image'].forEach(id => {
    document.getElementById(id).addEventListener('change', function() {
        const preview = document.getElementById(id.replace('_image', 'Preview'));
        const original = preview.querySelector('img');
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                if (original) {
                    original.src = e.target.result;
                } else {
                    preview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-height: 100px;">';
                }
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>
@endpush
