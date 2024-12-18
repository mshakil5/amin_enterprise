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
        Schema::table('program_details', function (Blueprint $table) {
            
            $table->unsignedBigInteger('vendor_sequence_number_id')->after('ghat_id')->nullable();
            $table->foreign('vendor_sequence_number_id')->references('id')->on('vendor_sequence_numbers');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_details', function (Blueprint $table) {
            //
        });
    }
};
