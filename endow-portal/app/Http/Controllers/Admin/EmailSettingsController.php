<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use App\Models\User;
use App\Notifications\NewStudentRegisteredNotification;
use App\Notifications\StudentApprovedNotification;
use App\Notifications\StudentRejectedNotification;
use App\Notifications\StudentAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    /**
     * Display email settings page
     */
    public function index()
    {
        $settings = EmailSetting::first();
        
        // If no settings exist, create default
        if (!$settings) {
            $settings = EmailSetting::create([
                'mailer' => config('mail.default', 'smtp'),
                'host' => config('mail.mailers.smtp.host', 'smtp.gmail.com'),
                'port' => config('mail.mailers.smtp.port', 587),
                'username' => config('mail.mailers.smtp.username', ''),
                'password' => config('mail.mailers.smtp.password', ''),
                'encryption' => config('mail.mailers.smtp.encryption', 'tls'),
                'from_address' => config('mail.from.address', ''),
                'from_name' => config('mail.from.name', 'Endow Connect'),
                'is_active' => true,
            ]);
        }

        return view('admin.email-settings.index', compact('settings'));
    }

    /**
     * Update email settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mailer' => 'required|in:smtp,sendmail,mailgun,ses,postmark,log',
            'host' => 'required_if:mailer,smtp',
            'port' => 'required_if:mailer,smtp|nullable|integer',
            'username' => 'nullable|string',
            'password' => 'nullable|string',
            'encryption' => 'required_if:mailer,smtp|nullable|in:tls,ssl',
            'from_address' => 'required|email',
            'from_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $settings = EmailSetting::first();
            
            if (!$settings) {
                $settings = new EmailSetting();
            }

            $settings->mailer = $request->mailer;
            $settings->host = $request->host;
            $settings->port = $request->port;
            $settings->username = $request->username;
            
            // Only update password if provided
            if ($request->filled('password')) {
                $settings->password = encrypt($request->password);
            }
            
            $settings->encryption = $request->encryption;
            $settings->from_address = $request->from_address;
            $settings->from_name = $request->from_name;
            $settings->is_active = $request->has('is_active');
            $settings->save();

            // Update runtime configuration
            $this->updateMailConfig($settings);

            // Clear config cache
            Artisan::call('config:clear');

            return back()->with('success', 'Email settings updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update email settings: ' . $e->getMessage());
            return back()->with('error', 'Failed to update email settings: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show test email form
     */
    public function testForm()
    {
        $settings = EmailSetting::first();
        return view('admin.email-settings.test', compact('settings'));
    }

    /**
     * Send test email
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:basic,registration,approval,rejection,assignment',
        ]);

        try {
            // Update mail configuration with current settings
            $settings = EmailSetting::first();
            if ($settings && $settings->is_active) {
                $this->updateMailConfig($settings);
            }

            $recipient = User::where('email', $request->email)->first();
            
            if (!$recipient) {
                // Create a temporary user object for testing
                $recipient = new User([
                    'name' => 'Test User',
                    'email' => $request->email,
                ]);
            }

            switch ($request->type) {
                case 'basic':
                    Mail::raw('This is a test email from Endow Connect. If you received this, your email configuration is working correctly!', function($message) use ($request) {
                        $message->to($request->email)
                                ->subject('Test Email - Endow Connect');
                    });
                    break;

                case 'registration':
                    $student = \App\Models\Student::with(['targetUniversity', 'targetProgram'])->first();
                    if (!$student) {
                        return back()->with('error', 'No student data available for testing. Please create a student first.');
                    }
                    $recipient->notify(new NewStudentRegisteredNotification($student));
                    break;

                case 'approval':
                    $student = \App\Models\Student::with(['targetUniversity', 'targetProgram', 'assignedUser'])->first();
                    if (!$student) {
                        return back()->with('error', 'No student data available for testing. Please create a student first.');
                    }
                    $recipient->notify(new StudentApprovedNotification($student, 'TestPass123!@#'));
                    break;

                case 'rejection':
                    $student = \App\Models\Student::first();
                    if (!$student) {
                        return back()->with('error', 'No student data available for testing. Please create a student first.');
                    }
                    $recipient->notify(new StudentRejectedNotification($student, 'This is a test rejection reason.'));
                    break;

                case 'assignment':
                    $student = \App\Models\Student::with(['targetUniversity', 'targetProgram'])->first();
                    $admin = User::role(['Super Admin', 'Admin'])->first();
                    if (!$student || !$admin) {
                        return back()->with('error', 'Required data not available for testing.');
                    }
                    $recipient->notify(new StudentAssignedNotification($student, $admin));
                    break;
            }

            return back()->with('success', "Test email sent successfully to {$request->email}! Please check the inbox (and spam folder).");

        } catch (\Exception $e) {
            Log::error('Test email failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    /**
     * Update runtime mail configuration
     */
    protected function updateMailConfig(EmailSetting $settings)
    {
        Config::set('mail.default', $settings->mailer);
        Config::set('mail.mailers.smtp.host', $settings->host);
        Config::set('mail.mailers.smtp.port', $settings->port);
        Config::set('mail.mailers.smtp.username', $settings->username);
        Config::set('mail.mailers.smtp.password', $settings->getDecryptedPassword());
        Config::set('mail.mailers.smtp.encryption', $settings->encryption);
        Config::set('mail.from.address', $settings->from_address);
        Config::set('mail.from.name', $settings->from_name);
    }

    /**
     * Test connection
     */
    public function testConnection(Request $request)
    {
        try {
            $settings = EmailSetting::first();
            if (!$settings || !$settings->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email settings not configured or disabled.',
                ], 422);
            }

            $this->updateMailConfig($settings);

            // Test connection by creating a transport instance
            $transport = app(\Illuminate\Mail\Mailer::class)->getSymfonyTransport();
            
            // For SMTP, try to ping the connection
            if ($settings->mailer === 'smtp') {
                // Create a temporary test message to verify connection
                Mail::raw('Connection test', function ($message) use ($settings) {
                    $message->to($settings->from_address)
                            ->subject('Connection Test');
                });
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Email configuration is valid and connection successful!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ], 422);
        }
    }
}
