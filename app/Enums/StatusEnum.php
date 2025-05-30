<?php
namespace App\Enums;

enum StatusEnum: string
{
    case Open            = 'open';
    case WaitingReview   = 'waiting_review';
    case Accepted        = 'accepted';
    case Rejected        = 'rejected';
}

// {
//     case Waiting = 'waiting';
//     case Processing = 'processing';
//     case Complete = 'complete';
// }