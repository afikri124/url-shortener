<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameGroupInWifiusersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wifi_users', function (Blueprint $table) {
            //
            $table->renameColumn('group', 'wifi_group');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wifi_users', function (Blueprint $table) {
            //
            $table->renameColumn('wifi_group', 'group');
        });
    }
}
