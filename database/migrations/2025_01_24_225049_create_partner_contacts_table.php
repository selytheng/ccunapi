<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerContactsTable extends Migration
{
    public function up()
    {
        Schema::create('partner_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->unique()->constrained('partners')->onDelete('cascade');
            $table->json('phone_number')->nullable(); // Array field for phone numbers
            $table->json('email')->nullable(); // Array field for emails
            $table->string('location_link')->nullable();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->string('moodle_link')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('partner_contacts');
    }
}
