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
        if (isset($document->qualityControl)) {
            $message = "Ha creado un nuevo documento";
            $this->notify($message, $document->qualityControl);
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
        if ($fase && isset($fase->qualityControl))
            $fase->updateStatus();
        $user = auth()->user();
        $message = '';
        $name = $document->name;
        if ($user->hasRole(RoleEnum::Client->value)) {
            $message = "Ha subido el documento con nombre: $name";
        } else if ($user->hasRole(RoleEnum::Consultant->value)) {
            $status = $document->status->key == StatusEnum::Waiting->value ? 'pendiente' : 'completado';
            $message = "Ha cambiado el documento $name a el estado de $status";
            $fase->qualityControl->users()->get()->each(function (User $item) use ($message) {
                $user = User::find($item->id);
                $user->notify(new Notify($message));
            });
        }
        $this->notify($message, $fase->qualityControl);
    }


    /**
     * Handle the Document "deleted" event.
     *
     * @param  \App\Models\Document  $document
     * @return void
     */
    public function deleted(Document $document)
    {
        //
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
