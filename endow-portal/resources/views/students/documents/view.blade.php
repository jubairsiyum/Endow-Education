@extends('layouts.admin')

@section('page-title', 'View Document')
@section('breadcrumb', 'Home' . ($student ? ' / Students / ' . $student->name : '') . ' / View Document')

@push('styles')
<style>
    .document-viewer {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        min-height: 600px;
    }
    
    .document-info {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .document-embed {
        width: 100%;
        min-height: 700px;
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .action-buttons .btn {
        min-width: 120px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Document Info Card -->
    <div class="card-custom mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-2">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    {{ $document->original_name ?? $document->filename ?? $document->file_name }}
                </h4>
                <div class="text-muted">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        Size: {{ number_format($document->file_size / 1024, 2) }} KB
                        @if($document->mime_type)
                            · Type: {{ $document->mime_type }}
                        @endif
                        @if($document->created_at)
                            · Uploaded: {{ $document->created_at->format('M d, Y g:i A') }}
                        @endif
                    </small>
                </div>
            </div>
            <div class="action-buttons d-flex gap-2">
                <a href="{{ $student ? route('students.documents.download', ['student' => $student, 'document' => $document]) : route('documents.download', $document) }}" 
                   class="btn btn-primary">
                    <i class="fas fa-download me-2"></i> Download
                </a>
                <a href="{{ $student ? route('students.show', $student) : route('documents.index') }}" 
                   class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Document Viewer -->
    <div class="card-custom">
        <div class="document-viewer">
            @if($document->mime_type === 'application/pdf')
                <embed src="data:{{ $document->mime_type }};base64,{{ base64_encode($fileContent) }}" 
                       class="document-embed" 
                       type="{{ $document->mime_type }}"
                       width="100%"
                       height="700px">
            @elseif(str_starts_with($document->mime_type, 'image/'))
                <div class="text-center">
                    <img src="data:{{ $document->mime_type }};base64,{{ base64_encode($fileContent) }}" 
                         class="img-fluid" 
                         style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                         alt="Document">
                </div>
            @else
                <div class="alert alert-warning-custom">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <strong>Preview Not Available</strong>
                            <p class="mb-0">This document type ({{ $document->mime_type }}) cannot be previewed in the browser. Please download it to view the contents.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection