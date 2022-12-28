<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('first_name')->nullable();
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('policy_number');
            $table->date('expiration_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contacts');
    }
};
