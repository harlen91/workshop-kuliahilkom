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
            if (! Schema::hasColumn('workshops', 'attendance_mode')) {
                $table->string('attendance_mode', 20)->default('token');
            }
            if (! Schema::hasColumn('workshops', 'attendance_token')) {
                $table->string('attendance_token', 50)->nullable();
            }
            if (! Schema::hasColumn('workshops', 'attendance_is_active')) {
                $table->boolean('attendance_is_active')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table): void {
            $columnsToDrop = [];
            foreach (['attendance_mode', 'attendance_token', 'attendance_is_active'] as $column) {
                if (Schema::hasColumn('workshops', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
