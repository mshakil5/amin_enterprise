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
        Schema::create('mother_vassels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country_name')->nullable();
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->boolean('status')->default(1); //1=>Running, 2=>Completed
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
        Schema::dropIfExists('mother_vassels');
    }
};
