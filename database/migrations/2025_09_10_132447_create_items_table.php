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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['lost', 'found']);
            $table->enum('status', ['pending', 'verified', 'rejected', 'resolved'])->default('pending');
            $table->string('location');
            $table->date('date_occurred');
            $table->json('contact_info');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('admin_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'type']);
            $table->index(['category_id', 'status']);

            // Add fulltext index only for MySQL/MariaDB
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText(['title', 'description']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
