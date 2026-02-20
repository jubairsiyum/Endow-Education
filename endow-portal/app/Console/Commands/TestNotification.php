<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification {userId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test notification to a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        
        if (!$userId) {
            // Get first available user
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

        // Create notification directly in database for testing
        DatabaseNotification::create([
            'id' => Str::uuid(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => get_class($user),
            'notifiable_id' => $user->id,
            'data' => [
                'type' => 'test',
                'title' => 'Test Notification',
                'message' => 'This is a test notification. The system is working correctly!',
                'url' => '/dashboard',
                'icon' => 'fas fa-check-circle',
                'color' => 'success',
            ],
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("✓ Test notification created for {$user->name} ({$user->email})");
        $this->info('✓ Refresh your browser to see the notification bell update!');
        $this->line('');
        $this->info('The notification bell should now show a badge with the unread count.');

        return 0;
    }
}
