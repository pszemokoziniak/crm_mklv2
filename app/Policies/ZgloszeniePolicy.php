<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Zgloszenie;
use Illuminate\Auth\Access\HandlesAuthorization;

class ZgloszeniePolicy
{
    use HandlesAuthorization;

    /** Każdy zalogowany może zgłaszać i przeglądać listę wszystkich zgłoszeń. */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /** Podgląd ma każdy zalogowany — zgłoszenia są wspólne. */
    public function view(User $user, Zgloszenie $zgloszenie): bool
    {
        return true;
    }

    /** Edycja treści: biuro, autor i osoba przypisana. */
    public function update(User $user, Zgloszenie $zgloszenie): bool
    {
        return $this->involved($user, $zgloszenie);
    }

    /** Zmiana statusu — te same osoby co edycja, ale bez wchodzenia w formularz. */
    public function updateStatus(User $user, Zgloszenie $zgloszenie): bool
    {
        return $this->involved($user, $zgloszenie);
    }

    /** Komentować może każdy zalogowany — dyskusja jest wspólna. */
    public function comment(User $user, Zgloszenie $zgloszenie): bool
    {
        return true;
    }

    /** Archiwizacja i przywracanie: biuro albo autor zgłoszenia. */
    public function delete(User $user, Zgloszenie $zgloszenie): bool
    {
        return $this->isOffice($user) || (int) $zgloszenie->reporter_id === (int) $user->id;
    }

    public function restore(User $user, Zgloszenie $zgloszenie): bool
    {
        return $this->delete($user, $zgloszenie);
    }

    private function involved(User $user, Zgloszenie $zgloszenie): bool
    {
        return $this->isOffice($user)
            || (int) $zgloszenie->reporter_id === (int) $user->id
            || (int) $zgloszenie->assignee_id === (int) $user->id;
    }

    /** „Biuro" = administracja z pełnym dostępem. */
    private function isOffice(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'Administrator']);
    }
}
