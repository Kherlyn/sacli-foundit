<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('chat_messages', function (Blueprint $table) {
      $table->id();
      $table->foreignId('chat_session_id')->constrained('chat_sessions')->onDelete('cascade');
      $table->enum('sender_type', ['user', 'admin']);
      $table->unsignedBigInteger('sender_id');
      $table->text('message');
      $table->timestamp('read_at')->nullable();
      $table->timestamps();

      // Indexes for performance
      $table->index('chat_session_id');
      $table->index(['sender_type', 'sender_id']);
      $table->index('created_at');
      $table->index('read_at');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('chat_messages');
  }
};
