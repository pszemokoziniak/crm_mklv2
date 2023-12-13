<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BranzaController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EditController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\KrajController;
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\OfertaStatusController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ZakresController;
use App\Http\Controllers\ZapytaniaController;
use App\Models\OfertaStatus;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth

Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store')
    ->middleware('guest');

Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

// Dashboard

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

// Users

Route::get('users', [UsersController::class, 'index'])
    ->name('users')
    ->middleware('auth');

Route::get('users/create', [UsersController::class, 'create'])
    ->name('users.create')
    ->middleware('auth');

Route::post('users', [UsersController::class, 'store'])
    ->name('users.store')
    ->middleware('auth');

Route::get('users/{user}/edit', [UsersController::class, 'edit'])
    ->name('users.edit')
    ->middleware('auth');

Route::put('users/{user}', [UsersController::class, 'update'])
    ->name('users.update')
    ->middleware('auth');

Route::delete('users/{user}', [UsersController::class, 'destroy'])
    ->name('users.destroy')
    ->middleware('auth');

Route::put('users/{user}/restore', [UsersController::class, 'restore'])
    ->name('users.restore')
    ->middleware('auth');

// Organizations

Route::get('organizations', [OrganizationsController::class, 'index'])
    ->name('organizations')
    ->middleware('auth');

Route::get('organizations/create', [OrganizationsController::class, 'create'])
    ->name('organizations.create')
    ->middleware('auth');

Route::post('organizations', [OrganizationsController::class, 'store'])
    ->name('organizations.store')
    ->middleware('auth');

Route::get('organizations/{organization}/edit', [OrganizationsController::class, 'edit'])
    ->name('organizations.edit')
    ->middleware('auth');

Route::put('organizations/{organization}', [OrganizationsController::class, 'update'])
    ->name('organizations.update')
    ->middleware('auth');

Route::delete('organizations/{organization}', [OrganizationsController::class, 'destroy'])
    ->name('organizations.destroy')
    ->middleware('auth');

Route::put('organizations/{organization}/restore', [OrganizationsController::class, 'restore'])
    ->name('organizations.restore')
    ->middleware('auth');

// Contacts

Route::get('contacts', [ContactsController::class, 'index'])
    ->name('contacts')
    ->middleware('auth');

Route::get('contacts/create', [ContactsController::class, 'create'])
    ->name('contacts.create')
    ->middleware('auth');

Route::post('contacts', [ContactsController::class, 'store'])
    ->name('contacts.store')
    ->middleware('auth');

Route::get('contacts/{contact}/edit', [ContactsController::class, 'edit'])
    ->name('contacts.edit')
    ->middleware('auth');

Route::put('contacts/{contact}', [ContactsController::class, 'update'])
    ->name('contacts.update')
    ->middleware('auth');

Route::delete('contacts/{contact}', [ContactsController::class, 'destroy'])
    ->name('contacts.destroy')
    ->middleware('auth');

Route::put('contacts/{contact}/restore', [ContactsController::class, 'restore'])
    ->name('contacts.restore')
    ->middleware('auth');

// Reports

Route::get('reports', [ReportsController::class, 'index'])
    ->name('reports')
    ->middleware('auth');

// Calendar

Route::get('calendar', [CalendarController::class, 'index'])
    ->name('calendar')
    ->middleware('auth');

// Statystyki

Route::get('stats', [StatsController::class, 'index'])
    ->name('stats')
    ->middleware('auth');

// Images

Route::get('/img/{path}', [ImagesController::class, 'show'])
    ->where('path', '.*')
    ->name('image');

// Clients

Route::get('clients', [ClientController::class, 'index'])
    ->name('clients')
    ->middleware('auth');

Route::get('clients/create', [ClientController::class, 'create'])
    ->name('clients.create')
    ->middleware('auth');

Route::post('clients', [ClientController::class, 'store'])
    ->name('clients.store')
    ->middleware('auth');

Route::get('clients/{client}/edit', [ClientController::class, 'edit'])
    ->name('clients.edit')
    ->middleware('auth');

Route::put('clients/{client}', [ClientController::class, 'update'])
    ->name('clients.update')
    ->middleware('auth');

Route::delete('clients/{client}', [ClientController::class, 'destroy'])
    ->name('clients.destroy')
    ->middleware('auth');

Route::put('clients/{client}/restore', [ClientController::class, 'restore'])
    ->name('clients.restore')
    ->middleware('auth');

// Zapytania

Route::get('zapytania', [ZapytaniaController::class, 'index'])
    ->name('zapytania')
    ->middleware('auth');

Route::get('zapytania/create', [ZapytaniaController::class, 'create'])
    ->name('zapytania.create')
    ->middleware('auth');

Route::post('zapytania', [ZapytaniaController::class, 'store'])
    ->name('zapytania.store')
    ->middleware('auth');

Route::get('zapytania/{zapytania}/edit', [ZapytaniaController::class, 'edit'])
    ->name('zapytania.edit')
    ->middleware('auth');

Route::put('zapytania/{zapytania}', [ZapytaniaController::class, 'update'])
    ->name('zapytania.update')
    ->middleware('auth');

Route::delete('zapytania/{zapytania}', [ZapytaniaController::class, 'destroy'])
    ->name('zapytania.destroy')
    ->middleware('auth');

Route::put('zapytania/{zapytania}/restore', [ZapytaniaController::class, 'restore'])
    ->name('zapytania.restore')
    ->middleware('auth');

// Oferta

Route::get('oferta', [OfertaController::class, 'index'])
    ->name('oferta')
    ->middleware('auth');

Route::get('oferta/create', [OfertaController::class, 'create'])
    ->name('oferta.create')
    ->middleware('auth');

Route::post('oferta', [OfertaController::class, 'store'])
    ->name('oferta.store')
    ->middleware('auth');

Route::get('oferta/{oferta}/edit', [OfertaController::class, 'edit'])
    ->name('oferta.edit')
    ->middleware('auth');

Route::put('oferta/{oferta}', [OfertaController::class, 'update'])
    ->name('oferta.update')
    ->middleware('auth');

Route::delete('oferta/{oferta}', [OfertaController::class, 'destroy'])
    ->name('oferta.destroy')
    ->middleware('auth');

Route::put('oferta/{oferta}/restore', [OfertaController::class, 'restore'])
    ->name('oferta.restore')
    ->middleware('auth');

// Branza

Route::get('branza', [BranzaController::class, 'index'])
    ->name('branza')
    ->middleware('auth');

Route::get('branza/create', [BranzaController::class, 'create'])
    ->name('branza.create')
    ->middleware('auth');

Route::post('branza', [BranzaController::class, 'store'])
    ->name('branza.store')
    ->middleware('auth');

Route::get('branza/{branza}/edit', [BranzaController::class, 'edit'])
    ->name('branza.edit')
    ->middleware('auth');

Route::post('branza/{branza}', [BranzaController::class, 'update'])
    ->name('branza.update')
    ->middleware('auth');

Route::delete('branza/{branza}', [BranzaController::class, 'destroy'])
    ->name('branza.destroy')
    ->middleware('auth');

Route::put('branza/{branza}/restore', [BranzaController::class, 'restore'])
    ->name('branza.restore')
    ->middleware('auth');

// Zakres

Route::get('zakres', [ZakresController::class, 'index'])
    ->name('zakres')
    ->middleware('auth');

Route::get('zakres/create', [ZakresController::class, 'create'])
    ->name('zakres.create')
    ->middleware('auth');

Route::post('zakres', [ZakresController::class, 'store'])
    ->name('zakres.store')
    ->middleware('auth');

Route::get('zakres/{zakres}/edit', [ZakresController::class, 'edit'])
    ->name('zakres.edit')
    ->middleware('auth');

Route::post('zakres/{zakres}', [ZakresController::class, 'update'])
    ->name('zakres.update')
    ->middleware('auth');

Route::delete('zakres/{zakres}', [ZakresController::class, 'destroy'])
    ->name('zakres.destroy')
    ->middleware('auth');

Route::put('zakres/{zakres}/restore', [ZakresController::class, 'restore'])
    ->name('zakres.restore')
    ->middleware('auth');

// Status Oferta

Route::get('ofertastatus', [OfertaStatusController::class, 'index'])
    ->name('ofertastatus')
    ->middleware('auth');

Route::get('ofertastatus/create', [OfertaStatusController::class, 'create'])
    ->name('ofertastatus.create')
    ->middleware('auth');

Route::post('ofertastatus', [OfertaStatusController::class, 'store'])
    ->name('ofertastatus.store')
    ->middleware('auth');

Route::get('ofertastatus/{ofertastatus}/edit', [OfertaStatusController::class, 'edit'])
    ->name('ofertastatus.edit')
    ->middleware('auth');

Route::post('ofertastatus/{ofertastatus}', [OfertaStatusController::class, 'update'])
    ->name('ofertastatus.update')
    ->middleware('auth');

Route::delete('ofertastatus/{ofertastatus}', [OfertaStatusController::class, 'destroy'])
    ->name('ofertastatus.destroy')
    ->middleware('auth');

Route::put('ofertastatus/{ofertastatus}/restore', [OfertaStatusController::class, 'restore'])
    ->name('ofertastatus.restore')
    ->middleware('auth');

// Kraj

Route::get('kraj', [KrajController::class, 'index'])
    ->name('kraj')
    ->middleware('auth');

Route::get('kraj/create', [KrajController::class, 'create'])
    ->name('kraj.create')
    ->middleware('auth');

Route::post('kraj', [KrajController::class, 'store'])
    ->name('kraj.store')
    ->middleware('auth');

Route::get('kraj/{kraj}/edit', [KrajController::class, 'edit'])
    ->name('kraj.edit')
    ->middleware('auth');

Route::post('kraj/{kraj}', [KrajController::class, 'update'])
    ->name('kraj.update')
    ->middleware('auth');

Route::delete('kraj/{kraj}', [KrajController::class, 'destroy'])
    ->name('kraj.destroy')
    ->middleware('auth');

Route::put('kraj/{kraj}/restore', [KrajController::class, 'restore'])
    ->name('kraj.restore')
    ->middleware('auth');

// Edit

Route::get('edit', [EditController::class, 'index'])
    ->name('edit')
    ->middleware('auth');
