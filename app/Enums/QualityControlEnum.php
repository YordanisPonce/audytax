<?php

namespace App\Enums;

enum QualityControlEnum: string
{
    case DELETED = 'Ha eliminado una auditoría';
    case UPDATED = 'Ha actualizado una auditoría';
    case CREATED = 'Ha creado una nueva  auditoría';
    case FASE_UPDATED = 'Ha actualizado una auditoría';
}