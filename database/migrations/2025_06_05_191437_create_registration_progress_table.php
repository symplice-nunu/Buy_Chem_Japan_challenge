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
        Schema::create('registration_progress', function (Blueprint $table) {
            $table->id();
            $table->uuid('registration_id')->unique();
            $table->string('email')->unique()->nullable();
            $table->integer('current_step')->default(1);
            $table->json('step_data')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_progress');
    }
};
