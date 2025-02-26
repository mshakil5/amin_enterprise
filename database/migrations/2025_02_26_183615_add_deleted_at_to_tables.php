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
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('advance_payments', function (Blueprint $table) {
            $table->softDeletes(); // Adds deleted_at column
        });

        // Repeat for other tables if needed
        Schema::table('bill_receives', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('challan_rates', function (Blueprint $table) {
            $table->softDeletes(); // Adds deleted_at column
        });

        // Repeat for other tables if needed
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->softDeletes(); // Adds deleted_at column
        });

        // Repeat for other tables if needed
        Schema::table('client_rates', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('destinations', function (Blueprint $table) {
            $table->softDeletes(); // Adds deleted_at column
        });

        // Repeat for other tables if needed
        Schema::table('destination_slab_rates', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('fuel_bills', function (Blueprint $table) {
            $table->softDeletes(); // Adds deleted_at column
        });

        // Repeat for other tables if needed
        Schema::table('ghats', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('lighter_vassels', function (Blueprint $table) {
            $table->softDeletes(); // Adds deleted_at column
        });

        // Repeat for other tables if needed
        Schema::table('mother_vassels', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('petrol_pumps', function (Blueprint $table) {
            $table->softDeletes(); // Adds deleted_at column
        });

        // Repeat for other tables if needed
        Schema::table('petty_cashes', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->softDeletes(); // Adds deleted_at column
        });

        // Repeat for other tables if needed
        Schema::table('program_destinations', function (Blueprint $table) {
            $table->softDeletes();
        });


        // Repeat for other tables if needed
        Schema::table('program_details', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Repeat for other tables if needed
        Schema::table('transactions', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Repeat for other tables if needed
        Schema::table('vendors', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Repeat for other tables if needed
        Schema::table('vendor_sequence_numbers', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
