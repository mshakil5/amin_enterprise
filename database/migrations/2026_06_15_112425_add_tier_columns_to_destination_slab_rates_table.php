<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('destination_slab_rates', function (Blueprint $table) {
            // 1. Add Client and Vendor relations
            $table->unsignedBigInteger('client_id')->nullable()->after('id');
            $table->unsignedBigInteger('vendor_id')->nullable()->after('client_id');

            // 2. Add new tier columns
            $table->decimal('tier_min_qty', 10, 2)->default(0)->nullable()->after('ghat_id');
            $table->decimal('tier_max_qty', 10, 2)->nullable()->after('tier_min_qty'); // Nullable means infinity (e.g., "15-up")
            $table->decimal('tier_rate', 10, 2)->nullable()->after('tier_max_qty');
        });

        // IMPORTANT: Backfill old data so they are linked to BSRM (client_id = 3)
        // This is safe because it only updates where client_id is currently NULL/0
        DB::statement('UPDATE destination_slab_rates SET client_id = 3 WHERE client_id IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destination_slab_rates', function (Blueprint $table) {
            //
        });
    }
};
