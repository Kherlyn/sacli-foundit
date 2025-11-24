<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    // Copy admin users from users table to admins table
    $adminUsers = DB::table('users')
      ->where('role', 'admin')
      ->get();

    foreach ($adminUsers as $user) {
      // Check if admin already exists (by email)
      $existingAdmin = DB::table('admins')
        ->where('email', $user->email)
        ->first();

      if (!$existingAdmin) {
        DB::table('admins')->insert([
          'name' => $user->name,
          'email' => $user->email,
          'password' => $user->password, // Already hashed
          'email_verified_at' => $user->email_verified_at,
          'remember_token' => $user->remember_token,
          'created_at' => $user->created_at,
          'updated_at' => $user->updated_at,
        ]);
      }
    }

    // Add a deprecated flag to the role column instead of dropping it
    // This allows for a gradual migration and rollback if needed
    Schema::table('users', function (Blueprint $table) {
      $table->boolean('role_deprecated')->default(false)->after('role');
    });

    // Mark all admin users as deprecated
    DB::table('users')
      ->where('role', 'admin')
      ->update(['role_deprecated' => true]);
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    // Remove the deprecated flag
    Schema::table('users', function (Blueprint $table) {
      $table->dropColumn('role_deprecated');
    });

    // Optionally, you could delete the migrated admins from the admins table
    // but we'll leave them for safety
  }
};
