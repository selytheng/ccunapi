<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gallery_detail', function (Blueprint $table) {
            $table->id();
            $table->bigint('gallery_id');
            $table->varchar('picture');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gallery_detail');
    }
};
