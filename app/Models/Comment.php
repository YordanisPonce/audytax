<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = ['comment', 'status_id', 'user_id', 'fase_id', 'quality_control_id', 'comment_id'];
    protected $with = ['user', 'comments'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->diffForHumans();
    }
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function qualityControl()
    {
        return $this->belongsTo(QualityControl::class);
    }
}
