<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhManualTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wh_manual_types', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('absent')->default(0);
        });

        Schema::table('wh_manual_atds', function (Blueprint $table) {
            $table->renameColumn('type', 'type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wh_manual_atds', function (Blueprint $table) {
            $table->renameColumn('type_id', 'type');
        });
        Schema::dropIfExists('wh_manual_types');
    }
}
