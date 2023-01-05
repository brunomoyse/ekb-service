<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // if messages table exists: drop it
        if (Schema::hasTable('messages')) {
            Schema::drop('messages');
        }
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('message_body')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
