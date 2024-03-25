<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInoutToWhUserUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_user_units', function (Blueprint $table) {
            //
            $table->time('time_in')->default("08:00:00");
            $table->time('time_out')->default("16:00:00");
            $table->time('time_total')->default("08:00:00");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_user_units', function (Blueprint $table) {
            //
            $table->dropColumn('time_in');
            $table->dropColumn('time_out');
            $table->dropColumn('time_total');
        });
    }
}
