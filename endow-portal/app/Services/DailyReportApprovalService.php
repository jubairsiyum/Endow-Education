<?php

namespace App\Services;

use App\Models\DailyReport;
use App\Models\DailyReportApprover;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Daily Report Approval Workflow Service
 * 
 * Manages multi-level approval process
 */
class DailyReportApprovalService
{
    /**
     * Initialize approval workflow for a report
     * 
     * @param DailyReport $report
     * @param array $approvers Array of user IDs in approval order
     */
    public function initializeApprovalChain(DailyReport $report, array $approvers): void
    {
        DB::beginTransaction();
        try {
            foreach ($approvers as $level => $approverId) {
                DailyReportApprover::create([
                    'daily_report_id' => $report->id,
                    'approver_id' => $approverId,
                    'approval_level' => $level + 1,
                    'status' => 'pending',
                ]);
            }
            
            DB::commit();
            Log::info('Approval chain initialized', [
                'report_id' => $report->id,
                'approvers_count' => count($approvers),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to initialize approval chain', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get current pending approver(s)
     * 
     * For sequential workflow, returns the first pending approver
     * For parallel workflow, returns all pending approvers at current level
     */
    public function getCurrentApprovers(DailyReport $report, bool $sequential = true)
    {
        $query = $report->approvers()->pending();
        
        if ($sequential) {
            // Get the lowest level pending approval
            $minLevel = $report->approvers()->pending()->min('approval_level');
            if ($minLevel) {
                $query->where('approval_level', $minLevel);
            }
        }
        
        return $query->get();
    }

    /**
     * Process approval response
     */
    public function processApproval(
        DailyReport $report, 
        User $approver, 
        bool $approved, 
        ?string $comments = null
    ): bool {
        DB::beginTransaction();
        try {
            // Find approver record
            $approverRecord = $report->approvers()
                ->where('approver_id', $approver->id)
                ->where('status', 'pending')
                ->first();

            if (!$approverRecord) {
                throw new Exception('Approver not found or already responded');
            }

            // Update approver record
            $approverRecord->update([
                'status' => $approved ? 'approved' : 'rejected',
                'comments' => $comments,
                'responded_at' => now(),
            ]);

            // If rejected, update report status
            if (!$approved) {
                $report->update([
                    'status' => DailyReport::STATUS_REJECTED,
                    'rejection_reason' => $comments,
                ]);
                
                DB::commit();
                return false;
            }

            // Check if all approvals are complete
            $pendingCount = $report->approvers()->pending()->count();
            
            if ($pendingCount === 0) {
                // All approvals complete
                $report->update([
                    'status' => DailyReport::STATUS_APPROVED,
                    'approved_by' => $approver->id,
                    'approved_at' => now(),
                ]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to process approval', [
                'report_id' => $report->id,
                'approver_id' => $approver->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get pending approvals for a user
     */
    public function getPendingApprovalsForUser(User $user, int $perPage = 15)
    {
        return DailyReportApprover::with(['dailyReport.submittedBy', 'dailyReport.department'])
            ->byApprover($user->id)
            ->pending()
            ->latest('created_at')
            ->paginate($perPage);
    }

    /**
     * Send reminder to pending approvers
     */
    public function sendReminders(DailyReport $report): void
    {
        $pendingApprovers = $this->getCurrentApprovers($report);
        
        foreach ($pendingApprovers as $approverRecord) {
            // TODO: Send notification/email
            $approverRecord->incrementReminder();
            $approverRecord->markAsNotified();
            
            Log::info('Reminder sent to approver', [
                'report_id' => $report->id,
                'approver_id' => $approverRecord->approver_id,
                'reminder_count' => $approverRecord->reminder_count,
            ]);
        }
    }

    /**
     * Check if user can approve report
     */
    public function canApprove(DailyReport $report, User $user): bool
    {
        return $report->approvers()
            ->where('approver_id', $user->id)
            ->where('status', 'pending')
            ->exists();
    }
}
