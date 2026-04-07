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
        Schema::table('bill_receives', function (Blueprint $table) {
            $table->unsignedBigInteger('chart_of_account_id')->nullable()->after('client_id');
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bill_receives', function (Blueprint $table) {
            $table->dropForeign(['chart_of_account_id']);
        });
    }


};
