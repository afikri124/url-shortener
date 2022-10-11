<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('job')->nullable();
            $table->char('gender')->nullable();
            $table->string('front_title')->nullable();
            $table->string('back_title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('job');
            $table->dropColumn('gender');
            $table->dropColumn('front_title');
            $table->dropColumn('back_title');
        });
    }
}
