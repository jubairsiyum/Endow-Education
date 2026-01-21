<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data (be careful in production)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Department::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $departments = [
            [
                'name' => 'Marketing',
                'code' => 'MKT',
                'description' => 'Marketing and promotional activities, brand management, and market research',
                'icon' => 'fas fa-bullhorn',
                'color' => '#FF6B6B',
                'is_active' => true,
            ],
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'IT infrastructure, software development, technical support, and system maintenance',
                'icon' => 'fas fa-laptop-code',
                'color' => '#4ECDC4',
                'is_active' => true,
            ],
            [
                'name' => 'Consultant Services',
                'code' => 'CONSULT',
                'description' => 'Student counseling, visa consultation, and university application guidance',
                'icon' => 'fas fa-user-tie',
                'color' => '#95E1D3',
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'Employee management, recruitment, training, and HR policy implementation',
                'icon' => 'fas fa-users',
                'color' => '#F38181',
                'is_active' => true,
            ],
            [
                'name' => 'Logistics',
                'code' => 'LOG',
                'description' => 'Operations management, procurement, and logistics coordination',
                'icon' => 'fas fa-truck',
                'color' => '#AA96DA',
                'is_active' => true,
            ],
            [
                'name' => 'Finance & Accounts',
                'code' => 'FIN',
                'description' => 'Financial management, accounting, budgeting, and financial reporting',
                'icon' => 'fas fa-coins',
                'color' => '#FCBF49',
                'is_active' => true,
            ],
            [
                'name' => 'Customer Support',
                'code' => 'SUPPORT',
                'description' => 'Customer service, query resolution, and client relationship management',
                'icon' => 'fas fa-headset',
                'color' => '#06FFA5',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }

        $this->command->info('Departments seeded successfully!');
    }
}

