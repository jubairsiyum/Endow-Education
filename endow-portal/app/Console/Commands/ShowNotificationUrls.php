<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ShowNotificationUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:show-urls {userId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show notification URLs for debugging';

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

        $this->info("=== Notification URLs for {$user->name} ===");
        $this->line('');

        $notifications = $user->notifications()->latest()->take(10)->get();

        if ($notifications->count() === 0) {
            $this->comment('No notifications found for this user.');
            return 0;
        }

        foreach ($notifications as $notification) {
            $data = $notification->data;
            $title = $data['title'] ?? 'No title';
            $url = $data['url'] ?? 'NO URL!';
            $type = $data['type'] ?? 'unknown';
            $status = $notification->read_at ? 'Read' : 'Unread';

            $this->line("[$status] {$title}");
            $this->line("  Type: {$type}");
            $this->line("  URL: {$url}");
            $this->line('');
        }

        return 0;
    }
}
