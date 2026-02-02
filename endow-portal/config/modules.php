<?php

return [
    /*
    |--------------------------------------------------------------------------
    | System Modules Configuration
    |--------------------------------------------------------------------------
    |
    | Define all modules in the system with their associated permissions.
    | This configuration is used for role management and permission assignment.
    |
    */

    'modules' => [
        'dashboard' => [
            'name' => 'Dashboard',
            'icon' => 'fas fa-home',
            'description' => 'View and access dashboard',
            'permissions' => [
                'view admin dashboard' => 'View Admin Dashboard',
                'view employee dashboard' => 'View Employee Dashboard',
                'view student dashboard' => 'View Student Dashboard',
            ],
        ],

        'users' => [
            'name' => 'User Management',
            'icon' => 'fas fa-users-cog',
            'description' => 'Manage system users',
            'permissions' => [
                'view users' => 'View Users',
                'create users' => 'Create Users',
                'edit users' => 'Edit Users',
                'delete users' => 'Delete Users',
            ],
        ],

        'roles' => [
            'name' => 'Role & Permission Management',
            'icon' => 'fas fa-user-shield',
            'description' => 'Manage roles and permissions',
            'permissions' => [
                'view roles' => 'View Roles',
                'create roles' => 'Create Roles',
                'edit roles' => 'Edit Roles',
                'delete roles' => 'Delete Roles',
                'assign permissions' => 'Assign Permissions to Roles',
                'assign user permissions' => 'Assign Permissions to Users',
            ],
        ],

        'students' => [
            'name' => 'Student Management',
            'icon' => 'fas fa-user-graduate',
            'description' => 'Manage students and applications',
            'permissions' => [
                'view students' => 'View Students',
                'create students' => 'Create Students',
                'edit students' => 'Edit Students',
                'delete students' => 'Delete Students',
                'assign students' => 'Assign Students',
                'approve students' => 'Approve Students',
            ],
        ],

        'student_visits' => [
            'name' => 'Student Visits',
            'icon' => 'fas fa-clipboard-list',
            'description' => 'Track student office visits',
            'permissions' => [
                'view student visits' => 'View Student Visits',
                'create student visits' => 'Create Student Visits',
                'edit student visits' => 'Edit Student Visits',
                'delete student visits' => 'Delete Student Visits',
            ],
        ],

        'follow_ups' => [
            'name' => 'Follow-ups',
            'icon' => 'fas fa-tasks',
            'description' => 'Manage student follow-ups',
            'permissions' => [
                'view follow-ups' => 'View Follow-ups',
                'create follow-ups' => 'Create Follow-ups',
                'edit follow-ups' => 'Edit Follow-ups',
                'delete follow-ups' => 'Delete Follow-ups',
            ],
        ],

        'documents' => [
            'name' => 'Document Management',
            'icon' => 'fas fa-file-alt',
            'description' => 'Manage student documents',
            'permissions' => [
                'view documents' => 'View Documents',
                'upload documents' => 'Upload Documents',
                'approve documents' => 'Approve Documents',
                'reject documents' => 'Reject Documents',
                'delete documents' => 'Delete Documents',
            ],
        ],

        'checklists' => [
            'name' => 'Checklist Management',
            'icon' => 'fas fa-check-square',
            'description' => 'Manage checklist items',
            'permissions' => [
                'view checklists' => 'View Checklists',
                'create checklists' => 'Create Checklists',
                'edit checklists' => 'Edit Checklists',
                'delete checklists' => 'Delete Checklists',
            ],
        ],

        'universities' => [
            'name' => 'Universities',
            'icon' => 'fas fa-university',
            'description' => 'Manage universities',
            'permissions' => [
                'view universities' => 'View Universities',
                'create universities' => 'Create Universities',
                'edit universities' => 'Edit Universities',
                'delete universities' => 'Delete Universities',
            ],
        ],

        'programs' => [
            'name' => 'Programs',
            'icon' => 'fas fa-graduation-cap',
            'description' => 'Manage academic programs',
            'permissions' => [
                'view programs' => 'View Programs',
                'create programs' => 'Create Programs',
                'edit programs' => 'Edit Programs',
                'delete programs' => 'Delete Programs',
            ],
        ],

        'contact_submissions' => [
            'name' => 'Contact Submissions',
            'icon' => 'fas fa-envelope',
            'description' => 'Manage contact form submissions',
            'permissions' => [
                'view contact submissions' => 'View Contact Submissions',
                'manage contact submissions' => 'Manage Contact Submissions',
                'delete contact submissions' => 'Delete Contact Submissions',
            ],
        ],

        'reports' => [
            'name' => 'Reports & Analytics',
            'icon' => 'fas fa-chart-line',
            'description' => 'View system reports',
            'permissions' => [
                'view reports' => 'View Reports',
                'export reports' => 'Export Reports',
            ],
        ],

        'office_daily_reports' => [
            'name' => 'Office Daily Reports',
            'icon' => 'fas fa-file-alt',
            'description' => 'Manage daily department reports',
            'permissions' => [
                'view daily reports' => 'View Daily Reports',
                'create daily reports' => 'Create Daily Reports',
                'edit daily reports' => 'Edit Daily Reports',
                'delete daily reports' => 'Delete Daily Reports',
                'review daily reports' => 'Review Daily Reports',
            ],
        ],

        'activity_logs' => [
            'name' => 'Activity Logs',
            'icon' => 'fas fa-history',
            'description' => 'View system activity logs',
            'permissions' => [
                'view activity logs' => 'View Activity Logs',
            ],
        ],

        'email_settings' => [
            'name' => 'Email Settings',
            'icon' => 'fas fa-envelope-open-text',
            'description' => 'Configure email settings',
            'permissions' => [
                'manage email settings' => 'Manage Email Settings',
            ],
        ],

        'consultant_evaluations' => [
            'name' => 'Consultant Evaluations',
            'icon' => 'fas fa-star',
            'description' => 'Manage consultant evaluations',
            'permissions' => [
                'view consultant evaluations' => 'View Consultant Evaluations',
                'manage evaluation questions' => 'Manage Evaluation Questions',
            ],
        ],

        'accounting' => [
            'name' => 'Accounting & Finance',
            'icon' => 'fas fa-money-bill-wave',
            'description' => 'Manage financial transactions and accounting',
            'permissions' => [
                'view-accounting' => 'View Accounting Module',
                'view-accounting-summary' => 'View Accounting Summary/Dashboard',
                'view-transaction' => 'View Transactions',
                'create-transaction' => 'Create Transactions',
                'edit-transaction' => 'Edit Transactions',
                'delete-transaction' => 'Delete Transactions',
                'approve-transaction' => 'Approve/Reject Transactions',
                'manage-account-categories' => 'Manage Account Categories',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Protected Roles
    |--------------------------------------------------------------------------
    |
    | These roles cannot be deleted or modified by anyone.
    | They are core to the system functionality.
    |
    */

    'protected_roles' => [
        'Super Admin',
        'Student',
    ],

    /*
    |--------------------------------------------------------------------------
    | System Permissions
    |--------------------------------------------------------------------------
    |
    | Core system permissions that should always exist.
    |
    */

    'system_permissions' => [
        'manage office' => 'Manage Office (Full Office Access)',
    ],
];
