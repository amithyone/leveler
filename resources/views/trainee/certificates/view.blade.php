@extends('layouts.trainee')

@section('title', 'Certificate - ' . $result->course->title)

@section('content')
<div class="certificate-view-header">
    <div class="breadcrumb">
        <a href="{{ route('trainee.certificates.index') }}"><i class="fas fa-arrow-left"></i> Back to Certificates</a>
    </div>
    <h1>Certificate of Completion</h1>
</div>

<div class="certificate-display">
    <div class="certificate-paper">
        <div class="certificate-border">
            <div class="certificate-header">
                <div class="cert-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1>CERTIFICATE OF COMPLETION</h1>
                <div class="cert-divider"></div>
            </div>
            
            <div class="certificate-body">
                <p class="cert-presented">This is to certify that</p>
                <h2 class="cert-name">{{ $result->trainee->full_name }}</h2>
                <p class="cert-completed">has successfully completed the course</p>
                <h3 class="cert-course">{{ $result->course->title }}</h3>
                <p class="cert-code">Course Code: {{ $result->course->code }}</p>
                <div class="cert-score">
                    <p>With a score of <strong>{{ number_format($result->percentage, 1) }}%</strong></p>
                </div>
            </div>
            
            <div class="certificate-footer">
                <div class="cert-date">
                    <p>Date: {{ $result->completed_at->format('F d, Y') }}</p>
                </div>
                <div class="cert-signature">
                    <div class="signature-line"></div>
                    <p>Authorized Signature</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="certificate-actions">
        <button onclick="window.print()" class="btn btn-primary btn-lg">
            <i class="fas fa-print"></i> Print Certificate
        </button>
        <a href="{{ route('trainee.certificates.download', $result->id) }}" class="btn btn-outline btn-lg">
            <i class="fas fa-download"></i> Download PDF
        </a>
        <a href="{{ route('trainee.dashboard') }}" class="btn btn-outline btn-lg">
            <i class="fas fa-home"></i> Back to Dashboard
        </a>
    </div>
    
    <style media="print">
        .certificate-actions, .breadcrumb, .trainee-header, .trainee-nav {
            display: none !important;
        }
        .certificate-paper {
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        body {
            background: white !important;
        }
    </style>
</div>
@endsection

