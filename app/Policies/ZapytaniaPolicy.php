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

        // 2. Jeśli użytkownik jest przypisany do zapytania w dowolnej roli, może je edytować
        if ($user->id === (int)$zapytania->user_otrzymal_id ||
            $user->id === (int)$zapytania->user_opracowuje_id ||
            $user->id === (int)$zapytania->user_id) {
            return true;
        }

        // 3. Ewentualnie jeśli ma ogólne uprawnienie do zarządzania wszystkimi zapytaniami
        if ($user->hasPermissionTo('manage zapytania')) {
            return true;
        }

        return false;
    }
}
