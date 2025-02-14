<?php

namespace App\Observers;

use App\Models\AuditoryType;
use App\Models\Fase;
use App\Models\Document;
use App\Models\Status;
use Illuminate\Support\Facades\Log;


class AuditoryTypeObserver
{
    /**
     * Handle the AuditoryType "created" event.
     *
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return void
     */
    public function created(AuditoryType $auditoryType)
    {
        $status = Status::where('key', 'waiting')->first();
        Fase::create([
            'name' => 'Fase 1', 'description' => 'Descripcion de la fase 1', 'auditory_type_id' => $auditoryType->id, 'status_id' =>  $status->id
        ]);
    }

    /**
     * Handle the AuditoryType "updated" event.
     *
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return void
     */
    public function updated(AuditoryType $auditoryType)
    {
        //
    }

    /**
     * Handle the AuditoryType "deleted" event.
     *
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return void
     */
    public function deleted(AuditoryType $auditoryType)
    {
        //
    }

    /**
     * Handle the AuditoryType "restored" event.
     *
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return void
     */
    public function restored(AuditoryType $auditoryType)
    {
        //
    }

    /**
     * Handle the AuditoryType "force deleted" event.
     *
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return void
     */
    public function forceDeleted(AuditoryType $auditoryType)
    {
        //
    }
}
