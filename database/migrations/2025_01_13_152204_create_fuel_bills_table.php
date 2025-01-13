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
        Schema::create('fuel_bills', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->string('qty')->nullable();
            $table->integer('markqty')->nullable();
            $table->integer('notmarkqty')->nullable();
            $table->string('bill_number')->nullable();
            $table->string('unique_id')->nullable();
            $table->string('vehicle_count')->nullable();
            $table->string('sequence')->nullable();
            $table->unsignedBigInteger('petrol_pump_id')->nullable();
            $table->foreign('petrol_pump_id')->references('id')->on('petrol_pumps')->onDelete('cascade');
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
        Schema::dropIfExists('fuel_bills');
    }
};
