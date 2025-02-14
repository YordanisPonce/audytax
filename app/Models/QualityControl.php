<?php

namespace App\Models;

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
            $query->where('key', 'complete');
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

    public function updateStatus()
    {
        $fasesCount = $this->fases()->count();

        $procesingFases = $this->fases()->whereHas('status', function ($query) {
            $query->where('label', 'processing');
        })->count();

        $completeFases = $this->fases()->whereHas('status', function ($query) {
            $query->where('label', 'complete');
        })->count();

        $cancelFases = $this->fases()->whereHas('status', function ($query) {
            $query->where('label', 'cancel');
        })->count();

        $status = 1;
        if ($procesingFases == $fasesCount) {
            $status = Status::where('key', 'processing')->first()->id ?: 1;
            //Lo pongo en procesando
        } else if ($completeFases == $fasesCount) {
            $status = Status::where('key', 'complete')->first()->id ?: 1;
            //Lo pongo en completado
        } else if ($cancelFases == $fasesCount) {
            $status = Status::where('key', 'cancel')->first()->id ?: 1;
        }
        $this->status_id = $status;
        $this->update();
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
        return $this->fases()->whereHas('status', function ($query) {
            $query->where('key', 'waiting');
        })->first() ?: $this->fases()->whereHas('status', function ($query) {
            $query->where('key', 'pending');
        })->first() ?: $this->fases()->latest()->first();
    }
}
