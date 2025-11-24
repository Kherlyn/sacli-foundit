<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class DeleteAdminUser extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'admin:delete 
                            {--id= : The ID of the admin account to delete}
                            {--email= : The email of the admin account to delete}
                            {--force : Skip confirmation prompt}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Delete an admin account from the SACLI FOUNDIT application';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $this->info('🗑️  SACLI FOUNDIT - Admin Account Deletion');
    $this->info('=========================================');

    // Get admin to delete
    $admin = null;

    if ($this->option('id')) {
      $admin = Admin::find($this->option('id'));
      if (!$admin) {
        $this->error("❌ Admin account with ID {$this->option('id')} not found.");
        return 1;
      }
    } elseif ($this->option('email')) {
      $admin = Admin::where('email', $this->option('email'))->first();
      if (!$admin) {
        $this->error("❌ Admin account with email '{$this->option('email')}' not found.");
        return 1;
      }
    } else {
      // Interactive mode - show list and ask for selection
      $admins = Admin::orderBy('created_at', 'desc')->get();

      if ($admins->isEmpty()) {
        $this->warn('⚠️  No admin accounts found in the system.');
        return 0;
      }

      $this->info('Available admin accounts:');
      $this->info('');

      $headers = ['ID', 'Name', 'Email', 'Created'];
      $rows = [];

      foreach ($admins as $a) {
        $rows[] = [
          $a->id,
          $a->name,
          $a->email,
          $a->created_at->format('Y-m-d H:i'),
        ];
      }

      $this->table($headers, $rows);

      $adminId = $this->ask('Enter the ID of the admin account to delete (or press Enter to cancel)');

      if (empty($adminId)) {
        $this->info('Operation cancelled.');
        return 0;
      }

      $admin = Admin::find($adminId);

      if (!$admin) {
        $this->error("❌ Admin account with ID $adminId not found.");
        return 1;
      }
    }

    // Display admin info
    $this->info('');
    $this->info('Admin account to be deleted:');
    $this->info("   ID: {$admin->id}");
    $this->info("   Name: {$admin->name}");
    $this->info("   Email: {$admin->email}");
    $this->info("   Created: {$admin->created_at->format('Y-m-d H:i:s')}");

    // Confirm deletion
    if (!$this->option('force')) {
      $this->warn('');
      $this->warn('⚠️  WARNING: This action cannot be undone!');

      if (!$this->confirm('Are you sure you want to delete this admin account?')) {
        $this->info('Operation cancelled.');
        return 0;
      }
    }

    // Delete admin
    try {
      $admin->delete();
      $this->info('');
      $this->info("✅ Admin account '{$admin->email}' has been deleted successfully!");
      return 0;
    } catch (\Exception $e) {
      $this->error('❌ Failed to delete admin account:');
      $this->error("   {$e->getMessage()}");
      return 1;
    }
  }
}
