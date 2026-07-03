@extends('layouts.app')

@section('title', 'Document Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4 mb-4 border-bottom">
    <h1 class="h2">Document Details</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('documents.edit', $document) }}" class="btn btn-primary me-2">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-primary fs-6">{{ $document->reference_no }}</span>
            </div>
            <small class="text-muted">Created: {{ $document->created_at->format('F d, Y h:i A') }}</small>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="w-25">Template</th>
                            <td>{{ $document->template->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Recipient Name</th>
                            <td>{{ $document->recipient_name }}</td>
                        </tr>
                        <tr>
                            <th>Recipient Address</th>
                            <td><pre class="mb-0" style="white-space: pre-wrap;">{{ $document->recipient_address }}</pre></td>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td>{{ $document->subject }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $document->updated_at->format('F d, Y h:i A') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="col-md-4">
                <h6>Document Preview</h6>
                <div class="border rounded p-3 bg-light">
                    {!! $document->body_html !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
