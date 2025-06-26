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
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('auditory_type_id')
                  ->nullable()
                  ->constrained('auditory_types')
                  ->onDelete('cascade'); // Si se borra un AuditoryType, borra sus documentos
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['auditory_type_id']);
            $table->dropColumn('auditory_type_id');
        });
    }
};
