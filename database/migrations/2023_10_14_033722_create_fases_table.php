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
        Schema::create('fases', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->text('description')->nullable();
         
            $table->bigInteger('auditory_type_id')->unsigned()->nullable();
            $table->foreign('auditory_type_id')->references('id')->on('auditory_types')->onDelete('cascade');
          
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
        Schema::dropIfExists('fases');
    }
};
