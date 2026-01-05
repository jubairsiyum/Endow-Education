<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\User;
use App\Notifications\NewStudentRegisteredNotification;
use App\Notifications\StudentApprovedNotification;
use App\Notifications\StudentRejectedNotification;
use App\Notifications\StudentAssignedNotification;
use Illuminate\Console\Command;

class TestEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {type} {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notifications. Types: registration, approval, rejection, assignment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $email = $this->argument('email');

        // Find or create a test user
        $testUser = User::where('email', $email)->first();
        
        if (!$testUser) {
            $this->error("User with email {$email} not found!");
            $this->info("Available users:");
            User::take(5)->get()->each(function($user) {
                $this->line("  - {$user->email} ({$user->name})");
            });
            return 1;
        }

        // Get a sample student
        $student = Student::with(['targetUniversity', 'targetProgram', 'assignedUser'])->first();
        
        if (!$student) {
            $this->error("No students found in the database!");
            return 1;
        }

        $this->info("Testing {$type} notification...");
        $this->info("Recipient: {$testUser->name} ({$testUser->email})");
        $this->info("Sample Student: {$student->name}");

        try {
            switch ($type) {
                case 'registration':
                    $testUser->notify(new NewStudentRegisteredNotification($student));
                    $this->info("✅ New student registration notification sent!");
                    break;

                case 'approval':
                    $testUser->notify(new StudentApprovedNotification($student, 'Test123!@#'));
                    $this->info("✅ Student approval notification sent!");
                    break;

                case 'rejection':
                    $testUser->notify(new StudentRejectedNotification($student, 'This is a test rejection reason.'));
                    $this->info("✅ Student rejection notification sent!");
                    break;

                case 'assignment':
                    $admin = User::role(['Super Admin', 'Admin'])->first();
                    $testUser->notify(new StudentAssignedNotification($student, $admin));
                    $this->info("✅ Student assignment notification sent!");
                    break;

                default:
                    $this->error("Invalid type! Use: registration, approval, rejection, or assignment");
                    return 1;
            }

            $this->newLine();
            $this->info("Check your email inbox: {$testUser->email}");
            $this->info("Or check logs: storage/logs/laravel.log");
            
            return 0;

        } catch (\Exception $e) {
            $this->error("Failed to send notification: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}
