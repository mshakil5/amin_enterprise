<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create the column as nullable first to prevent errors with existing data
        Schema::table('vendor_sequence_numbers', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->after('vendor_id');
        });

        // 2. Update all existing records to point to Client ID 3
        DB::table('vendor_sequence_numbers')
            ->whereNull('client_id')
            ->update(['client_id' => 3]);

        // 3. Apply the foreign key constraint and make it non-nullable for future data
        Schema::table('vendor_sequence_numbers', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable(false)->change();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_sequence_numbers', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });
    }


};
