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
        Schema::create('challan_rate_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
            $table->unsignedBigInteger('program_detail_id')->nullable();
            $table->foreign('program_detail_id')->references('id')->on('program_details')->onDelete('cascade');
            $table->string('challan_no')->nullable();

            $table->string('qty')->nullable();
            $table->double('rate_per_unit',10,2)->default(0)->nullable();
            $table->double('total',10,2)->default(0)->nullable();

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
        Schema::dropIfExists('challan_rate_logs');
    }
};
