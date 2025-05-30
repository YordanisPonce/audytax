<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Document;

class QualityControl extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'auditory_type_id', 'status_id'];

    protected $with = ['fases', 'comments'];
    public function fases()
    {
        return $this->hasMany(Fase::class);
    }

    public function auditoryType()
    {
        return $this->belongsTo(AuditoryType::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'quality_control_users');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function getFinishPercent()
    {
        $total = $this->fases()->count();
        $part = $this->fases()->whereHas('status', function ($query) {
            $query->where('key', 'accepted');
        })->count();
        return number_format(($part * 100) / $total);
    }

    public function histories()
    {
        return $this->hasMany(History::class, 'quality_control_id')->with('user')->orderBy('id', 'desc');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'quality_control_id');
    }

   public function updateStatus(): void
{
    // Traemos todos los documentos asociados
    $docs = $this->documents()->with('status')->get();
    if ($docs->isEmpty()) {
        // Sin documentos, mantenemos 'open'
        $newKey = StatusEnum::Open->value;
    }
    // 1) Si hay alguno rechazado
    elseif ($docs->contains(fn($d) => $d->status->key === StatusEnum::Rejected->value)) {
        $newKey = StatusEnum::Rejected->value;
    }
    // 2) Si hay alguno esperando revisión
    elseif ($docs->contains(fn($d) => $d->status->key === StatusEnum::WaitingReview->value)) {
        $newKey = StatusEnum::WaitingReview->value;
    }
    // 3) Si todos están aceptados
    elseif ($docs->every(fn($d) => $d->status->key === StatusEnum::Accepted->value)) {
        $newKey = StatusEnum::Accepted->value;
    }
    // 4) Caso contrario (p.ej. recién creado) → 'open'
    else {
        $newKey = StatusEnum::Open->value;
    }

    // Buscamos la ID del status y actualizamos solo si cambió
    $status = Status::where('key', $newKey)->first();
    if ($status && $this->status_id !== $status->id) {
        $this->status_id = $status->id;
        $this->save();
    }
}

    public function getCount()
    {
        $count = 0;
        foreach ($this->comments as $value) {
            $count += $value->comments()->count() + 1;
        }
        return $count;
    }

    public function getActiveFase()
{
    // 1) La primera fase aún “open” (sin que el cliente suba nada)
    $open = $this->fases()
        ->whereHas('status', fn($q) => $q->where('key', StatusEnum::Open->value))
        ->first();
    if ($open) {
        return $open;
    }

    // 2) La primera fase “waiting_review” (cliente ya subió, pendiente revisión)
    $waitingReview = $this->fases()
        ->whereHas('status', fn($q) => $q->where('key', StatusEnum::WaitingReview->value))
        ->first();
    if ($waitingReview) {
        return $waitingReview;
    }

    // 3) Si todo lo anterior falla, devuelve la última fase creada
    return $this->fases()->latest()->first();
}
}
