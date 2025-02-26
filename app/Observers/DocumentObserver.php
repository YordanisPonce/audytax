<?php

namespace App\Observers;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Models\Document;
use App\Models\Status;
use App\Models\User;
use App\Notifications\Notify;
use App\Traits\Notify as NotifyTrait;
use Illuminate\Support\Facades\Auth;

class DocumentObserver
{
    use NotifyTrait;
    /**
     * Handle the Document "created" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function created(Document $document)
    {
        if ($document->quality_control_id) {
            $this->notify('Ha creado un nuevo documento', $document->qualityControl);
        }
    }

    /**
     * Handle the Document "updated" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function updated(Document $document)
    {
        if ($document->quality_control_id) {
            $label = $document->status->label;
            $message = "Ha cambiado el documento $document->name a el estado de: $label";
            $this->notify($message, $document->qualityControl);
        }
    }

    /**
     * Handle the Document "deleted" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function deleted(Document $document)
    {
        if ($document->quality_control_id) {
            $this->notify('Ha eliminado un documento', $document->qualityControl);
        }
    }

    /**
     * Handle the Document "restored" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function restored(Document $document)
    {
        //
    }

    /**
     * Handle the Document "force deleted" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function forceDeleted(Document $document)
    {
        //
    }
}
