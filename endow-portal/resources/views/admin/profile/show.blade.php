@extends('layouts.admin')

@section('page-title', 'My Profile')
@section('breadcrumb', 'Home / Profile')

@section('content')
    <div class="row g-4">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-body-custom text-center">
                    <div class="mb-4">
                        @if($user->photo_path)
                            <img src="{{ asset('storage/' . $user->photo_path) }}"
                                 alt="{{ $user->name }}"
                                 class="rounded-circle"
                                 style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--primary);">
                        @else
                            <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 2.5rem; border: 4px solid var(--gray-200);">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <h4 class="mb-2" style="color: var(--dark); font-weight: 700;">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->roles->first()->name ?? 'User' }}</p>

                    <div class="d-flex gap-2 justify-content-center mb-4">
                        <form action="{{ route('admin.profile.photo.upload') }}" method="POST" enctype="multipart/form-data" id="upload-photo-form">
                            @csrf
                            <input type="file" name="photo" id="photo-input" class="d-none" accept="image/*" onchange="document.getElementById('upload-photo-form').submit();">
                            <button type="button" class="btn btn-primary-custom btn-sm" onclick="document.getElementById('photo-input').click();">
                                <i class="fas fa-camera me-1"></i> Upload Photo
                            </button>
                        </form>

                        @if($user->photo_path)
                        <form action="{{ route('admin.profile.photo.delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete your profile photo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <div class="stat-label">Member Since</div>
                                <div class="stat-value" style="font-size: 1.125rem;">{{ $user->created_at->format('M Y') }}</div>
                            </div>
                            <div class="col-6">
                                <div class="stat-label">Total Logins</div>
                                <div class="stat-value" style="font-size: 1.125rem;">{{ $totalLogins }}</div>
                            </div>
                        </div>
                    </div>

                    @if($lastLogin)
                    <div class="alert alert-info mt-3" style="font-size: 0.875rem;">
                        <i class="fas fa-info-circle me-1"></i> Last login: {{ $lastLogin->created_at->diffForHumans() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Profile Details & Settings -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card-custom mb-4">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Personal Information</h5>
                        <p class="text-muted mb-0" style="font-size: 0.875rem;">Your account details</p>
                    </div>
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-1"></i> Edit Profile
                    </a>
                </div>
                <div class="card-body-custom">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.875rem; font-weight: 600;">Full Name</label>
                            <div style="font-size: 1rem; color: var(--dark);">{{ $user->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.875rem; font-weight: 600;">Email Address</label>
                            <div style="font-size: 1rem; color: var(--dark);">{{ $user->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.875rem; font-weight: 600;">Phone Number</label>
                            <div style="font-size: 1rem; color: var(--dark);">{{ $user->phone ?? 'Not provided' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.875rem; font-weight: 600;">Role</label>
                            <div><span class="badge-custom badge-primary-custom">{{ $user->roles->first()->name ?? 'User' }}</span></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted" style="font-size: 0.875rem; font-weight: 600;">Address</label>
                            <div style="font-size: 1rem; color: var(--dark);">{{ $user->address ?? 'Not provided' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-1">Security Settings</h5>
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">Manage your password and security</p>
                </div>
                <div class="card-body-custom">
                    <form action="{{ route('admin.profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="current-password" required>
                                @error('current_password')
                                    <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" name="password" id="password" class="form-control" autocomplete="new-password" required>
                                @error('password')
                                    <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password" required>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="fas fa-lock me-2"></i> Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
