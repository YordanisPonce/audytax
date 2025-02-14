<?php

namespace App\Enums;

enum QualityControlEnum: string
{
    case DELETED = 'Ha eliminado un control de calidad';
    case UPDATED = 'Ha actualizado un control de calidad';
    case CREATED = 'Ha creado un nuevo control de calidad';
    case FASE_UPDATED = 'Ha actualizado un control de calidad';
}