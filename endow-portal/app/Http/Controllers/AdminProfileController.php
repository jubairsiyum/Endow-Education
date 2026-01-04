<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Services\ActivityLogService;
use Spatie\Activitylog\Models\Activity;

class AdminProfileController extends Controller
{
    protected $activityLog;

    public function __construct(ActivityLogService $activityLog)
    {
        $this->middleware('auth');
        $this->activityLog = $activityLog;
    }

    /**
     * Display the admin/employee profile.
     */
    public function show()
    {
        $user = Auth::user();
        
        // Get activity stats
        $totalLogins = Activity::where('causer_id', $user->id)
            ->where('causer_type', get_class($user))
            ->where('log_name', 'auth')
            ->where('description', 'logged in')
            ->count();

        $lastLogin = Activity::where('causer_id', $user->id)
            ->where('causer_type', get_class($user))
            ->where('log_name', 'auth')
            ->where('description', 'logged in')
            ->latest()
            ->skip(1)
            ->first();

        return view('admin.profile.show', compact('user', 'totalLogins', 'lastLogin'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update($validated);

        $this->activityLog->logGeneric('profile', 'Profile updated', $user);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->activityLog->logGeneric('security', 'Password changed', $user);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Upload profile photo.
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $user = Auth::user();

        // Delete old photo if exists
        if ($user->photo_path && Storage::disk('public')->exists($user->photo_path)) {
            Storage::disk('public')->delete($user->photo_path);
        }

        // Store new photo
        $path = $request->file('photo')->store('profile-photos', 'public');

        $user->update([
            'photo_path' => $path,
        ]);

        $this->activityLog->logGeneric('profile', 'Profile photo uploaded', $user);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profile photo uploaded successfully.');
    }

    /**
     * Delete profile photo.
     */
    public function deletePhoto()
    {
        $user = Auth::user();

        if ($user->photo_path && Storage::disk('public')->exists($user->photo_path)) {
            Storage::disk('public')->delete($user->photo_path);
        }

        $user->update([
            'photo_path' => null,
        ]);

        $this->activityLog->logGeneric('profile', 'Profile photo deleted', $user);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Profile photo deleted successfully.');
    }
}
