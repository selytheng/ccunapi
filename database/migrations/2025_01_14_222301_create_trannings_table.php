<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->varchar('title');
            $table->varchar('image');
            $table->foreignId('gallery_id')->constrained();
            $table->varchar('description');
            $table->foreignId('partner_id')->constrained();
            $table->varchar('location');
            $table->varchar('status');
            $table->varchar('start_date');
            $table->varchar('end_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trainings');
    }
};
