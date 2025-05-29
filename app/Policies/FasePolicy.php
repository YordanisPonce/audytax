<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Fase;
use App\Models\User;

class FasePolicy
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
        return $user->can('fase index');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Fase  $fase
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Fase $fase)
    {
        return $user->can('fase show');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('fase create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Fase  $fase
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Fase $fase)
    {
        return $user->can('fase update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Fase  $fase
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Fase $fase)
    {
        return $user->can('fase delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Fase  $fase
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Fase $fase)
    {
        return $user->can('fase delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Fase  $fase
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Fase $fase)
    {
        return $user->can('fase delete');
    }
}
