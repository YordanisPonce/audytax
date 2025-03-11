<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use ProtoneMedia\LaravelVerifyNewEmail\MustVerifyNewEmail;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Contracts\Role;
use Avatar;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, MustVerifyNewEmail, InteractsWithMedia;

    protected array $guard_name = ['sanctum', 'web'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'phone',
        'company',
        'post_code',
        'city',
        'country',
        'photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['avatar'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Manipulations::FIT_CROP, 300, 300)
            ->nonQueued();
    }

    /**
     * Local scope to exclude auth user
     * @param $query
     * @return mixed
     */
    public function scopeWithoutAuthUser($query): mixed
    {
        return $query->where('id', '!=', auth()->id());
    }

    /**
     * Local scope to exclude super admin
     * @param $query
     * @return mixed
     */
    public function scopeWithoutSuperAdmin($query): mixed
    {
        return $query->where('id', '!=', 1);
    }

    public function getPhoto()
    {
        if ($this->photo) {
            return '/storage/' . $this->photo;
        }
        return false;
    }

    public function getRole()
    {
        $roles = ['admin' => 'Administrador', 'consultant' => 'Consultor', 'client' => 'Cliente'];
        $role = $this->hasRole('admin') ? 'admin' : ($this->hasRole('consultant') ? 'consultant' : 'client');
        return $roles[$role];
    }

    public function qualityControls()
    {
        return $this->belongsToMany(QualityControl::class, 'quality_control_users');
    }
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function hasDocument(Document $document): bool
    {
        return $this->qualityControls()->whereHas('documents', function ($query) use ($document) {
            $query->where('id', $document->id);
        })->exists();
    }

    public function getAvatarAttribute()
    {
        return $this->getPhoto();

    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function auditoryTypes()
    {
        return $this->belongsToMany(AuditoryType::class, 'auditory_type_client', 'client_id', 'auditory_type_id');
    }
}
