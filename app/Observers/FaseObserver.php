<?php

namespace App\Observers;

use App\Enums\StatusEnum;
use App\Models\Fase;
use App\Models\Status;
use App\Traits\Notify;
use Illuminate\Support\Facades\Log;
use Throwable;

class FaseObserver
{
    use Notify;
    /**
     * Handle the Fase "created" event.
     *
     * @param  \App\Models\Fase  $fase
     * @return void
     */
    public function created(Fase $fase)
    {
        $status = Status::where('key', StatusEnum::Open->value)->first();
        if ($status) {
            $fase->documents()->create(['name' => 'Documento 1', 'status_id' => $status->id]);
        }
    }

    /**
     * Handle the Fase "updated" event.
     *
     * @param  \App\Models\Fase  $fase
     * @return void
     */
    public function updated(Fase $fase)
    {
        // 0) Asegúrate de tener el QC
        if (! $qc = $fase->qualityControl) {
            return;
        }

        // 1) Cambio de estado
        if ($fase->wasChanged('status_id')) {
            $oldLabel = Status::find($fase->getOriginal('status_id'))->label;
            $newLabel = $fase->status->label;
            $msg = "El estado del grupo “{$fase->name}” cambió de “{$oldLabel}” a “{$newLabel}.”";

            try {
                $this->notify($msg, $qc);
            } catch (Throwable $e) {
                Log::warning("Notify fallo al cambiar estado de Fase {$fase->id}: " . $e->getMessage());
            }
        }

        // 2) Cambio de nombre
        if ($fase->wasChanged('name')) {
            $oldName = $fase->getOriginal('name');
            $newName = $fase->name;
            $msg = "El nombre del grupo cambió de “{$oldName}” a “{$newName}.”";

            try {
                $this->notify($msg, $qc);
            } catch (Throwable $e) {
                Log::warning("Notify fallo al cambiar nombre de Fase {$fase->id}: " . $e->getMessage());
            }
        }
    }
    /**
     * Handle the Fase "deleted" event.
     *
     * @param  \App\Models\Fase  $fase
     * @return void
     */
    public function deleted(Fase $fase)
    {
        if ($fase && isset($fase->qualityControl)) {
            $label = $fase->status->label;
            $this->notify("Ha borrado el grupo $fase->name en el estado de $label", $fase->qualityControl);
        }
    }

    /**
     * Handle the Fase "restored" event.
     *
     * @param  \App\Models\Fase  $fase
     * @return void
     */
    public function restored(Fase $fase)
    {
        //
    }

    /**
     * Handle the Fase "force deleted" event.
     *
     * @param  \App\Models\Fase  $fase
     * @return void
     */
    public function forceDeleted(Fase $fase)
    {
        //
    }
}
