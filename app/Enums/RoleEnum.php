<?php

namespace App\Enums;

enum RoleEnum: string
{
    case Admin = 'admin';
    case Client = 'client';
    case Consultant = 'consultant';
}