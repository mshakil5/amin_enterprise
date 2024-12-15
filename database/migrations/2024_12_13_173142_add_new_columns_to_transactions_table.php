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
            
            $table->string('bill_number')->after('client_id')->nullable();
            $table->unsignedBigInteger('bill_receive_id')->after('client_id')->nullable();
            $table->foreign('bill_receive_id')->references('id')->on('bill_receives')->onDelete('cascade');
            $table->unsignedBigInteger('chart_of_account_id')->after('client_id')->nullable();
            $table->foreign('chart_of_account_id')->references('id')->on('chart_of_accounts');


            $table->string('table_type')->after('client_id')->nullable();
            $table->string('ref')->after('client_id')->nullable();
            $table->longText('description')->after('client_id')->nullable();
            $table->decimal('tax_rate', 10, 2)->after('amount')->default(0)->nullable();
            $table->decimal('tax_amount', 10, 2)->after('amount')->default(0)->nullable();
            $table->decimal('vat_rate', 10, 2)->after('amount')->default(0)->nullable();
            $table->decimal('vat_amount', 10, 2)->after('amount')->default(0)->nullable();
            $table->double('discount', 10, 2)->after('amount')->default(0);
            $table->decimal('at_amount', 10, 2)->after('amount')->default(0)->nullable();
            $table->string('liability_id')->after('note')->nullable();
            $table->string('asset_id')->after('note')->nullable();
            $table->string('liablity_id')->after('note')->nullable();
            $table->string('income_id')->after('note')->nullable();
            $table->string('expense_id')->after('note')->nullable();
            $table->string('equity_id')->after('note')->nullable();
            $table->string('document')->after('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
