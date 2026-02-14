<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Zapytania;
use Illuminate\Auth\Access\HandlesAuthorization;

class ZapytaniaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Zapytania  $zapytania
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Zapytania $zapytania)
    {
        // Super-admin can do anything (handled by Gate::before in AuthServiceProvider)

        // Check if user has permission to manage zapytania
        if (!$user->hasPermissionTo('manage zapytania')) {
            return false;
        }

        // Check if user is assigned to this zapytanie
        return $user->id === $zapytania->user_otrzymal_id || $user->id === $zapytania->user_opracowuje_id;
    }
}
