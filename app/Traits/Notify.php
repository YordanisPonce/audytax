<?php

namespace App\Traits;

use App\Models\History;
use App\Models\QualityControl;
use App\Models\User;
use App\Notifications\Notify as NotificationsNotify;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as FacadesNotification;

trait Notify
{


    public function notify(string $message, QualityControl $qualityControl)
    {
        History::create(
            [
                'title' => null,
                'description' => $message,
                'user_id' => auth()->id(),
                'quality_control_id' => $qualityControl->id
            ]
        );
    }
}
