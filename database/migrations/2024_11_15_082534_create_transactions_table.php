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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade'); 
            $table->unsignedBigInteger('advance_payment_id')->nullable();
            $table->foreign('advance_payment_id')->references('id')->on('advance_payments')->onDelete('cascade'); 

            $table->unsignedBigInteger('program_id')->nullable();
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade'); 
            $table->unsignedBigInteger('program_detail_id')->nullable();
            $table->foreign('program_detail_id')->references('id')->on('program_details')->onDelete('cascade'); 
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade'); 
            $table->string('payment_type')->nullable();
            $table->string('tran_type')->nullable();
            $table->string('challan_no')->nullable();
            $table->double('contact_amount',10,2)->default(0)->nullable();
            $table->double('rcv_amount',10,2)->default(0)->nullable();
            $table->double('due_amount',10,2)->default(0)->nullable();
            $table->double('other_cost',10,2)->default(0)->nullable();
            $table->longText('note')->nullable();
            $table->boolean('status')->default(1); 
            // 1= new or processing, 0= cancel, 2=hold, 3=complete 
            $table->string('updated_by')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
