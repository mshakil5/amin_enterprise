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
        $tables = ['users', 'advance_payments', 'bill_receives', 'challan_rates', 'chart_of_accounts', 'clients', 'client_rates', 'destinations', 'destination_slab_rates', 'fuel_bills', 'ghats', 'lighter_vassels', 'mother_vassels', 'petrol_pumps', 'petty_cashes', 'programs', 'program_destinations', 'program_details', 'transactions', 'vendors', 'vendor_sequence_numbers'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('deleted_by')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multiple_tables', function (Blueprint $table) {
            //
        });
    }
};
