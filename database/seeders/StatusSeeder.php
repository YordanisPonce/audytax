<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run()
    {
        // Limpia la tabla y reinicia el contador de IDs
        Status::truncate();

        $statuses = [
            ['key' => 'open',           'label' => 'Abierto'],
            ['key' => 'waiting_review', 'label' => 'Esperando revisión'],
            ['key' => 'accepted',       'label' => 'Aceptado'],
            ['key' => 'rejected',       'label' => 'Rechazado'],
        ];

        foreach ($statuses as $attrs) {
            Status::create($attrs);
        }
    }
}
