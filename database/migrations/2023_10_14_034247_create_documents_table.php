<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('url')->nullable();
            $table->text('description')->nullable();

            $table->bigInteger('fase_id')->unsigned()->nullable();
            $table->foreign('fase_id')->references('id')->on('fases')->onDelete('cascade');

            $table->bigInteger('quality_control_id')->unsigned()->nullable();
            $table->foreign('quality_control_id')->references('id')->on('quality_controls')->onDelete('cascade');
           
            $table->bigInteger('status_id')->unsigned()->nullable();
            $table->foreign('status_id')->references('id')->on('statuses')->onDelete('cascade');

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
        Schema::dropIfExists('documents');
    }
};
