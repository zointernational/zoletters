<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Print - {{ $document->reference_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
        }

        .letter-preview {
            max-width: 100%;
            margin: 0 auto;
        }

        .letter-header-image {
            margin-bottom: 30px;
            padding-bottom: 20px;
        }

        .letter-header-image img,
        .letter-footer-image img {
            max-width: 100%;
            height: auto;
            max-height: 100px;
        }

        .letter-footer-image {
            margin-top: 30px;
            padding-top: 20px;
        }

        .letter-meta {
            margin-bottom: 30px;
        }

        .letter-meta p {
            margin: 0 0 5px 0;
        }

        .letter-recipient {
            margin-bottom: 30px;
        }

        .letter-recipient p {
            margin: 0 0 5px 0;
        }

        .letter-subject {
            margin-bottom: 30px;
        }

        .letter-subject p {
            margin: 0;
        }

        .letter-body {
            text-align: justify;
            margin-bottom: 50px;
        }

        .letter-body p {
            margin-bottom: 1em;
        }

        .letter-signature {
            text-align: left;
            margin-top: 80px;
        }

        .letter-signature p {
            margin: 0;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            width: 200px;
        }

        .no-print {
            display: none !important;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
            }

            .letter-preview {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="letter-preview">
        @if($document->template && $document->template->hasHeaderImage())
            <div class="letter-header-image">
                <img src="{{ public_path('uploads/templates/' . $document->template->header_image) }}" alt="Letterhead">
            </div>
        @endif

        <div class="letter-content">
            <div class="letter-meta">
                <p><strong>Ref: {{ $document->reference_no }}</strong></p>
                <p><strong>Date: {{ $document->created_at->format('F d, Y') }}</strong></p>
            </div>

            <div class="letter-recipient">
                <p><strong>To:</strong></p>
                <p>{{ $document->recipient_name }}</p>
                <p style="white-space: pre-wrap;">{{ $document->recipient_address }}</p>
            </div>

            <div class="letter-subject">
                <p><strong>Subject: {{ $document->subject }}</strong></p>
            </div>

            <div class="letter-body">
                {!! $document->body_html !!}
            </div>

            <div class="letter-signature">
                <p>Sincerely,</p>
                <div class="signature-line"></div>
                <p><strong>{{ config('app.name', 'ZO Letters') }}</strong></p>
            </div>
        </div>

        @if($document->template && $document->template->hasFooterImage())
            <div class="letter-footer-image">
                <img src="{{ public_path('uploads/templates/' . $document->template->footer_image) }}" alt="Footer">
            </div>
        @endif
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
