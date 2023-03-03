<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroupidToWhUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_users', function (Blueprint $table) {
            //
            $table->uuid('group_id')->nullable()->default('JF');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_users', function (Blueprint $table) {
            //
            $table->dropColumn('group_id');
        });
    }
}
