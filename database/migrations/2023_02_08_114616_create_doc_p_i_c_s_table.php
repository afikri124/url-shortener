<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocPICSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc_p_i_c_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doc_id');
            $table->foreign('doc_id')->references('id')->on('doc_systems')->onDelete('cascade');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('doc_departments')->onDelete('set null');
            $table->unsignedBigInteger('pic_id');
            $table->foreign('pic_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doc_p_i_c_s');
    }
}
