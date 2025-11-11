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
        Schema::table('users', function (Blueprint $table) {
            $table->string('course', 100)->nullable()->after('email');
            $table->tinyInteger('year')->unsigned()->nullable()->after('course');
            $table->index(['course', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['course', 'year']);
            $table->dropColumn(['course', 'year']);
        });
    }
};
