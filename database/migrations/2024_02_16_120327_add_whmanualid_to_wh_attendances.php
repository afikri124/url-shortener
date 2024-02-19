<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWhmanualidToWhAttendances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_attendances', function (Blueprint $table) {
            //
            $table->integer('wh_manual_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_attendances', function (Blueprint $table) {
            //
            $table->dropColumn('wh_manual_id');
        });
    }
}
