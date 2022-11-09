<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsernameToUseridMomlistpicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('mom_list_pics');
        Schema::create('mom_list_pics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mom_list_id');
            $table->foreign('mom_list_id')->references('id')->on('mom_lists')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['mom_list_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mom_list_pics');
        Schema::create('mom_list_pics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mom_list_id');
            $table->foreign('mom_list_id')->references('id')->on('mom_lists')->onDelete('cascade');
            $table->string('username');
            $table->foreign('username')->references('username')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['mom_list_id','username']);
        });
    }
}
