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
        Schema::create('generating_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade'); 
            $table->unsignedBigInteger('program_detail_id')->nullable();
            $table->foreign('program_detail_id')->references('id')->on('program_details')->onDelete('cascade');
            $table->string('header_id')->nullable();
            $table->string('date')->nullable();
            $table->string('truck_number')->nullable();
            $table->string('destination')->nullable();
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('challan_qty')->nullable();
            $table->string('trip_number')->nullable();
            $table->string('trip_qty')->nullable();
            $table->string('before_freight_amount')->nullable();
            $table->string('after_freight_amount')->nullable();
            $table->string('additional_claim')->nullable();
            $table->string('final_trip_amount')->nullable();
            $table->string('remark_by_transporter')->nullable();
            $table->string('rental_mode')->nullable();
            $table->string('mode_of_trip')->nullable();
            $table->string('rate_type')->nullable();
            $table->string('sales_region')->nullable();
            $table->string('wings')->nullable();
            $table->string('lc_no')->nullable();
            $table->string('vessel_name')->nullable();
            $table->string('batch_no')->nullable();
            $table->string('billing_ou')->nullable();
            $table->string('billing_legal_entity')->nullable();
            $table->string('bill_no')->nullable();
            $table->string('transaction_status')->nullable();
            $table->string('billing_status')->nullable();
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
        Schema::dropIfExists('generating_bills');
    }
};
