<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image');
            $table->json('gallery')->nullable(); // Store multiple image paths as JSON
            $table->text('description');
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->string('location');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}
