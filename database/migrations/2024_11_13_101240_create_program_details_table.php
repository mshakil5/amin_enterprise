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
        Schema::create('program_details', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->string('programid')->nullable();
            $table->string('consignmentno')->nullable();
            $table->string('headerid')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade'); 
            $table->unsignedBigInteger('mother_vassel_id')->nullable();
            $table->foreign('mother_vassel_id')->references('id')->on('mother_vassels')->onDelete('cascade'); 
            $table->unsignedBigInteger('lighter_vassel_id')->nullable();
            $table->foreign('lighter_vassel_id')->references('id')->on('lighter_vassels')->onDelete('cascade'); 
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade'); 
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade'); 
            $table->unsignedBigInteger('ghat_id')->nullable();
            $table->foreign('ghat_id')->references('id')->on('ghats')->onDelete('cascade'); 
            $table->unsignedBigInteger('destination_id')->nullable();
            $table->foreign('destination_id')->references('id')->on('destinations')->onDelete('cascade'); 
            $table->string('truck_number')->nullable();
            $table->string('qty')->nullable();
            $table->string('challan_no')->nullable();
            $table->string('line_charge')->nullable();
            $table->string('token_fee')->nullable();
            $table->string('party_name')->nullable();
            $table->double('rate_per_qty',10,2)->default(0)->nullable();
            $table->double('amount',10,2)->default(0)->nullable();
            $table->double('rcv_amount',10,2)->default(0)->nullable();
            $table->double('due_amount',10,2)->default(0)->nullable();
            $table->double('cost',10,2)->default(0)->nullable();
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
        Schema::dropIfExists('program_details');
    }
};
