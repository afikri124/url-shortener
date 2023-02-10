<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueUidIdmesinToWhAttendances extends Migration
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
            $table->unique(['uid','idmesin'],'unique_uid_idmesin');
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
            $table->dropUnique('unique_uid_idmesin');
        });
    }
}
