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
        Schema::create('bill_receives', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->string('bill_number')->nullable();
            $table->unsignedBigInteger('mother_vassel_id')->nullable();
            $table->foreign('mother_vassel_id')->references('id')->on('mother_vassels')->onDelete('cascade'); 
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade'); 
            $table->string('rcv_type')->nullable();
            $table->string('qty')->nullable();
            $table->double('total_amount',10,2)->default(0)->nullable();
            $table->double('maintainance',10,2)->default(0)->nullable();
            $table->double('scale_charge',10,2)->default(0)->nullable();
            $table->double('other_exp',10,2)->default(0)->nullable();
            $table->double('other_rcv',10,2)->default(0)->nullable();
            $table->double('net_amount',10,2)->default(0)->nullable();
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
        Schema::dropIfExists('bill_receives');
    }
};
