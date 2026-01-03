<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentVisit;
use App\Models\User;

class StudentVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get employees (users with Employee, Admin, or Super Admin roles)
        $employees = User::role(['Employee', 'Admin', 'Super Admin'])->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No employees found. Please seed users first.');
            return;
        }

        $sampleVisits = [
            [
                'student_name' => 'John Smith',
                'phone' => '+1-555-0101',
                'email' => 'john.smith@example.com',
                'notes' => '<p>Student is interested in pursuing Computer Science at top universities.</p><ul><li>Preferred countries: USA, Canada, UK</li><li>Budget: $50,000 per year</li><li>IELTS score: 7.5</li></ul><p><strong>Follow-up:</strong> Schedule university shortlisting session next week.</p>',
            ],
            [
                'student_name' => 'Sarah Johnson',
                'phone' => '+1-555-0102',
                'email' => 'sarah.j@example.com',
                'notes' => '<p>Interested in Business Administration programs.</p><ul><li>Target: MBA programs</li><li>Work experience: 3 years</li><li>GMAT: 680</li></ul>',
            ],
            [
                'student_name' => 'Michael Chen',
                'phone' => '+1-555-0103',
                'email' => null,
                'notes' => '<p>Engineering student looking for scholarships.</p><p>Discussed various funding options and scholarship opportunities available.</p>',
            ],
            [
                'student_name' => 'Emily Davis',
                'phone' => '+1-555-0104',
                'email' => 'emily.davis@example.com',
                'notes' => '<p>Medical field applicant.</p><ul><li>Looking for MBBS programs</li><li>Preferred: Australia, New Zealand</li><li>Strong academic background</li></ul>',
            ],
            [
                'student_name' => 'David Wilson',
                'phone' => '+1-555-0105',
                'email' => 'david.w@example.com',
                'notes' => '<p>Parent inquiry for dependent child.</p><p>Discussed admission requirements, timeline, and visa process for undergraduate programs.</p>',
            ],
        ];

        foreach ($sampleVisits as $visit) {
            StudentVisit::create([
                'student_name' => $visit['student_name'],
                'phone' => $visit['phone'],
                'email' => $visit['email'],
                'employee_id' => $employees->random()->id,
                'notes' => $visit['notes'],
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        $this->command->info('Sample student visits created successfully!');
    }
}
