<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\User;
use App\Traits\Notify as NotifyTrait;  // tu trait
use Illuminate\Support\Facades\Log;
use Throwable;


class CommentObserver
{
    use NotifyTrait;
    /**
     * Handle the Comment "created" event.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function created(Comment $comment)
    {
        $qc = $comment->qualityControl;

        // 1) Crea la entrada en histories
        $userName = $comment->user->name;
        $docPart  = $comment->document
            ? ' en el documento «' . $comment->document->name . '»'
            : '';
        $message  = "{$userName} comentó{$docPart}: {$comment->comment}";

        // Este notify viene de tu NotifyTrait y grabará en histories
        $this->notify($message, $qc);

        // 2) Notificar por e-mail / push, protegiendo fallos
        foreach ($qc->users as $user) {
            try {
                $user->notify(new \App\Notifications\Notify(
                    "Hay un nuevo comentario en «{$qc->name}»"
                ));
            } catch (Throwable $e) {
                Log::warning(
                    "Error notificando comentario {$comment->id} "
                        . "para usuario {$user->id}: " . $e->getMessage()
                );
                // opcional: seguir al siguiente usuario sin interrumpir
            }
        }
    }


    /**
     * Handle the Comment "updated" event.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function updated(Comment $comment)
    {
        //
    }

    /**
     * Handle the Comment "deleted" event.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function deleted(Comment $comment)
    {
        //
    }

    /**
     * Handle the Comment "restored" event.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function restored(Comment $comment)
    {
        //
    }

    /**
     * Handle the Comment "force deleted" event.
     *
     * @param  \App\Models\Comment  $comment
     * @return void
     */
    public function forceDeleted(Comment $comment)
    {
        //
    }
}
