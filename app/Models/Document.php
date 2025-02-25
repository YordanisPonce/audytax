<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'url', 'description', 'fase_id', 'quality_control_id', 'status_id', 'original_name'];
    protected $with = ['status'];

    public function fase()
    {
        return $this->belongsTo(Fase::class);
    }
    //add  relation document belongs to AuditoryType
    public function auditoryType()
    {
        return $this->belongsTo(AuditoryType::class);
    }

    public function qualityControl()
    {
        return $this->belongsTo(QualityControl::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function isWaiting()
    {
        return $this->status->key == 'waiting';
    }

    public function isProcessing()
    {
        return $this->status->key == 'processing';
    }

    public function isComplete()
    {
        return $this->status->key == 'complete';
    }
}
