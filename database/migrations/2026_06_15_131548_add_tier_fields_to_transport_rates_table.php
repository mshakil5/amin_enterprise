<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTierFieldsToTransportRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transport_rates', function (Blueprint $table) {
            // 1. Add Client and Vendor relations
            $table->unsignedBigInteger('client_id')->nullable()->after('id');
            $table->unsignedBigInteger('vendor_id')->nullable()->after('client_id');

            // 2. Add new tier columns
            $table->decimal('tier_min_qty', 10, 2)->default(0)->nullable()->after('ghat_id');
            $table->decimal('tier_max_qty', 10, 2)->nullable()->after('tier_min_qty'); // Nullable means infinity (e.g., "15-up")
            $table->decimal('tier_rate', 10, 2)->nullable()->after('tier_max_qty');
        });

        // OPTIONAL BUT RECOMMENDED: Backfill old data so they belong to BSRM (client_id = 3)
        // This only updates records where client_id is currently NULL
        DB::statement('UPDATE transport_rates SET client_id = 3 WHERE client_id IS NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transport_rates', function (Blueprint $table) {
            $table->dropColumn(['client_id', 'vendor_id', 'tier_min_qty', 'tier_max_qty', 'tier_rate']);
        });
    }
}