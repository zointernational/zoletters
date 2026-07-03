@extends('layouts.app')

@section('title', 'Letter Preview')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-4 mb-4 border-bottom">
    <h1 class="h2">Letter Preview</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('documents.print', $document) }}" target="_blank" class="btn btn-primary me-2">
            <i class="bi bi-printer me-1"></i> Print
        </a>
        <a href="{{ route('documents.pdf.download', $document) }}" class="btn btn-success me-2">
            <i class="bi bi-download me-1"></i> Download PDF
        </a>
        <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="letterPreview" class="letter-preview">
            @if($document->template && $document->template->hasHeaderImage())
                <div class="letter-header-image">
                    <img src="{{ asset('uploads/templates/' . $document->template->header_image) }}" alt="Letterhead" class="img-fluid">
                </div>
            @endif

            <div class="letter-content">
                <div class="letter-meta">
                    <p><strong>Ref: {{ $document->reference_no }}</strong></p>
                    <p><strong>Date: {{ $document->created_at->format('F d, Y') }}</strong></p>
                </div>

                <div class="letter-recipient mt-4">
                    <p><strong>To:</strong></p>
                    <p>{{ $document->recipient_name }}</p>
                    <p style="white-space: pre-wrap;">{{ $document->recipient_address }}</p>
                </div>

                <div class="letter-subject mt-4">
                    <p><strong>Subject: {{ $document->subject }}</strong></p>
                </div>

                <div class="letter-body mt-4">
                    {!! $document->body_html !!}
                </div>

                <div class="letter-signature mt-5">
                    <p>Sincerely,</p>
                    <p class="mt-5"><strong>{{ config('app.name', 'ZO Letters') }}</strong></p>
                </div>
            </div>

            @if($document->template && $document->template->hasFooterImage())
                <div class="letter-footer-image">
                    <img src="{{ asset('uploads/templates/' . $document->template->footer_image) }}" alt="Footer" class="img-fluid">
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.letter-preview {
    background: white;
    padding: 40px;
    max-width: 800px;
    margin: 0 auto;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    font-family: 'Times New Roman', Times, serif;
    font-size: 12pt;
    line-height: 1.6;
}

.letter-header-image img,
.letter-footer-image img {
    max-width: 100%;
    height: auto;
}

.letter-header-image {
    margin-bottom: 30px;
    border-bottom: 2px solid #333;
    padding-bottom: 20px;
}

.letter-footer-image {
    margin-top: 30px;
    border-top: 2px solid #333;
    padding-top: 20px;
}

.letter-body {
    text-align: justify;
}

.letter-body p {
    margin-bottom: 1em;
}

.letter-signature {
    text-align: left;
}
</style>
@endpush
