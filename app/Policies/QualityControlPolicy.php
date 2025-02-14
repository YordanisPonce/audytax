<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\QualityControl;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QualityControlPolicy
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
        return $user->can('qualityControl index');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\QualityControl  $qualityControl
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, QualityControl $qualityControl)
    {
        return $user->can('qualityControl show');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('qualityControl create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\QualityControl  $qualityControl
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, QualityControl $qualityControl)
    {
        return $user->can('qualityControl update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\QualityControl  $qualityControl
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, QualityControl $qualityControl)
    {
        return $user->can('qualityControl delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\QualityControl  $qualityControl
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, QualityControl $qualityControl)
    {
        return $user->can('qualityControl delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\QualityControl  $qualityControl
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, QualityControl $qualityControl)
    {
        return $user->can('qualityControl delete');
    }

    public function getDetails(User $user, QualityControl $qualityControl)
    {
        $autorizeUsers = $qualityControl->users()->get(['users.id'])->pluck('id')->toArray();
        return ($user->isAdmin() ||  in_array($user->id, $autorizeUsers)) ? Response::allow() : Response::deny('You do not own this post.');
    }
}
