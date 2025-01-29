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
        Schema::create('trainings', function (Blueprint $table) { // Use a different table name if needed
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->json('gallery')->nullable();
            $table->text('description');
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->json('co_host')->nullable();
            $table->json('sponsor')->nullable();
            $table->string('location');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
