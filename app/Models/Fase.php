<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Document;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use NumberFormatter;

class Fase extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'auditory_type_id', 'quality_control_id', 'status_id'];
    protected $appends = ['added_documents'];

    protected $with = ['documents'];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function auditoryType()
    {
        return $this->belongsTo(AuditoryType::class);
    }

    public function qualityControl()
    {
        return $this->belongsTo(QualityControl::class);
    }

    public function createDocuments($fase)
    {
        $status = Status::where('key', 'waiting')->first();
        if ($status)
            $documents = $fase->documents; {
            foreach ($documents as $key => $item) {
                $this->documents()->create([
                    'name' => $item->name,
                    'description' => $item->description,
                    'quality_control_id' => $this->qualityControl->id,
                    'status_id' => $status->id
                ]);
            }
        }
    }

    public function getFinishPercent()
    {
        $total = $this->documents()->count();
        $approvedDocuments = $this->documents()->whereHas('status', function ($query) {
            if ($this->isWaiting()) {
                $query->where('key', StatusEnum::Processing);
            } else {
                $query->where('key', StatusEnum::Complete);
            }
        })->count();

        if (!$total)
            return 0;

        return number_format((($approvedDocuments * 100) / $total));

    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function isWaiting(): bool
    {
        return $this->documents()->whereHas('status', function ($query) {
            $query->where('key', StatusEnum::Waiting);
        })->exists();
    }

    public function isProcessing(): bool
    {
        return $this->documents()->whereHas('status', function ($query) {
            $query->where('key', StatusEnum::Processing);
        })->exists() && !$this->isWaiting();
    }

    public function isComplete(): bool
    {
        return $this->documents()->whereHas('status', function ($query) {
            $query->where('key', StatusEnum::Complete);
        })->exists() && !$this->isProcessing() && !$this->isWaiting();
    }


    public function getAddedDocumentsAttribute()
    {
        return $this->documents()->whereNotNull('url')->count();
    }

    public function updateStatus()
    {
        $waiting = Status::where('key', StatusEnum::Waiting->value)->first();
        $processing = Status::where('key', StatusEnum::Processing->value)->first();
        $complete = Status::where('key', StatusEnum::Complete->value)->first();

        if ($this->isWaiting() && $this->status->id != $waiting->id) {
            $this->status_id = $waiting->id;
            $this->update();
        } else if ($this->isProcessing() && $this->status->id != $processing->id) {
            $this->status_id = $processing->id;
            $this->update();
        } else if ($this->isComplete() && $this->status->id != $complete->id) {
            $this->status_id = $complete->id;
            $this->update();
        }
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function getStatusLabel(): string
    {
        return $this->isWaiting() ? StatusEnum::Waiting->value : ($this->isProcessing() ? StatusEnum::Processing->value : StatusEnum::Complete->value);
    }
}
