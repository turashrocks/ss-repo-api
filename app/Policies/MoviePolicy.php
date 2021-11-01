<?php

namespace App\Policies;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MoviePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any movies.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the movie.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Movie  $movie
     * @return mixed
     */
    public function view(User $user, Movie $movie)
    {
        //
    }

    /**
     * Determine whether the user can create movies.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the movie.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Movie  $movie
     * @return mixed
     */
    public function update(User $user, Movie $movie)
    {
        return $movie->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the movie.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Movie  $movie
     * @return mixed
     */
    public function delete(User $user, Movie $movie)
    {
        return $movie->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the movie.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Movie  $movie
     * @return mixed
     */
    public function restore(User $user, Movie $movie)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the movie.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Movie  $movie
     * @return mixed
     */
    public function forceDelete(User $user, Movie $movie)
    {
        //
    }
}
