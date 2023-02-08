<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doc_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('deadline')->nullable();
            $table->string('doc_path')->nullable();
            $table->uuid('status_id')->nullable();
            $table->foreign('status_id')->references('id')->on('doc_statuses')->onDelete('set null');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('doc_categories')->onDelete('cascade');
            $table->unsignedBigInteger('created_id');
            $table->foreign('created_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('updated_id')->nullable();
            $table->foreign('updated_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->text('remark');
            $table->text('histories');
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
        Schema::dropIfExists('doc_systems');
    }
}
