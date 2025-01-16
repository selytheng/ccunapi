<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('workshop', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('major_id');
            $table->foreign('major_id')->references('id')->on('majors')->onDelete('cascade');
            $table->unsignedBigInteger('year_id');
            $table->foreign('year_id')->references('id')->on('years')->onDelete('cascade');
            $table->string('name', 30);
            $table->text('description');
            $table->text('image')->nullable();
            $table->string('link_registeration', 30);
            $table->timestamps();
        });
    }

 
    public function down(): void
    {
        Schema::dropIfExists('workshop');
    }
};
