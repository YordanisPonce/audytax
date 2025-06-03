<?php

namespace App\Enums;

enum QualityControlEnum: string
{
    case DELETED = 'Ha eliminado una auditoria';
    case UPDATED = 'Ha actualizado una auditoria';
    case CREATED = 'Ha creado una nueva  auditoria';
    case FASE_UPDATED = 'Ha actualizado una auditoria';
}