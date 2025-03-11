<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\AuditoryType;
use App\Models\User;

class AuditoryTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
       return $user->can('auditoryType index');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, AuditoryType $auditoryType)
    {
        return $user->can('auditoryType show') || ($user->hasRole('client') && $auditoryType->clients->contains($user->id));
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('auditoryType create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, AuditoryType $auditoryType)
    {
        return $user->can('auditoryType update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\AuditoryType  $auditoryType
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, AuditoryType $auditoryType)
    {
        return $user->can('auditoryType delete');
    }
}
