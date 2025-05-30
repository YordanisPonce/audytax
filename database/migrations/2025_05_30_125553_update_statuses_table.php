<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Renombrar claves y etiquetas existentes
        DB::table('statuses')->where('key', 'waiting')
            ->update(['key' => 'open', 'label' => 'Abierto']);

        DB::table('statuses')->where('key', 'processing')
            ->update(['key' => 'waiting_review', 'label' => 'Esperando revisión']);

        DB::table('statuses')->where('key', 'complete')
            ->update(['key' => 'accepted', 'label' => 'Aceptado']);

        // Insertar el nuevo estado "Rechazado"
        DB::table('statuses')->insert([
            'key'   => 'rejected',
            'label' => 'Rechazado',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        // Opcional: revertir a como estaba antes
        DB::table('statuses')->where('key', 'open')
            ->update(['key' => 'waiting', 'label' => 'En espera']);

        DB::table('statuses')->where('key', 'waiting_review')
            ->update(['key' => 'processing', 'label' => 'Procesando']);

        DB::table('statuses')->where('key', 'accepted')
            ->update(['key' => 'complete', 'label' => 'Completado']);

        DB::table('statuses')->where('key', 'rejected')->delete();
    }
};
