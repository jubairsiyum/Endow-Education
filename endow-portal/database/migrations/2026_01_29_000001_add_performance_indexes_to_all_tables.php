<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Students table indexes
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (!$this->hasIndex('students', 'students_status_index')) {
                    $table->index('status', 'students_status_index');
                }
                if (!$this->hasIndex('students', 'students_assigned_to_index')) {
                    $table->index('assigned_to', 'students_assigned_to_index');
                }
                if (!$this->hasIndex('students', 'students_target_university_id_index')) {
                    $table->index('target_university_id', 'students_target_university_id_index');
                }
                if (!$this->hasIndex('students', 'students_target_program_id_index')) {
                    $table->index('target_program_id', 'students_target_program_id_index');
                }
                if (!$this->hasIndex('students', 'students_created_at_index')) {
                    $table->index('created_at', 'students_created_at_index');
                }
            });
        }

        // Transactions table indexes (for the new accounting module)
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!$this->hasIndex('transactions', 'transactions_status_index')) {
                    $table->index('status', 'transactions_status_index');
                }
                if (!$this->hasIndex('transactions', 'transactions_type_index')) {
                    $table->index('type', 'transactions_type_index');
                }
                if (!$this->hasIndex('transactions', 'transactions_entry_date_index')) {
                    $table->index('entry_date', 'transactions_entry_date_index');
                }
                if (!$this->hasIndex('transactions', 'transactions_status_entry_date_index')) {
                    $table->index(['status', 'entry_date'], 'transactions_status_entry_date_index');
                }
            });
        }

        // Account categories table indexes
        if (Schema::hasTable('account_categories')) {
            Schema::table('account_categories', function (Blueprint $table) {
                if (!$this->hasIndex('account_categories', 'account_categories_type_is_active_index')) {
                    $table->index(['type', 'is_active'], 'account_categories_type_is_active_index');
                }
            });
        }

        // Student visits table indexes
        if (Schema::hasTable('student_visits')) {
            Schema::table('student_visits', function (Blueprint $table) {
                if (!$this->hasIndex('student_visits', 'student_visits_student_id_index')) {
                    $table->index('student_id', 'student_visits_student_id_index');
                }
                if (!$this->hasIndex('student_visits', 'student_visits_visit_date_index')) {
                    $table->index('visit_date', 'student_visits_visit_date_index');
                }
            });
        }

        // Student checklists table indexes
        if (Schema::hasTable('student_checklists')) {
            Schema::table('student_checklists', function (Blueprint $table) {
                if (!$this->hasIndex('student_checklists', 'student_checklists_student_id_index')) {
                    $table->index('student_id', 'student_checklists_student_id_index');
                }
                if (!$this->hasIndex('student_checklists', 'student_checklists_status_index')) {
                    $table->index('status', 'student_checklists_status_index');
                }
            });
        }

        // Activity logs table indexes
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                if (!$this->hasIndex('activity_logs', 'activity_logs_user_id_index')) {
                    $table->index('user_id', 'activity_logs_user_id_index');
                }
                if (!$this->hasIndex('activity_logs', 'activity_logs_created_at_index')) {
                    $table->index('created_at', 'activity_logs_created_at_index');
                }
            });
        }

        // Student payments table indexes
        if (Schema::hasTable('student_payments')) {
            Schema::table('student_payments', function (Blueprint $table) {
                if (!$this->hasIndex('student_payments', 'student_payments_student_id_index')) {
                    $table->index('student_id', 'student_payments_student_id_index');
                }
                if (!$this->hasIndex('student_payments', 'student_payments_status_index')) {
                    $table->index('status', 'student_payments_status_index');
                }
            });
        }
    }

    /**
     * Check if an index exists on a table.
     */
    protected function hasIndex(string $table, string $indexName): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return !empty($indexes);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes in reverse order
        if (Schema::hasTable('student_payments')) {
            Schema::table('student_payments', function (Blueprint $table) {
                $table->dropIndex('student_payments_student_id_index');
                $table->dropIndex('student_payments_status_index');
            });
        }

        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropIndex('activity_logs_user_id_index');
                $table->dropIndex('activity_logs_created_at_index');
            });
        }

        if (Schema::hasTable('student_checklists')) {
            Schema::table('student_checklists', function (Blueprint $table) {
                $table->dropIndex('student_checklists_student_id_index');
                $table->dropIndex('student_checklists_status_index');
            });
        }

        if (Schema::hasTable('student_visits')) {
            Schema::table('student_visits', function (Blueprint $table) {
                $table->dropIndex('student_visits_student_id_index');
                $table->dropIndex('student_visits_visit_date_index');
            });
        }

        if (Schema::hasTable('account_categories')) {
            Schema::table('account_categories', function (Blueprint $table) {
                $table->dropIndex('account_categories_type_is_active_index');
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropIndex('transactions_status_index');
                $table->dropIndex('transactions_type_index');
                $table->dropIndex('transactions_entry_date_index');
                $table->dropIndex('transactions_status_entry_date_index');
            });
        }

        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropIndex('students_status_index');
                $table->dropIndex('students_assigned_to_index');
                $table->dropIndex('students_target_university_id_index');
                $table->dropIndex('students_target_program_id_index');
                $table->dropIndex('students_created_at_index');
            });
        }
    }
};
