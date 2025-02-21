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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            // Basic Information
            $table->string('species');
            $table->string('name');
            $table->string('breed');
            $table->string('color');
            $table->integer('age');
            $table->decimal('weight', 5, 2);
            $table->text('description')->nullable();

            // Additional Common Columns
            $table->enum('gender', ['male', 'female']);
            $table->date('date_of_birth')->nullable();
            $table->string('microchip_id')->unique()->nullable(); // Unique identifier for the dog
            $table->string('insurance_policy_number')->nullable(); // Insurance policy number
            $table->boolean('is_vaccinated')->default(false);
            $table->boolean('is_neutered')->default(false);
            $table->string('profile_image_url')->nullable(); // URL to the dog's profile image

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('name');
            $table->index('breed');
            $table->index('microchip_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};