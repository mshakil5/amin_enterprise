<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('program_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->nullable(); 
            $table->unsignedBigInteger('program_id')->nullable(); 
            $table->string('document')->nullable(); // file path
            $table->integer('total_truck')->nullable();
            $table->integer('total_challan')->nullable();
            $table->date('date')->nullable();
            $table->string('truck_numbers')->nullable(); // comma or bracket separated
            $table->unsignedBigInteger('mother_vassel_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('program_documents');
    }
};