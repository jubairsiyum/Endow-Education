@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow text-center">
                <div class="card-body py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 72px;"></i>
                    </div>

                    <h2 class="mb-3">Registration Submitted Successfully!</h2>

                    <p class="lead text-muted mb-4">
                        Thank you for registering with Endow Connect.
                    </p>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Your registration is currently under review. Our team will verify your information and you will receive an email notification once your account is approved.
                    </div>

                    <p class="text-muted mb-4">
                        Once approved, you will be able to log in and access your dashboard to upload required documents.
                    </p>

                    <a href="{{ route('student.login') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
