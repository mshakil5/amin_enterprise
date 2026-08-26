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
            // Adding the new string columns after the 'status' column
            // ->nullable() is used so existing rows don't break when you run the migration
            $table->string('checked_by')->nullable()->after('status');
            $table->string('received_by')->nullable()->after('checked_by');
            $table->string('approved_by')->nullable()->after('received_by');
            $table->string('proprietor')->nullable()->after('approved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Rollback: drop the columns if you migrate:fresh or rollback
            $table->dropColumn(['checked_by', 'received_by', 'approved_by', 'proprietor']);
        });
    }
};