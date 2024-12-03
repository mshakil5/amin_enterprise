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
        Schema::create('advance_payments', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade'); 
            $table->unsignedBigInteger('petrol_pump_id')->nullable();
            $table->foreign('petrol_pump_id')->references('id')->on('petrol_pumps')->onDelete('cascade'); 
            $table->string('fuel_rate')->nullable();
            $table->string('fuelqty')->nullable();
            $table->string('fueltoken')->nullable();
            $table->double('fuelamount',10,2)->default(0)->nullable();
            $table->double('cashamount',10,2)->default(0)->nullable();
            $table->double('amount',10,2)->default(0)->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('payment_type')->nullable();

            $table->unsignedBigInteger('program_id')->nullable();
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade'); 
            $table->unsignedBigInteger('program_detail_id')->nullable();
            $table->foreign('program_detail_id')->references('id')->on('program_details')->onDelete('cascade'); 

            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade'); 
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
        Schema::dropIfExists('advance_payments');
    }
};
