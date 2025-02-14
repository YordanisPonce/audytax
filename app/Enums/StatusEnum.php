<?php

namespace App\Enums;

enum StatusEnum: string
{
    case Waiting = 'waiting';
    case Processing = 'processing';
    case Complete = 'complete';
}