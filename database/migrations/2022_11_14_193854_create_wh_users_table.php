<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_users', function (Blueprint $table) {
            $table->uuid('uid')->primary();
            $table->string('username',24)->unique()->nullable();
            $table->string('username_old', 8)->nullable();
            $table->string('name',24)->nullable();
            $table->string('role',24)->nullable()->default(0);
            $table->string('password',8)->nullable();
            $table->string('cardno',10)->nullable();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('wh_users');
    }
}
