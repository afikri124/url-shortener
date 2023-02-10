<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdmesinToWhAttendances extends Migration
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
            $table->dropPrimary('uid');
            $table->integer('idmesin')->nullable();
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
            $table->dropColumn('idmesin');
            $table->primary('uid');
        });
    }
}
