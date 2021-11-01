<?php

namespace App\Policies;

use App\Models\Studio;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudioPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any studios.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the studio.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Studio  $studio
     * @return mixed
     */
    public function view(User $user, Studio $studio)
    {
        //
    }

    /**
     * Determine whether the user can create studios.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the studio.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Studio  $studio
     * @return mixed
     */
    public function update(User $user, Studio $studio)
    {
        return $user->isOwnerOfStudio($studio);
    }

    /**
     * Determine whether the user can delete the studio.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Studio  $studio
     * @return mixed
     */
    public function delete(User $user, Studio $studio)
    {
        return $user->isOwnerOfStudio($studio);
    }

    /**
     * Determine whether the user can restore the studio.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Studio  $studio
     * @return mixed
     */
    public function restore(User $user, Studio $studio)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the studio.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Studio  $studio
     * @return mixed
     */
    public function forceDelete(User $user, Studio $studio)
    {
        //
    }
}
