<?php

namespace App\Http\Middleware;

use App\Models\Kontakt;
use App\Models\MainMenu; // Import the MainMenu model
use App\Models\Oferta;
use App\Models\User;
use App\Models\Zadania;
use App\Models\Zapytania;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Log; // Import Log facade

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => function () use ($request) {
                return [
                    'user' => $request->user() ? [
                        'id' => $request->user()->id,
                        'first_name' => $request->user()->first_name,
                        'last_name' => $request->user()->last_name,
                        'email' => $request->user()->email,
                        'owner' => $request->user()->owner,
                        'roles' => $request->user()->getRoleNames(),
                        'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                        'is_super_admin' => $request->user()->hasRole('super-admin'), // Added this line
                        'account' => [
                            'id' => $request->user()->account ? $request->user()->account->id : null,
                            'name' => $request->user()->account ? $request->user()->account->name : null,
                        ],
                    ] : null,
                ];
            },
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
            'unreadNotificationsCount' => function () use ($request) {
                return $request->user() ? $request->user()->unreadNotifications()->count() : 0;
            },
            'vapidPublicKey' => config('webpush.vapid.public_key'),
            'impersonating' => function () {
                $impersonatorId = session('impersonator_id');
                if (!$impersonatorId) {
                    return null;
                }
                $impersonator = User::find($impersonatorId);
                if (!$impersonator) {
                    return null;
                }
                return [
                    'id' => $impersonator->id,
                    'first_name' => $impersonator->first_name,
                    'last_name' => $impersonator->last_name,
                ];
            },
            'userFirstNames' => function () use ($request) {
                if (!$request->user()) {
                    return [];
                }
                // Unikalne imiona aktywnych, nieusunietych userow - do podswietlania
                // dzisiejszych imienin w headerze.
                return User::whereNull('deleted_at')
                    ->where('active', 1)
                    ->whereNotNull('first_name')
                    ->where('first_name', '!=', '')
                    ->pluck('first_name')
                    ->map(fn ($n) => trim($n))
                    ->unique()
                    ->values()
                    ->all();
            },
            'myTodo' => function () use ($request) {
                if (!$request->user()) {
                    return null;
                }
                $userId = $request->user()->id;
                $today = \Carbon\Carbon::today();
                $soonDate = $today->copy()->addDays(7);

                // Logika 1:1 z DashboardController - to samo co widok "Do zrobienia".

                // Zapytania: moje, bez aktywnej oferty, nie sa parentem wznowienia
                $zapytaniaCollection = Zapytania::with(['client:id,nazwa', 'opracowuje:id,first_name,last_name'])
                    ->where(function ($q) use ($userId) {
                        $q->where('user_opracowuje_id', $userId)->orWhere('user_id', $userId);
                    })->whereDoesntHave('oferty', fn ($q) => $q->whereNull('deleted_at'))
                    ->where(function ($q) {
                        $q->whereNull('wznowienie')->orWhere('wznowienie', 0)->orWhere('wznowienie', 1);
                    })
                    ->orderByRaw('data_zlozenia IS NULL, data_zlozenia ASC')
                    ->limit(50)
                    ->get();

                // Oferty: moje w statusie TOCZY, z aktywnym parent zapytaniem
                $ofertyCollection = Oferta::with(['zapytania:id,nazwa_projektu', 'client:id,nazwa', 'waluta:id,name'])
                    ->whereHas('zapytania', fn ($q) => $q->whereNull('deleted_at'))
                    ->whereHas('ofertastatus', fn ($q) => $q->where('name', 'TOCZY'))
                    ->where('user_id', $userId)
                    ->orderByRaw('data_kontakt IS NULL, data_kontakt ASC')
                    ->limit(50)
                    ->get();

                // Kontakty: moje, tylko najnowszy wpis per watek
                $kontaktyCollection = Kontakt::with(['client:id,nazwa'])
                    ->whereIn('id', function ($q) {
                        $q->selectRaw('MAX(id)')->from('kontakts')->groupByRaw('COALESCE(parent_id, id)');
                    })->where('opiekun_id', $userId)
                    ->where(function ($q) use ($soonDate, $today) {
                        $q->where('next_call_date', '<=', $soonDate)
                          ->orWhere('call_date', $today);
                    })
                    ->orderByRaw('next_call_date IS NULL, next_call_date ASC')
                    ->limit(50)
                    ->get();

                // Zadania: moje, niezamkniete, bez deadline lub deadline <= dzis+7
                $zadaniaCollection = Zadania::with(['client:id,nazwa'])
                    ->where('status', '!=', 'zamkniete')
                    ->where(function ($q) use ($soonDate) {
                        $q->whereNull('deadline')->orWhere('deadline', '<=', $soonDate);
                    })
                    ->where('responsible_person_id', $userId)
                    ->orderByRaw('deadline IS NULL, deadline ASC')
                    ->limit(50)
                    ->get();

                $isOverdue = fn ($date) => $date && \Carbon\Carbon::parse($date)->lt($today);

                return [
                    'zapytania' => $zapytaniaCollection->count(),
                    'oferty' => $ofertyCollection->count(),
                    'kontakty' => $kontaktyCollection->count(),
                    'zadania' => $zadaniaCollection->count(),
                    'total' => $zapytaniaCollection->count() + $ofertyCollection->count() + $kontaktyCollection->count() + $zadaniaCollection->count(),
                    'items' => [
                        'zapytania' => $zapytaniaCollection->map(fn ($z) => [
                            'id' => $z->id,
                            'id_zapyt' => $z->id_zapyt,
                            'nazwa_projektu' => $z->nazwa_projektu,
                            'client' => $z->client?->nazwa,
                            'data_zlozenia' => $z->data_zlozenia?->format('Y-m-d'),
                            'overdue' => $isOverdue($z->data_zlozenia),
                            'opracowuje' => $z->opracowuje ? trim($z->opracowuje->first_name . ' ' . $z->opracowuje->last_name) : null,
                            'link' => "/zapytania/{$z->id}/edit",
                        ])->values(),
                        'oferty' => $ofertyCollection->map(fn ($o) => [
                            'id' => $o->id,
                            'nazwa_projektu' => $o->zapytania?->nazwa_projektu,
                            'client' => $o->client?->nazwa,
                            'data_kontakt' => $o->data_kontakt?->format('Y-m-d'),
                            'overdue' => $isOverdue($o->data_kontakt),
                            'kwota' => $o->kwota,
                            'waluta' => $o->waluta?->name,
                            'link' => "/oferta/{$o->id}/edit",
                        ])->values(),
                        'kontakty' => $kontaktyCollection->map(fn ($k) => [
                            'id' => $k->id,
                            'thread_root_id' => $k->parent_id ?? $k->id,
                            'subject' => $k->subject,
                            'client' => $k->client?->nazwa,
                            'next_call_date' => $k->next_call_date?->format('Y-m-d'),
                            'next_call_time' => $k->next_call_time,
                            'overdue' => $isOverdue($k->next_call_date),
                            'link' => "/kontakt/" . ($k->parent_id ?? $k->id) . "/edit",
                        ])->values(),
                        'zadania' => $zadaniaCollection->map(fn ($z) => [
                            'id' => $z->id,
                            'subject' => $z->subject,
                            'client' => $z->client?->nazwa,
                            'deadline' => $z->deadline?->format('Y-m-d'),
                            'overdue' => $isOverdue($z->deadline),
                            'link' => "/zadania/{$z->id}/edit",
                        ])->values(),
                    ],
                ];
            },
            'onlineUsers' => function () use ($request) {
                if (!$request->user()) {
                    return [];
                }
                // Widoczne tylko dla super-admin i Administrator
                if (!$request->user()->hasAnyRole(['super-admin', 'Administrator'])) {
                    return [];
                }
                // Bierzemy uzytkownikow ktorzy logowali sie w ciagu 7 dni
                // (jeden wpis na usera - w users mamy zawsze 1 wiersz),
                // sortowane od najnowszego logowania.
                return User::whereNotNull('last_login_at')
                    ->where('last_login_at', '>=', now()->subDays(7))
                    ->where('id', '!=', $request->user()->id) // pomijamy siebie
                    ->orderByDesc('last_login_at')
                    ->get(['id', 'first_name', 'last_name', 'email', 'last_login_at', 'last_logout_at', 'last_seen_at'])
                    ->map(function ($u) {
                        // "Zalogowany" = login pozniejszy niz ewentualny logout
                        $isLoggedIn = $u->last_logout_at === null
                            || $u->last_login_at->gt($u->last_logout_at);
                        return [
                            'id' => $u->id,
                            'first_name' => $u->first_name,
                            'last_name' => $u->last_name,
                            'email' => $u->email,
                            'is_logged_in' => $isLoggedIn,
                            'last_login_at' => $u->last_login_at->format('Y-m-d H:i:s'),
                            'last_logout_at' => $u->last_logout_at ? $u->last_logout_at->format('Y-m-d H:i:s') : null,
                            'last_seen_at' => $u->last_seen_at ? $u->last_seen_at->format('Y-m-d H:i:s') : null,
                        ];
                    })
                    ->values();
            },
            'mainMenus' => function () use ($request) {
                if (!$request->user()) {
                    Log::info('No user logged in, returning empty mainMenus.');
                    return [];
                }

                $user = $request->user();
                $allMainMenus = MainMenu::with('uprawnienia')->orderBy('order')->get();
                $userRoles = $user->getRoleNames()->toArray(); // Get user's role names
                $isSuperAdmin = $user->hasRole('super-admin');
                $isAdmin = $user->hasRole('Administrator');
                $hasFullAccess = $isSuperAdmin || $isAdmin;

                Log::info('User ID: ' . $user->id . ', Name: ' . $user->first_name . ' ' . $user->last_name);
                Log::info('User Roles: ' . implode(', ', $userRoles)); // Log user roles
                Log::info('Is Super Admin: ' . ($isSuperAdmin ? 'Yes' : 'No'));
                Log::info('Is Administrator: ' . ($isAdmin ? 'Yes' : 'No'));
                Log::info('Total Main Menus fetched: ' . $allMainMenus->count());

                $filteredMenus = $allMainMenus->filter(function ($menuItem) use ($userRoles, $hasFullAccess) {
                    Log::info('Processing menu item: ' . $menuItem->name);
                    $menuItemRoles = $menuItem->uprawnienia->pluck('name')->toArray(); // Get roles associated with menu item
                    Log::info('Menu item associated roles: ' . implode(', ', $menuItemRoles));

                    // Super admin / Administrator sees everything
                    if ($hasFullAccess) {
                        Log::info('User is super-admin/Administrator, showing menu item: ' . $menuItem->name);
                        return true;
                    }

                    // If a menu item has no associated roles, it should NOT be visible to non-super-admins
                    if (empty($menuItemRoles)) {
                        Log::info('Menu item has no associated roles, hiding from non-super-admin: ' . $menuItem->name);
                        return false; // Hide if no roles are assigned and user is not super admin
                    }

                    // Check if the user has any of the roles required by the menu item
                    foreach ($menuItemRoles as $menuRoleName) {
                        if (in_array($menuRoleName, $userRoles)) {
                            Log::info('User has role "' . $menuRoleName . '" for menu item: ' . $menuItem->name);
                            return true;
                        }
                    }

                    Log::info('User does not have required roles for menu item: ' . $menuItem->name);
                    return false;
                })->map(function ($menuItem) {
                    return [
                        'id' => $menuItem->id,
                        'name' => $menuItem->name,
                        'route' => $menuItem->route,
                        'icon' => $menuItem->icon,
                        'order' => $menuItem->order,
                    ];
                })->values()->toArray(); // Reset keys and convert to array

                Log::info('Filtered Main Menus count: ' . count($filteredMenus));
                Log::info('Filtered Main Menus: ' . json_encode(array_column($filteredMenus, 'name')));

                return $filteredMenus;
            },
        ]);
    }
}
