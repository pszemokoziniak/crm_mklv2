<?php

namespace App\Http\Middleware;

use App\Models\MainMenu;
use Closure;
use Illuminate\Http\Request;

class CheckMenuAccess
{
    /**
     * Sprawdza czy user ma dostęp do danej sekcji na podstawie
     * przypisań ról do pozycji menu (tabela uprawnienia_main_menu).
     *
     * Logika spójna z HandleInertiaRequests::mainMenus.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Super admin i Administrator mają dostęp do wszystkiego
        if ($user->hasRole('super-admin') || $user->hasRole('Administrator')) {
            return $next($request);
        }

        // Pierwszy segment URL → dopasowanie do MainMenu.route
        $firstSegment = $request->segment(1);
        $path = '/' . $firstSegment;

        // Znajdź pozycję menu odpowiadającą tej ścieżce
        $menuItem = MainMenu::with('uprawnienia')->where('route', $path)->first();

        // Jeśli nie ma pozycji menu dla tego URL — przepuść
        // (route nie jest kontrolowany przez system menu)
        if (!$menuItem) {
            return $next($request);
        }

        // Role przypisane do tej pozycji menu
        $menuRoles = $menuItem->uprawnienia->pluck('name')->toArray();

        // Jeśli menu nie ma przypisanych ról — blokuj (spójne z HandleInertiaRequests)
        if (empty($menuRoles)) {
            abort(403, 'Brak dostępu.');
        }

        // Sprawdź czy user ma którąkolwiek z wymaganych ról
        $userRoles = $user->getRoleNames()->toArray();

        foreach ($menuRoles as $roleName) {
            if (in_array($roleName, $userRoles)) {
                return $next($request);
            }
        }

        abort(403, 'Brak dostępu.');
    }
}