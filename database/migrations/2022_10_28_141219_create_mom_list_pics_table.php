<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMomListPicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mom_list_pics');
    }
}
