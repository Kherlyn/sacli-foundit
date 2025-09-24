<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListAdminUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:list {--all : Show all users, not just admins}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all admin users in the SACLI FOUNDIT application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $showAll = $this->option('all');

        $this->info('👥 SACLI FOUNDIT - User Management');
        $this->info('=================================');

        if ($showAll) {
            $users = User::orderBy('role', 'desc')->orderBy('created_at', 'desc')->get();
            $this->info('📋 All Users:');
        } else {
            $users = User::where('role', 'admin')->orderBy('created_at', 'desc')->get();
            $this->info('🔧 Admin Users:');
        }

        if ($users->isEmpty()) {
            $message = $showAll ? 'No users found in the system.' : 'No admin users found in the system.';
            $this->warn("⚠️  $message");

            if (!$showAll) {
                $this->info('');
                $this->info('💡 To create an admin user, run:');
                $this->info('   php artisan admin:create');
            }

            return 0;
        }

        // Prepare table data
        $headers = ['ID', 'Name', 'Email', 'Role', 'Verified', 'Created', 'Last Login'];
        $rows = [];

        foreach ($users as $user) {
            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->role === 'admin' ? '🔧 Admin' : '👤 User',
                $user->email_verified_at ? '✅ Yes' : '❌ No',
                $user->created_at->format('Y-m-d'),
                $user->updated_at->format('Y-m-d H:i'),
            ];
        }

        $this->table($headers, $rows);

        // Show summary
        $adminCount = $users->where('role', 'admin')->count();
        $userCount = $users->where('role', 'user')->count();
        $verifiedCount = $users->whereNotNull('email_verified_at')->count();

        $this->info('');
        $this->info('📊 Summary:');
        if ($showAll) {
            $this->info("   Total Users: {$users->count()}");
            $this->info("   Admin Users: $adminCount");
            $this->info("   Regular Users: $userCount");
        } else {
            $this->info("   Admin Users: $adminCount");
        }
        $this->info("   Verified Users: $verifiedCount");

        // Show helpful commands
        $this->info('');
        $this->info('💡 Helpful Commands:');
        $this->info('   php artisan admin:create          - Create new admin user');
        if (!$showAll) {
            $this->info('   php artisan admin:list --all      - Show all users');
        }

        return 0;
    }
}
