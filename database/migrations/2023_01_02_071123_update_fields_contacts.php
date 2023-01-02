<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // delete all contacts records
        DB::table('contacts')->delete();
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('expiration_date');
            $table->string('policy_holder')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date');
            $table->string('registration_number')->nullable();
        });
    }

    public function down()
    {
        //
    }
};
