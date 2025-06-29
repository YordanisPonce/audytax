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
use Illuminate\Support\Facades\Log;
use Throwable;

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
        if (! $qc = $document->qualityControl) {
            return;
        }

        $message = "Ha creado un nuevo documento";

        // 1) Grabar en history (NotifyTrait)
        try {
            $this->notify($message, $qc);
        } catch (Throwable $e) {
            Log::warning("History notify falló al crear documento {$document->id}: " . $e->getMessage());
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
        $fase = $document->fase;
        $qc   = $fase?->qualityControl;

        // 1) Recalcula estado de la fase
        if ($fase && $qc) {
            $fase->updateStatus();
        }

        $user      = auth()->user();
        $name      = $document->name;
        $statusKey = $document->status->key;
        $message   = null;

        // 2) Si actualiza el cliente (subida nueva versión), volver a "Esperando revisión"
        if ($user->hasRole(RoleEnum::Client->value)) {
            $message = "Documento “{$name}”  esperando revisión.";
        }
        // 3) Si actualiza el consultor, maneja Accepted / Rejected
        elseif ($user->hasRole(RoleEnum::Consultant->value)) {
            $message = match($statusKey) {
                StatusEnum::Accepted->value    => "Documento “{$name}” aceptado por el consultor.",
                StatusEnum::Rejected->value    => "Documento “{$name}” rechazado por el consultor.",
                default                         => null,
            };

            // notificar a cada usuario del QC
            if ($message && $qc) {
                foreach ($qc->users as $u) {
                    try {
                        $u->notify(new Notify($message));
                    } catch (Throwable $e) {
                        Log::warning(
                            "Error notificando user {$u->id} "
                                . "sobre doc {$document->id}: " . $e->getMessage()
                        );
                    }
                }
            }
        }

        // 4) Notificación global al Quality Control
        if ($message && $qc) {
            $this->notify($message, $qc);
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
         if ($document->qualityControl) {
            $message = "Ha borrado un documento";
            $this->notify($message, $document->qualityControl);
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
