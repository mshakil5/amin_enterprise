<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bill_receives', function (Blueprint $table) {
            // Add program_id after client_id
            $table->unsignedBigInteger('program_id')->nullable()->after('client_id');
        });
    }

    public function down()
    {
        Schema::table('bill_receives', function (Blueprint $table) {
            $table->dropColumn('program_id');
        });
    }
};
