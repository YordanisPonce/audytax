<?php

namespace App\Observers;

use App\Models\Fase;
use App\Models\Status;
use App\Traits\Notify;

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
        $status = Status::where('key', 'waiting')->first();
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
        if ($fase && isset($fase->qualityControl)) {
            $label = $fase->status->label;
            $this->notify("Ha cambiado la fase $fase->name a el estado de $label", $fase->qualityControl);
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
        //
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
