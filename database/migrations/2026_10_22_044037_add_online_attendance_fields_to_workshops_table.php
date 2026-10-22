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
        Schema::table('workshops', function (Blueprint $table): void {
            $table->string('attendance_mode', 20)->default('token')->after('meeting_password');
            $table->string('attendance_token', 50)->nullable()->after('attendance_mode');
            $table->boolean('attendance_is_active')->default(true)->after('attendance_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table): void {
            $table->dropColumn(['attendance_mode', 'attendance_token', 'attendance_is_active']);
        });
    }
};
