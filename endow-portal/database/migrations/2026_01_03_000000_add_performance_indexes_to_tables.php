<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds performance-critical indexes to improve query speed.
     * These indexes target the most frequently queried columns based on actual usage patterns.
     */
    /**
     * Run the migrations.
     * 
     * This migration adds performance-critical indexes to improve query speed.
     * These indexes target the most frequently queried columns based on actual usage patterns.
     * Only adds indexes for columns that exist in the current schema.
     */
    public function up(): void
    {
        $connection = Schema::getConnection();
        
        // Helper function to check if index exists
        $indexExists = function ($table, $indexName) use ($connection) {
            $result = $connection->select(
                "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
                [$indexName]
            );
            return !empty($result);
        };
        
        // Helper function to check if column exists
        $columnExists = function ($table, $column) {
            return Schema::hasColumn($table, $column);
        };

        // Students table - Performance indexes for dashboard and filtering
        if (!$indexExists('students', 'idx_students_status_assigned') && 
            $columnExists('students', 'status') && $columnExists('students', 'assigned_to')) {
            Schema::table('students', function (Blueprint $table) {
                $table->index(['status', 'assigned_to'], 'idx_students_status_assigned');
            });
        }
        
        if (!$indexExists('students', 'idx_students_account_status_created') &&
            $columnExists('students', 'account_status') && $columnExists('students', 'created_at')) {
            Schema::table('students', function (Blueprint $table) {
                $table->index(['account_status', 'created_at'], 'idx_students_account_status_created');
            });
        }
        
        if (!$indexExists('students', 'idx_students_created_by') && $columnExists('students', 'created_by')) {
            Schema::table('students', function (Blueprint $table) {
                $table->index('created_by', 'idx_students_created_by');
            });
        }
        
        if (!$indexExists('students', 'idx_students_target_university') && $columnExists('students', 'target_university_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->index('target_university_id', 'idx_students_target_university');
            });
        }
        
        if (!$indexExists('students', 'idx_students_target_program') && $columnExists('students', 'target_program_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->index('target_program_id', 'idx_students_target_program');
            });
        }
        
        if (!$indexExists('students', 'idx_students_user_id') && $columnExists('students', 'user_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->index('user_id', 'idx_students_user_id');
            });
        }

        // Student checklists - Progress tracking and status queries
        if (!$indexExists('student_checklists', 'idx_checklists_student_status') &&
            $columnExists('student_checklists', 'student_id') && $columnExists('student_checklists', 'status')) {
            Schema::table('student_checklists', function (Blueprint $table) {
                $table->index(['student_id', 'status'], 'idx_checklists_student_status');
            });
        }
        
        if (!$indexExists('student_checklists', 'idx_checklists_item') && $columnExists('student_checklists', 'checklist_item_id')) {
            Schema::table('student_checklists', function (Blueprint $table) {
                $table->index('checklist_item_id', 'idx_checklists_item');
            });
        }
        
        if (!$indexExists('student_checklists', 'idx_checklists_reviewer') && $columnExists('student_checklists', 'reviewed_by')) {
            Schema::table('student_checklists', function (Blueprint $table) {
                $table->index('reviewed_by', 'idx_checklists_reviewer');
            });
        }
        
        if (!$indexExists('student_checklists', 'idx_checklists_created') && $columnExists('student_checklists', 'created_at')) {
            Schema::table('student_checklists', function (Blueprint $table) {
                $table->index('created_at', 'idx_checklists_created');
            });
        }

        // Student documents - Document management and review queries
        if (!$indexExists('student_documents', 'idx_documents_student_status') &&
            $columnExists('student_documents', 'student_id') && $columnExists('student_documents', 'status')) {
            Schema::table('student_documents', function (Blueprint $table) {
                $table->index(['student_id', 'status'], 'idx_documents_student_status');
            });
        }
        
        if (!$indexExists('student_documents', 'idx_documents_checklist_item') && $columnExists('student_documents', 'checklist_item_id')) {
            Schema::table('student_documents', function (Blueprint $table) {
                $table->index('checklist_item_id', 'idx_documents_checklist_item');
            });
        }
        
        if (!$indexExists('student_documents', 'idx_documents_uploader') && $columnExists('student_documents', 'uploaded_by')) {
            Schema::table('student_documents', function (Blueprint $table) {
                $table->index('uploaded_by', 'idx_documents_uploader');
            });
        }
        
        if (!$indexExists('student_documents', 'idx_documents_reviewer') && $columnExists('student_documents', 'reviewed_by')) {
            Schema::table('student_documents', function (Blueprint $table) {
                $table->index('reviewed_by', 'idx_documents_reviewer');
            });
        }
        
        if (!$indexExists('student_documents', 'idx_documents_created') && $columnExists('student_documents', 'created_at')) {
            Schema::table('student_documents', function (Blueprint $table) {
                $table->index('created_at', 'idx_documents_created');
            });
        }

        // Follow-ups table - Only index columns that exist (no status, due_date, or assigned_to)
        if (!$indexExists('follow_ups', 'idx_followups_student') && $columnExists('follow_ups', 'student_id')) {
            Schema::table('follow_ups', function (Blueprint $table) {
                $table->index('student_id', 'idx_followups_student');
            });
        }
        
        if (!$indexExists('follow_ups', 'idx_followups_next_date') && $columnExists('follow_ups', 'next_follow_up_date')) {
            Schema::table('follow_ups', function (Blueprint $table) {
                $table->index('next_follow_up_date', 'idx_followups_next_date');
            });
        }
        
        if (!$indexExists('follow_ups', 'idx_followups_creator') && $columnExists('follow_ups', 'created_by')) {
            Schema::table('follow_ups', function (Blueprint $table) {
                $table->index('created_by', 'idx_followups_creator');
            });
        }

        // Activity logs - Uses spatie/laravel-activitylog structure
        if (!$indexExists('activity_logs', 'idx_activity_subject') &&
            $columnExists('activity_logs', 'subject_type') && $columnExists('activity_logs', 'subject_id')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->index(['subject_type', 'subject_id'], 'idx_activity_subject');
            });
        }
        
        if (!$indexExists('activity_logs', 'idx_activity_causer') &&
            $columnExists('activity_logs', 'causer_type') && $columnExists('activity_logs', 'causer_id')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->index(['causer_type', 'causer_id'], 'idx_activity_causer');
            });
        }
        
        if (!$indexExists('activity_logs', 'idx_activity_created') && $columnExists('activity_logs', 'created_at')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->index('created_at', 'idx_activity_created');
            });
        }

        // Universities - Lookup and filtering
        if (!$indexExists('universities', 'idx_universities_status') && $columnExists('universities', 'status')) {
            Schema::table('universities', function (Blueprint $table) {
                $table->index('status', 'idx_universities_status');
            });
        }
        
        if (!$indexExists('universities', 'idx_universities_creator') && $columnExists('universities', 'created_by')) {
            Schema::table('universities', function (Blueprint $table) {
                $table->index('created_by', 'idx_universities_creator');
            });
        }

        // Programs - University program queries
        if (!$indexExists('programs', 'idx_programs_university_active') &&
            $columnExists('programs', 'university_id') && $columnExists('programs', 'is_active')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->index(['university_id', 'is_active'], 'idx_programs_university_active');
            });
        }
        
        if (!$indexExists('programs', 'idx_programs_creator') && $columnExists('programs', 'created_by')) {
            Schema::table('programs', function (Blueprint $table) {
                $table->index('created_by', 'idx_programs_creator');
            });
        }

        // Checklist items - Program checklist queries
        if (!$indexExists('checklist_items', 'idx_checklist_items_active') && $columnExists('checklist_items', 'is_active')) {
            Schema::table('checklist_items', function (Blueprint $table) {
                $table->index('is_active', 'idx_checklist_items_active');
            });
        }
        
        if (!$indexExists('checklist_items', 'idx_checklist_items_order_active') &&
            $columnExists('checklist_items', 'order') && $columnExists('checklist_items', 'is_active')) {
            Schema::table('checklist_items', function (Blueprint $table) {
                $table->index(['order', 'is_active'], 'idx_checklist_items_order_active');
            });
        }

        // Users - Authentication and role queries
        if (!$indexExists('users', 'idx_users_status') && $columnExists('users', 'status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('status', 'idx_users_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_students_status_assigned');
            $table->dropIndex('idx_students_account_status_created');
            $table->dropIndex('idx_students_created_by');
            $table->dropIndex('idx_students_target_university');
            $table->dropIndex('idx_students_target_program');
            $table->dropIndex('idx_students_user_id');
            // $table->dropIndex('idx_students_fulltext_search');
        });

        Schema::table('student_checklists', function (Blueprint $table) {
            $table->dropIndex('idx_checklists_student_status');
            $table->dropIndex('idx_checklists_item');
            $table->dropIndex('idx_checklists_reviewer');
            $table->dropIndex('idx_checklists_created');
        });

        Schema::table('student_documents', function (Blueprint $table) {
            $table->dropIndex('idx_documents_student_status');
            $table->dropIndex('idx_documents_checklist_item');
            $table->dropIndex('idx_documents_uploader');
            $table->dropIndex('idx_documents_reviewer');
            $table->dropIndex('idx_documents_created');
        });

        Schema::table('follow_ups', function (Blueprint $table) {
            $table->dropIndex('idx_followups_student_status');
            $table->dropIndex('idx_followups_due_status');
            $table->dropIndex('idx_followups_creator');
            $table->dropIndex('idx_followups_assigned');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_activity_student_created');
            $table->dropIndex('idx_activity_user');
            $table->dropIndex('idx_activity_type');
        });

        Schema::table('universities', function (Blueprint $table) {
            $table->dropIndex('idx_universities_status');
            $table->dropIndex('idx_universities_creator');
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropIndex('idx_programs_university_active');
            $table->dropIndex('idx_programs_creator');
        });

        Schema::table('checklist_items', function (Blueprint $table) {
            $table->dropIndex('idx_checklist_items_active');
            $table->dropIndex('idx_checklist_items_order_active');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_status');
        });
    }
};
