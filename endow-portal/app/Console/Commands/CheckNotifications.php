<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:check {userId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check notifications for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        
        if (!$userId) {
            $user = User::first();
            if (!$user) {
                $this->error('No users found in the database');
                return 1;
            }
        } else {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found");
                return 1;
            }
        }

        $this->info("=== Notification Status for {$user->name} ===");
        $this->line('');

        // Get notification counts
        $totalNotifications = $user->notifications()->count();
        $unreadNotifications = $user->unreadNotifications()->count();
        $readNotifications = $user->readNotifications()->count();

        $this->info("Total Notifications: {$totalNotifications}");
        $this->info("Unread: {$unreadNotifications}");
        $this->info("Read: {$readNotifications}");
        $this->line('');

        // Show recent notifications
        if ($totalNotifications > 0) {
            $this->info('=== Recent Notifications ===');
            $recent = $user->notifications()->latest()->take(5)->get();

            foreach ($recent as $notification) {
                $status = $notification->read_at ? '✓ Read' : '○ Unread';
                $data = $notification->data;
                $title = $data['title'] ?? 'No title';
                $message = $data['message'] ?? 'No message';
                $time = $notification->created_at->diffForHumans();

                $this->line("{$status} - {$title}");
                $this->line("  {$message}");
                $this->line("  {$time}");
                $this->line('');
            }
        } else {
            $this->comment('No notifications found for this user.');
        }

        return 0;
    }
}
