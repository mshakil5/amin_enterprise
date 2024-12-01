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
        Schema::create('program_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->unsignedBigInteger('destination_id')->nullable();
            $table->foreign('destination_id')->references('id')->on('destinations')->onDelete('cascade'); 
            $table->unsignedBigInteger('ghat_id')->nullable();
            $table->foreign('ghat_id')->references('id')->on('ghats')->onDelete('cascade'); 
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade'); 
            $table->unsignedBigInteger('program_id')->nullable();
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade'); 
            $table->unsignedBigInteger('program_detail_id')->nullable();
            $table->foreign('program_detail_id')->references('id')->on('program_details')->onDelete('cascade');
            $table->string('headerid')->nullable();
            $table->string('challan_no')->nullable();
            $table->string('dest_qty')->nullable();

            $table->double('line_charge',10,2)->default(0)->nullable();
            $table->double('carrying_bill',10,2)->default(0)->nullable();
            $table->double('scale_fee',10,2)->default(0)->nullable();
            $table->double('other_cost',10,2)->default(0)->nullable();

            $table->double('transportcost',10,2)->default(0)->nullable();
            $table->double('additional_cost',10,2)->default(0)->nullable();
            $table->double('advance',10,2)->default(0)->nullable();
            $table->double('due',10,2)->default(0)->nullable();

            $table->boolean('dest_status')->default(1); 
            // 1= new or processing, 0= cancel, 2=hold, 3=complete 
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
        Schema::dropIfExists('program_destinations');
    }
};
