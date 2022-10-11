<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_activities', function (Blueprint $table) {
            $table->id();
            $table->char('type')->nullable();
            $table->string('title');
            $table->string('sub_title');
            $table->date('date');
            $table->string('location');
            $table->string('host');
            $table->string('participant');
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
        Schema::dropIfExists('attendance_activities');
    }
}
