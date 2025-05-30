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
        $status = Status::where('key', 'open')->first();
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

   public function getFinishPercent(): float
{
    // Total de documentos en esta fase
    $total = $this->documents()->count();

    if ($total === 0) {
        return 0;
    }

    // Contamos solo los documentos cuyo estado es "accepted"
    $acceptedCount = $this->documents()
        ->whereHas('status', function ($query) {
            $query->where('key', StatusEnum::Accepted->value);
        })
        ->count();

    // Calculamos el porcentaje y lo retornamos
    return number_format(($acceptedCount * 100) / $total, 2);
}


    public function status()
    {
        return $this->belongsTo(Status::class);
    }

   public function isOpen(): bool
    {
        return $this->documents()
            ->whereHas('status', fn($q) => $q->where('key', StatusEnum::Open->value))
            ->exists();
    }

    /**
     * Si NO hay open, pero HAY waiting_review
     */
    public function isWaitingReview(): bool
    {
        return ! $this->isOpen() && $this->documents()
            ->whereHas('status', fn($q) => $q->where('key', StatusEnum::WaitingReview->value))
            ->exists();
    }

    /**
     * Si TODOS los docs están accepted (y ninguno open ni waiting_review)
     */
    public function isAccepted(): bool
    {
        $total = $this->documents()->count();
        if (! $total) return false;

        $acceptedCount = $this->documents()
            ->whereHas('status', fn($q) => $q->where('key', StatusEnum::Accepted->value))
            ->count();

        return $acceptedCount === $total;
    }

    /**
     * Si hay al menos un doc rejected
     */
    public function isRejected(): bool
    {
        return $this->documents()
            ->whereHas('status', fn($q) => $q->where('key', StatusEnum::Rejected->value))
            ->exists();
    }


    public function getAddedDocumentsAttribute()
    {
        return $this->documents()->whereNotNull('url')->count();
    }

  public function updateStatus(): void
{
    // Buscamos cada Status por su clave
    $sOpen          = Status::where('key', StatusEnum::Open->value)->first();
    $sWaitingReview = Status::where('key', StatusEnum::WaitingReview->value)->first();
    $sAccepted      = Status::where('key', StatusEnum::Accepted->value)->first();
    $sRejected      = Status::where('key', StatusEnum::Rejected->value)->first();

    // Decidimos el nuevo estado de la fase según prioridades:
    // 1. Si hay al menos un documento rechazado → rejected
    // 2. Si aún hay documentos "open"           → open
    // 3. Si no hay open pero hay waiting_review → waiting_review
    // 4. Si todos están accepted                → accepted
    $nuevo = null;

    if ($this->isRejected()    && $sRejected) {
        $nuevo = $sRejected;
    }
    elseif ($this->isOpen()    && $sOpen) {
        $nuevo = $sOpen;
    }
    elseif ($this->isWaitingReview() && $sWaitingReview) {
        $nuevo = $sWaitingReview;
    }
    elseif ($this->isAccepted() && $sAccepted) {
        $nuevo = $sAccepted;
    }

    // Si cambió, lo guardamos
    if ($nuevo && $this->status_id !== $nuevo->id) {
        $this->status_id = $nuevo->id;
        $this->save();
    }
}


    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

     public function getStatusLabel(): string
    {
        if ($this->isRejected()) {
            return StatusEnum::Rejected->value;
        }
        if ($this->isAccepted()) {
            return StatusEnum::Accepted->value;
        }
        if ($this->isWaitingReview()) {
            return StatusEnum::WaitingReview->value;
        }
        return StatusEnum::Open->value;
    }
}
