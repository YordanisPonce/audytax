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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('url')->nullable();
            $table->string('original_name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_approved')->default(false);
            
            $table->bigInteger('document_id')->unsigned()->nullable();
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
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
        Schema::dropIfExists('files');
    }
};