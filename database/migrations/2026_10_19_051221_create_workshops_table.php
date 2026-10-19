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
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('speaker_id')->nullable()->constrained('speakers')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('banner_path')->nullable();
            $table->enum('type', ['webinar', 'workshop', 'bootcamp'])->default('webinar');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('timezone')->default('WIB');
            $table->enum('platform', ['zoom', 'gmeet', 'youtube', 'offline'])->default('zoom');
            $table->string('meeting_url')->nullable();
            $table->string('meeting_password')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('original_price', 12, 2)->nullable();
            $table->integer('quota')->nullable();
            $table->integer('remaining_quota')->nullable();
            $table->enum('status', ['draft', 'published', 'ongoing', 'completed', 'cancelled'])->default('published');
            $table->boolean('certificate_enabled')->default(true);
            $table->string('recording_url')->nullable();
            $table->string('materials_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshops');
    }
};
