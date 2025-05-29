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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('comment')->nullable();

            $table->bigInteger('status id')->unsigned()->nullable();
            $table->foreign('status id')->references('id')->on('statuses')->onDelete('cascade');

            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('fase_id')->unsigned()->nullable();
            $table->foreign('fase_id')->references('id')->on('fases')->onDelete('cascade');

            $table->bigInteger('quality_control_id')->unsigned()->nullable();
            $table->foreign('quality_control_id')->references('id')->on('quality_controls')->onDelete('cascade');

            $table->bigInteger('comment_id')->unsigned()->nullable();
            $table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');
           

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
        Schema::dropIfExists('comments');
    }
};
