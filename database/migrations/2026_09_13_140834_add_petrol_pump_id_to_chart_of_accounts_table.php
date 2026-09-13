<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPetrolPumpIdToChartOfAccountsTable extends Migration
{
    public function up()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->foreignId('petrol_pumps_id')->nullable()->after('sub_account_head')->constrained('petrol_pumps')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropForeign(['petrol_pumps_id']);
            $table->dropColumn('petrol_pumps_id');
        });
    }
}