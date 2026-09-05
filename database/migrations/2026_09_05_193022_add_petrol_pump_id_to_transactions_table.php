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
        Schema::table('transactions', function (Blueprint $table) {
            // Add the column after fuel_bill_id (or change to 'chart_of_account_id' if you prefer)
            // It is nullable so existing transactions don't break
            $table->unsignedBigInteger('petrol_pump_id')->nullable()->after('fuel_bill_id');
            
            // Add foreign key constraint
            $table->foreign('petrol_pump_id')
                  ->references('id')
                  ->on('petrol_pumps')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop foreign key first, then the column
            $table->dropForeign(['petrol_pump_id']);
            $table->dropColumn('petrol_pump_id');
        });
    }
};