@extends('layouts.app')

@section('title', 'Create Document')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4 mb-4 border-bottom">
    <h1 class="h2">Create Document</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="documentForm" method="POST" action="{{ route('documents.store') }}">
            @csrf
            
            <div class="row g-3">
                <!-- Template Selection -->
                <div class="col-12">
                    <label for="template_id" class="form-label">Template <span class="text-danger">*</span></label>
                    <select class="form-select @error('template_id') is-invalid @enderror" 
                            id="template_id" name="template_id" required>
                        <option value="">Select a template...</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                {{ $template->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('template_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Recipient Name -->
                <div class="col-md-6">
                    <label for="recipient_name" class="form-label">Recipient Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" 
                           id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}" required>
                    @error('recipient_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Subject -->
                <div class="col-md-6">
                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                           id="subject" name="subject" value="{{ old('subject') }}" required>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Recipient Address -->
                <div class="col-12">
                    <label for="recipient_address" class="form-label">Recipient Address <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('recipient_address') is-invalid @enderror" 
                              id="recipient_address" name="recipient_address" rows="3" required>{{ old('recipient_address') }}</textarea>
                    @error('recipient_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Body HTML (Rich Text Editor) -->
                <div class="col-12">
                    <label for="body_html" class="form-label">Document Body <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('body_html') is-invalid @enderror" 
                              id="body_html" name="body_html" rows="15" required>{{ old('body_html') }}</textarea>
                    @error('body_html')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-check-circle me-1"></i> Create Document
                    </button>
                    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<style>
    #body_html_ifr {
        min-height: 400px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE
    tinymce.init({
        selector: '#body_html',
        height: 400,
        menubar: false,
        statusbar: false,
        plugins: [
            'lists', 'link', 'table', 'image', 'anchor',
            'undo', 'redo'
        ],
        toolbar: 'formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | table | image | link | undo redo',
        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
        setup: function(editor) {
            editor.on('init', function() {
                this.save();
            });
        }
    });
    
    // Form submission
    document.getElementById('documentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = document.getElementById('submitBtn');
        
        // Update TinyMCE content to textarea
        tinymce.triggerSave();
        
        const formData = new FormData(form);

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Creating...';

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
                alert('Document created successfully!\nReference No: ' + data.reference_no);
                window.location.href = data.redirect;
            } else {
                alert(data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Create Document';
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Create Document';
        });
    });
});
</script>
@endpush
