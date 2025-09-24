<?php

namespace App\Console\Commands;

use App\Models\User;
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
                            {--force : Force creation even if user exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user for the SACLI FOUNDIT application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 SACLI FOUNDIT - Admin User Creation');
        $this->info('=====================================');

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
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('❌ Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("   • $error");
            }
            return 1;
        }

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            if (!$this->option('force')) {
                if ($existingUser->isAdmin()) {
                    $this->warn("⚠️  Admin user with email '$email' already exists!");
                    return 1;
                } else {
                    $this->warn("⚠️  Regular user with email '$email' already exists!");

                    if ($this->confirm('Do you want to promote this user to admin?')) {
                        $existingUser->update(['role' => 'admin']);
                        $this->info("✅ User '$email' has been promoted to admin!");
                        $this->displayUserInfo($existingUser);
                        return 0;
                    }
                    return 1;
                }
            } else {
                // Force update existing user
                $existingUser->update([
                    'name' => $name,
                    'password' => Hash::make($password),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]);

                $this->info("✅ Existing user '$email' has been updated and set as admin!");
                $this->displayUserInfo($existingUser);
                return 0;
            }
        }

        // Create new admin user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(), // Auto-verify admin users
            ]);

            $this->info('✅ Admin user created successfully!');
            $this->displayUserInfo($user);

            // Show login instructions
            $this->info('');
            $this->info('🔐 Login Instructions:');
            $this->info("   Email: $email");
            $this->info('   Password: [as provided]');
            $this->info('   Admin Panel: /admin/dashboard');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to create admin user:');
            $this->error("   {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Display user information in a formatted way
     */
    private function displayUserInfo(User $user): void
    {
        $this->info('');
        $this->info('👤 Admin User Details:');
        $this->info("   ID: {$user->id}");
        $this->info("   Name: {$user->name}");
        $this->info("   Email: {$user->email}");
        $this->info("   Role: {$user->role}");
        $this->info("   Created: {$user->created_at->format('Y-m-d H:i:s')}");
        $this->info("   Email Verified: " . ($user->email_verified_at ? 'Yes' : 'No'));
    }
}
