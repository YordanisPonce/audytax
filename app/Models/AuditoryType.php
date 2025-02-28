<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditoryType extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'client_id'];
    protected $with = ['fases'];

    public function fases()
    {
        return $this->hasMany(Fase::class);
    }

    public function qualityControls()
    {
        return $this->hasMany(QualityControl::class);
    }
    //add relation auditoryType has many documents
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
    
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
