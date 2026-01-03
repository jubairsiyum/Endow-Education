<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'registration_id' => 'STU20260001',
                'name' => 'John Smith',
                'surname' => 'Smith',
                'given_names' => 'John Michael',
                'email' => 'john.smith@example.com',
                'phone' => '+1-555-0101',
                'date_of_birth' => '2000-05-15',
                'gender' => 'male',
                'nationality' => 'American',
                'country' => 'USA',
                'address' => '123 Main Street',
                'city' => 'New York',
                'postal_code' => '10001',
                'status' => 'new',
                'account_status' => 'approved',
                'profile' => [
                    'student_id_number' => 'SN2026001',
                    'academic_level' => 'Undergraduate',
                    'major' => 'Computer Science',
                    'minor' => 'Mathematics',
                    'gpa' => 3.75,
                    'enrollment_date' => '2024-09-01',
                    'expected_graduation_date' => '2028-06-15',
                    'bio' => 'Passionate computer science student with interest in AI and machine learning.',
                    'interests' => 'Programming, Chess, Reading',
                    'skills' => 'Python, Java, React, Node.js',
                ],
            ],
            [
                'registration_id' => 'STU20260002',
                'name' => 'Sarah Johnson',
                'surname' => 'Johnson',
                'given_names' => 'Sarah Elizabeth',
                'email' => 'sarah.johnson@example.com',
                'phone' => '+1-555-0102',
                'date_of_birth' => '1999-08-22',
                'gender' => 'female',
                'nationality' => 'Canadian',
                'country' => 'Canada',
                'address' => '456 Oak Avenue',
                'city' => 'Toronto',
                'postal_code' => 'M5H 2N2',
                'status' => 'processing',
                'account_status' => 'approved',
                'profile' => [
                    'student_id_number' => 'SN2026002',
                    'academic_level' => 'Graduate',
                    'major' => 'Business Administration',
                    'gpa' => 3.92,
                    'enrollment_date' => '2023-09-01',
                    'expected_graduation_date' => '2025-05-15',
                    'bio' => 'MBA student focusing on international business and marketing strategy.',
                    'interests' => 'Marketing, Entrepreneurship, Travel',
                    'skills' => 'Project Management, Data Analysis, Marketing',
                ],
            ],
            [
                'registration_id' => 'STU20260003',
                'name' => 'Ahmed Hassan',
                'surname' => 'Hassan',
                'given_names' => 'Ahmed Ali',
                'email' => 'ahmed.hassan@example.com',
                'phone' => '+20-555-0103',
                'date_of_birth' => '2001-03-10',
                'gender' => 'male',
                'nationality' => 'Egyptian',
                'country' => 'Egypt',
                'address' => '789 Nile Road',
                'city' => 'Cairo',
                'postal_code' => '11511',
                'passport_number' => 'A12345678',
                'passport_expiry_date' => '2028-03-10',
                'status' => 'applied',
                'account_status' => 'pending',
                'profile' => [
                    'student_id_number' => 'SN2026003',
                    'academic_level' => 'Undergraduate',
                    'major' => 'Mechanical Engineering',
                    'gpa' => 3.45,
                    'enrollment_date' => '2024-09-01',
                    'expected_graduation_date' => '2028-06-15',
                    'bio' => 'Engineering student with passion for renewable energy and sustainable design.',
                    'interests' => 'Robotics, Renewable Energy, Soccer',
                    'skills' => 'CAD, MATLAB, AutoCAD, 3D Printing',
                ],
            ],
            [
                'registration_id' => 'STU20260004',
                'name' => 'Maria Garcia',
                'surname' => 'Garcia',
                'given_names' => 'Maria Isabel',
                'email' => 'maria.garcia@example.com',
                'phone' => '+34-555-0104',
                'date_of_birth' => '2000-11-28',
                'gender' => 'female',
                'nationality' => 'Spanish',
                'country' => 'Spain',
                'address' => '321 Barcelona Street',
                'city' => 'Madrid',
                'postal_code' => '28001',
                'status' => 'new',
                'account_status' => 'approved',
                'emergency_contact_name' => 'Carlos Garcia',
                'emergency_contact_phone' => '+34-555-9999',
                'emergency_contact_relationship' => 'Father',
                'profile' => [
                    'student_id_number' => 'SN2026004',
                    'academic_level' => 'Undergraduate',
                    'major' => 'International Relations',
                    'minor' => 'Economics',
                    'gpa' => 3.88,
                    'enrollment_date' => '2024-09-01',
                    'expected_graduation_date' => '2028-06-15',
                    'bio' => 'International relations student interested in diplomacy and global governance.',
                    'interests' => 'Politics, Languages, Photography',
                    'skills' => 'Public Speaking, Research, Spanish, English, French',
                ],
            ],
            [
                'registration_id' => 'STU20260005',
                'name' => 'Li Wei',
                'surname' => 'Li',
                'given_names' => 'Wei',
                'email' => 'li.wei@example.com',
                'phone' => '+86-555-0105',
                'date_of_birth' => '1998-07-05',
                'gender' => 'male',
                'nationality' => 'Chinese',
                'country' => 'China',
                'address' => '567 Beijing Road',
                'city' => 'Shanghai',
                'postal_code' => '200000',
                'status' => 'contacted',
                'account_status' => 'approved',
                'profile' => [
                    'student_id_number' => 'SN2026005',
                    'academic_level' => 'Graduate',
                    'major' => 'Data Science',
                    'gpa' => 3.95,
                    'enrollment_date' => '2024-01-15',
                    'expected_graduation_date' => '2025-12-15',
                    'bio' => 'Data science graduate student specializing in machine learning and big data analytics.',
                    'interests' => 'AI, Big Data, Basketball',
                    'skills' => 'Python, R, TensorFlow, SQL, Machine Learning',
                ],
            ],
        ];

        foreach ($students as $studentData) {
            $profileData = $studentData['profile'] ?? null;
            unset($studentData['profile']);

            // Create student
            $student = Student::create($studentData);

            // Create profile if data exists
            if ($profileData) {
                $student->profile()->create($profileData);
            }

            $this->command->info("Created student: {$student->name} ({$student->registration_id})");
        }

        $this->command->info('Student profile seeding completed!');
    }
}
