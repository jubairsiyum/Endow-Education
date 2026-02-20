<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixNotificationUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:fix-urls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix URLs in existing notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Fixing Notification URLs ===');
        $this->line('');

        $updated = 0;

        // Fix StudentDocumentUploadedNotification
        $notifications = DB::table('notifications')
            ->where('type', 'App\\Notifications\\StudentDocumentUploadedNotification')
            ->get();

        foreach ($notifications as $notification) {
            $data = json_decode($notification->data, true);
            
            if (!isset($data['student_id'])) continue;

            $studentId = $data['student_id'];
            $oldUrl = $data['url'] ?? '';
            $newUrl = url("/students/{$studentId}#checklist");

            if ($oldUrl !== $newUrl) {
                $data['url'] = $newUrl;
                
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update(['data' => json_encode($data)]);
                
                $updated++;
                $this->line("✓ Updated: {$data['title']} - Student {$studentId}");
            }
        }

        // Fix DocumentPendingApprovalNotification
        $notifications = DB::table('notifications')
            ->where('type', 'App\\Notifications\\DocumentPendingApprovalNotification')
            ->get();

        foreach ($notifications as $notification) {
            $data = json_decode($notification->data, true);
            
            if (!isset($data['student_id'])) continue;

            $studentId = $data['student_id'];
            $oldUrl = $data['url'] ?? '';
            $newUrl = url("/students/{$studentId}#checklist");

            if ($oldUrl !== $newUrl) {
                $data['url'] = $newUrl;
                
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update(['data' => json_encode($data)]);
                
                $updated++;
                $this->line("✓ Updated: {$data['title']} - Student {$studentId}");
            }
        }

        // Fix StudentChecklistCompletedNotification
        $notifications = DB::table('notifications')
            ->where('type', 'App\\Notifications\\StudentChecklistCompletedNotification')
            ->get();

        foreach ($notifications as $notification) {
            $data = json_decode($notification->data, true);
            
            if (!isset($data['student_id'])) continue;

            $studentId = $data['student_id'];
            $oldUrl = $data['url'] ?? '';
            $newUrl = url("/students/{$studentId}#checklist");

            if ($oldUrl !== $newUrl) {
                $data['url'] = $newUrl;
                
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update(['data' => json_encode($data)]);
                
                $updated++;
                $this->line("✓ Updated: {$data['title']} - Student {$studentId}");
            }
        }

        $this->line('');
        $this->info("✓ Fixed {$updated} notifications");
        $this->line('');
        $this->info('All student-related notifications now redirect to the Checklist & Documents tab');

        return 0;
    }
}
