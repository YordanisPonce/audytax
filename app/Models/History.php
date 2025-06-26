<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'user_id', 'quality_control_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Añade esta relación
    public function qualityControl()
    {
        return $this->belongsTo(QualityControl::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }
}
