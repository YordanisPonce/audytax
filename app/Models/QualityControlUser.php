<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityControlUser extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'quality_control_id'];
}
