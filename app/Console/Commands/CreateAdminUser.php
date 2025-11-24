<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create 
                            {--name= : The name of the admin user}
                            {--email= : The email address of the admin user}
                            {--password= : The password for the admin user}
                            {--force : Force creation even if admin exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin account for the SACLI FOUNDIT application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 SACLI FOUNDIT - Admin Account Creation');
        $this->info('=========================================');

        // Get user input
        $name = $this->option('name') ?: $this->ask('Enter admin name');
        $email = $this->option('email') ?: $this->ask('Enter admin email');
        $password = $this->option('password') ?: $this->secret('Enter admin password');

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('❌ Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("   • $error");
            }
            return 1;
        }

        // Check if admin already exists
        $existingAdmin = Admin::where('email', $email)->first();

        if ($existingAdmin) {
            if (!$this->option('force')) {
                $this->warn("⚠️  Admin account with email '$email' already exists!");
                return 1;
            } else {
                // Force update existing admin
                $existingAdmin->update([
                    'name' => $name,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                ]);

                $this->info("✅ Existing admin account '$email' has been updated!");
                $this->displayAdminInfo($existingAdmin);
                return 0;
            }
        }

        // Create new admin account
        try {
            $admin = Admin::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(), // Auto-verify admin accounts
            ]);

            $this->info('✅ Admin account created successfully!');
            $this->displayAdminInfo($admin);

            // Show login instructions
            $this->info('');
            $this->info('🔐 Login Instructions:');
            $this->info("   Email: $email");
            $this->info('   Password: [as provided]');
            $this->info('   Admin Login: /admin/login');
            $this->info('   Admin Panel: /admin/dashboard');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to create admin account:');
            $this->error("   {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Display admin information in a formatted way
     */
    private function displayAdminInfo(Admin $admin): void
    {
        $this->info('');
        $this->info('👤 Admin Account Details:');
        $this->info("   ID: {$admin->id}");
        $this->info("   Name: {$admin->name}");
        $this->info("   Email: {$admin->email}");
        $this->info("   Created: {$admin->created_at->format('Y-m-d H:i:s')}");
        $this->info("   Email Verified: " . ($admin->email_verified_at ? 'Yes' : 'No'));
    }
}
