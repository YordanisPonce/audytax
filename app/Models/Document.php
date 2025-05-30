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

    public function qualityControl()
    {
        return $this->belongsTo(QualityControl::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

      public function isOpen(): bool
    {
        return $this->status->key === 'open';
    }

     public function isWaitingReview(): bool
    {
        return $this->status->key === 'waiting_review';
    }

    public function isAccepted(): bool
    {
        return $this->status->key === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status->key === 'rejected';
    }
}
