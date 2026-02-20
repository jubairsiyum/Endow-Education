<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;

class VerifyStudentAssignments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:verify-assignments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify which students have assigned counselors/users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Student Assignment Verification ===');
        $this->line('');

        $totalStudents = Student::count();
        $assignedStudents = Student::whereNotNull('assigned_to')->count();
        $unassignedStudents = $totalStudents - $assignedStudents;

        $this->info("Total Students: {$totalStudents}");
        $this->info("Assigned Students: {$assignedStudents}");
        $this->warn("Unassigned Students: {$unassignedStudents}");
        $this->line('');

        if ($unassignedStudents > 0) {
            $this->warn('⚠ The following students are NOT assigned to any counselor:');
            $this->warn('⚠ They will NOT receive notifications!');
            $this->line('');
            
            $unassigned = Student::whereNull('assigned_to')
                ->orWhere('assigned_to', 0)
                ->get(['id', 'name', 'email']);

            foreach ($unassigned as $student) {
                $this->line("  ID: {$student->id} - {$student->name} ({$student->email})");
            }
            $this->line('');
        }

        // Show assigned students
        $this->info('=== Students with Assigned Counselors ===');
        $this->line('');

        $assigned = Student::with('assignedUser')
            ->whereNotNull('assigned_to')
            ->where('assigned_to', '>', 0)
            ->get();

        if ($assigned->count() > 0) {
            foreach ($assigned as $student) {
                $counselor = $student->assignedUser;
                $counselorName = $counselor ? $counselor->name : 'INVALID USER ID';
                $this->line("  {$student->name} → Assigned to: {$counselorName}");
            }
        } else {
            $this->comment('  No assigned students found.');
        }

        $this->line('');
        $this->info('=== Recommendation ===');
        $this->line('To ensure notifications work properly:');
        $this->line('1. Assign all students to a counselor/user');
        $this->line('2. Use the admin panel to edit student and set "Assigned To" field');
        $this->line('3. Only assigned counselors will receive notifications for that student');

        return 0;
    }
}
