<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class ListAdminUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all admin accounts in the SACLI FOUNDIT application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('👥 SACLI FOUNDIT - Admin Account Management');
        $this->info('==========================================');

        $admins = Admin::orderBy('created_at', 'desc')->get();
        $this->info('🔧 Admin Accounts:');

        if ($admins->isEmpty()) {
            $this->warn('⚠️  No admin accounts found in the system.');
            $this->info('');
            $this->info('💡 To create an admin account, run:');
            $this->info('   php artisan admin:create');
            return 0;
        }

        // Prepare table data
        $headers = ['ID', 'Name', 'Email', 'Verified', 'Created', 'Updated'];
        $rows = [];

        foreach ($admins as $admin) {
            $rows[] = [
                $admin->id,
                $admin->name,
                $admin->email,
                $admin->email_verified_at ? '✅ Yes' : '❌ No',
                $admin->created_at->format('Y-m-d H:i'),
                $admin->updated_at->format('Y-m-d H:i'),
            ];
        }

        $this->table($headers, $rows);

        // Show summary
        $verifiedCount = $admins->whereNotNull('email_verified_at')->count();

        $this->info('');
        $this->info('📊 Summary:');
        $this->info("   Total Admin Accounts: {$admins->count()}");
        $this->info("   Verified Accounts: $verifiedCount");

        // Show helpful commands
        $this->info('');
        $this->info('💡 Helpful Commands:');
        $this->info('   php artisan admin:create          - Create new admin account');
        $this->info('   php artisan admin:delete          - Delete an admin account');

        return 0;
    }
}
