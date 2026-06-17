<?php

namespace App\Http\Middleware;

use App\Models\MainMenu; // Import the MainMenu model
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
