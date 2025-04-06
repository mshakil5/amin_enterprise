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
          $table->unsignedBigInteger('fuel_bill_id')->nullable()->after('id');
          $table->foreign('fuel_bill_id')->references('id')->on('fuel_bills')->onDelete('set null');
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
