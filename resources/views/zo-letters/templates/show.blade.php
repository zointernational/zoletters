@extends('layouts.app')

@section('title', 'Template Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4 mb-4 border-bottom">
    <h1 class="h2">Template Details</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('templates.edit', $template) }}" class="btn btn-primary me-2">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="w-25">Name</th>
                            <td>{{ $template->name }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $template->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Page Size</th>
                            <td>{{ $template->page_size }}</td>
                        </tr>
                        <tr>
                            <th>Orientation</th>
                            <td>{{ ucfirst($template->orientation) }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-{{ $template->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($template->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Margins</th>
                            <td>
                                Top: {{ $template->margin_top }}mm, 
                                Bottom: {{ $template->margin_bottom }}mm, 
                                Left: {{ $template->margin_left }}mm, 
                                Right: {{ $template->margin_right }}mm
                            </td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td>{{ $template->created_at->format('F d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $template->updated_at->format('F d, Y h:i A') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="col-md-4">
                <h6>Header Image</h6>
                @if($template->header_image)
                    <div class="border rounded p-2 mb-4">
                        <img src="{{ asset('uploads/templates/' . $template->header_image) }}" 
                             alt="Header" class="img-fluid">
                    </div>
                @else
                    <p class="text-muted">No header image uploaded.</p>
                @endif
                
                <h6>Footer Image</h6>
                @if($template->footer_image)
                    <div class="border rounded p-2">
                        <img src="{{ asset('uploads/templates/' . $template->footer_image) }}" 
                             alt="Footer" class="img-fluid">
                    </div>
                @else
                    <p class="text-muted">No footer image uploaded.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
