<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            [
                'key' => 'waiting', 'label' => 'En espera'
            ],
            [
                'key' => 'processing', 'label' => 'Procesando'
            ],
            [
                'key' => 'complete', 'label' => 'Completado'
            ],
        ];

        Status::insert($statuses);
    }
}
