<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMicrositeLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('microsite_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('microsite_id');
            $table->foreign('microsite_id')->references('id')->on('microsites')->onDelete('cascade');
            $table->string('title')->unique();
            $table->text('link');
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
        Schema::dropIfExists('microsite_links');
    }
}
