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
            $table->string('certificate_signer_name')->nullable()->after('certificate_enabled');
            $table->string('certificate_signer_title')->nullable()->after('certificate_signer_name');
            $table->string('certificate_signature_path')->nullable()->after('certificate_signer_title');
            $table->string('certificate_background_path')->nullable()->after('certificate_signature_path');
        });

        Schema::table('registrations', function (Blueprint $table): void {
            $table->index('certificate_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table): void {
            $table->dropIndex(['certificate_code']);
        });

        Schema::table('workshops', function (Blueprint $table): void {
            $table->dropColumn([
                'certificate_signer_name',
                'certificate_signer_title',
                'certificate_signature_path',
                'certificate_background_path',
            ]);
        });
    }
};
