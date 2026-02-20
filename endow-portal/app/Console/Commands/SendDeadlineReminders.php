<?php

namespace App\Console\Commands;

use App\Models\WorkAssignment;
use App\Notifications\WorkDeadlineApproachingNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:deadline-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deadline approaching notifications for work assignments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for approaching deadlines...');

        // Get work assignments with deadlines in the next 3 days
        $assignments = WorkAssignment::with(['assignedTo'])
            ->whereIn('status', [
                WorkAssignment::STATUS_PENDING,
                WorkAssignment::STATUS_IN_PROGRESS
            ])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', Carbon::today())
            ->whereDate('due_date', '<=', Carbon::today()->addDays(3))
            ->get();

        $count = 0;

        foreach ($assignments as $assignment) {
            if ($assignment->assignedTo) {
                try {
                    $assignment->assignedTo->notify(new WorkDeadlineApproachingNotification($assignment));
                    $count++;
                    $this->info("Sent reminder for: {$assignment->title} to {$assignment->assignedTo->name}");
                } catch (\Exception $e) {
                    $this->error("Failed to send notification for assignment #{$assignment->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("Sent {$count} deadline reminder notifications.");

        return 0;
    }
}
