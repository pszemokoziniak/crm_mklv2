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
        // 1. Super-admin i Administrator mogą edytować wszystko
        if ($user->hasAnyRole(['super-admin', 'administrator'])) {
            return true;
        }

        // 2. Inni muszą mieć uprawnienie 'manage zapytania' ORAZ być przypisani do rekordu
        if ($user->hasPermissionTo('manage zapytania')) {
            return $user->id === $zapytania->user_otrzymal_id ||
                   $user->id === $zapytania->user_opracowuje_id ||
                   $user->id === $zapytania->user_id;
        }

        return false;
    }
}
