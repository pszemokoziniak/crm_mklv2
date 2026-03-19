<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'filters' => Request::all('search', 'role', 'trashed'),
            'roles' => Role::all()->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
            ]),
            'users' => Auth::user()->account->users()
                ->orderByName()
                ->filter(Request::only('search', 'role', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'owner' => $user->owner,
                    'active' => $user->active,
                    'roles' => $user->getRoleNames(),
                    'photo' => $user->photo_path ? URL::route('image', ['path' => $user->photo_path, 'w' => 40, 'h' => 40, 'fit' => 'crop']) : null,
                    'deleted_at' => $user->deleted_at,
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create', [
            'roles' => Role::all()->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
            ]),
        ]);
    }

    public function store()
    {
        Request::validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')],
            'password' => ['nullable'],
            'role' => ['required', 'exists:roles,name'],
            'photo' => ['nullable', 'image'],
            'active' => ['required'],
            'preliminarz_email' => ['boolean'],
        ]);

        $user = Auth::user()->account->users()->create([
            'first_name' => Request::get('first_name'),
            'last_name' => Request::get('last_name'),
            'email' => Request::get('email'),
            'password' => Request::get('password'),
            'owner' => false, // Domyślnie false, bo używamy ról Spatie
            'photo_path' => Request::file('photo') ? Request::file('photo')->store('users') : null,
            'active' => Request::get('active'),
            'preliminarz_email' => Request::get('preliminarz_email', false),
        ]);

        $user->assignRole(Request::get('role'));

        return Redirect::route('users')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'owner' => $user->owner,
                'photo' => $user->photo_path ? URL::route('image', ['path' => $user->photo_path, 'w' => 60, 'h' => 60, 'fit' => 'crop']) : null,
                'deleted_at' => $user->deleted_at,
                'active' => $user->active,
                'preliminarz_email' => $user->preliminarz_email,
                'role' => $user->getRoleNames()->first(),
            ],
            'roles' => Role::all()->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
            ]),
            'activities' => $user->activities()->with('causer')->latest()->get()->map(fn ($activity) => [
                'id' => $activity->id,
                'description' => $activity->description,
                'user' => $activity->causer ? $activity->causer->first_name . ' ' . $activity->causer->last_name : 'System',
                'changes' => $activity->changes,
                'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
            ]),
        ]);
    }

    public function update(User $user)
    {
        if (App::environment('demo') && $user->isDemoUser()) {
            return Redirect::back()->with('error', 'Updating the demo user is not allowed.');
        }

        Request::validate([
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable'],
            'role' => ['nullable', 'exists:roles,name'],
            'photo' => ['nullable', 'image', 'max:2048'], // Dodano max size dla bezpieczeństwa
            'active' => ['nullable'],
            'preliminarz_email' => ['boolean'],
        ]);

        $oldRole = $user->getRoleNames()->first();
        $newRole = Request::get('role');

        $user->update(Request::only('first_name', 'last_name', 'email', 'active', 'preliminarz_email'));

        if ($newRole && (Auth::user()->hasRole('super-admin') || Auth::user()->hasRole('Administrator'))) {
            if ($oldRole !== $newRole) {
                $user->syncRoles($newRole);

                // Ręczne logowanie zmiany roli
                activity()
                    ->performedOn($user)
                    ->causedBy(Auth::user())
                    ->withProperties([
                        'attributes' => ['role' => $newRole],
                        'old' => ['role' => $oldRole]
                    ])
                    ->log('updated');
            }
        }

        if (Request::file('photo')) {
            $user->update(['photo_path' => Request::file('photo')->store('users')]);
        }

        if (Request::get('password')) {
            $user->update(['password' => Request::get('password')]);
        }

        return Redirect::back()->with('success', 'Użytkownik poprawiony.');
    }

    public function destroy(User $user)
    {
        if (App::environment('demo') && $user->isDemoUser()) {
            return Redirect::back()->with('error', 'Deleting the demo user is not allowed.');
        }

        $user->delete();

        return Redirect::back()->with('success', 'User deleted.');
    }

    public function restore(User $user)
    {
        $user->restore();

        return Redirect::back()->with('success', 'User restored.');
    }

    public function block(User $user)
    {
        $user->active = 0;
        $user->save();
        return Redirect::back()->with('success', 'Użytkownik zablokowany.');
    }

    public function unblock(User $user)
    {
        $user->active = 1;
        $user->save();
        return Redirect::back()->with('success', 'Użytkownik odblokowany.');
    }
}
