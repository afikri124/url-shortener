<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByToPublicholidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wh_public_holidays', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('created_id')->nullable();
            $table->foreign('created_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_public_holidays', function (Blueprint $table) {
            //
            $table->dropForeign('users_created_id_foreign');
            $table->dropColumn('created_id');
        });
    }
}
