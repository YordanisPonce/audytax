<?php

namespace App\Observers;

use App\Enums\QualityControlEnum;
use App\Enums\RoleEnum;
use App\Models\History;
use App\Models\QualityControl;
use App\Models\Status;
use App\Models\User;
use App\Notifications\Notify as NotificationsNotify;
use App\Traits\Notify;
use Illuminate\Support\Facades\Log;

class QualityControlObserver
{
    use Notify;
    /**
     * Handle the QualityControl "created" event.
     *
     * @param  \App\Models\QualityControl  $qualityControl
     * @return void
     */
    public function created(QualityControl $qualityControl)
    {
        $qualityControl->auditoryType->fases()->whereDoesntHave('qualityControl')->get()->map(function ($phase) use ($qualityControl) {
            $newPhase = $phase->replicate();
            $newPhase->quality_control_id = $qualityControl->id;
            $newPhase->save();
            $newPhase->createDocuments($phase);
            return $newPhase;
        });
        $this->notify('Ha creado un nuevo control de calidad', $qualityControl);
    }

    /**
     * Handle the QualityControl "updated" event.
     *
     * @param  \App\Models\QualityControl  $qualityControl
     * @return void
     */
    public function updated(QualityControl $qualityControl)
    {
        $label = $qualityControl->status->label;
        $message = "Ha cambiado el control de calidad $qualityControl->name a el estado de: $label";
        $this->notify($message, $qualityControl);
    }

    /**
     * Handle the QualityControl "deleted" event.
     *
     * @param  \App\Models\QualityControl  $qualityControl
     * @return void
     */
    public function deleted(QualityControl $qualityControl)
    {
        $this->notify('Ha eliminado un control de calidad', $qualityControl);
    }

    /**
     * Handle the QualityControl "restored" event.
     *
     * @param  \App\Models\QualityControl  $qualityControl
     * @return void
     */
    public function restored(QualityControl $qualityControl)
    {
        //
    }

    /**
     * Handle the QualityControl "force deleted" event.
     *
     * @param  \App\Models\QualityControl  $qualityControl
     * @return void
     */
    public function forceDeleted(QualityControl $qualityControl)
    {
        //
    }
}
