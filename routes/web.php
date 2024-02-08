<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BackUpController;
use App\Http\Controllers\BranzaController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EditController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FazaController;
use App\Http\Controllers\FutureProjectController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\KontaktController;
use App\Http\Controllers\KontaktPersonController;
use App\Http\Controllers\KrajController;
use App\Http\Controllers\KursyController;
use App\Http\Controllers\LinkedinController;
use App\Http\Controllers\ObjektController;
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\OfertaStatusController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\StronyWwwController;
use App\Http\Controllers\UprawnieniaController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WalutaController;
use App\Http\Controllers\ZadaniaController;
use App\Http\Controllers\ZakresController;
use App\Http\Controllers\ZapytaniaController;
use App\Models\OfertaStatus;
use App\Models\StronyWww;
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

// Kontakt

Route::get('kontakt/{client_id}/index', [KontaktController::class, 'index'])
    ->name('kontakt')
    ->middleware('auth');

Route::get('kontakt/create/{client}/{kontaktPerson_id}', [KontaktController::class, 'create'])
    ->name('kontakt.create')
    ->middleware('auth');

Route::post('kontakt/post/{client}', [KontaktController::class, 'store'])
    ->name('kontakt.store')
    ->middleware('auth');

Route::get('kontakt/{kontakt}/edit', [KontaktController::class, 'edit'])
    ->name('kontakt.edit')
    ->middleware('auth');

Route::put('kontakt/{kontakt}', [KontaktController::class, 'update'])
    ->name('kontakt.update')
    ->middleware('auth');

Route::delete('kontakt/{kontakt}', [KontaktController::class, 'destroy'])
    ->name('kontakt.destroy')
    ->middleware('auth');

Route::put('kontakt/{kontakt}/restore', [KontaktController::class, 'restore'])
    ->name('kontakt.restore')
    ->middleware('auth');

// KontaktPerson

Route::get('kontaktperson/{client_id}/index', [KontaktPersonController::class, 'index'])
    ->name('kontaktperson')
    ->middleware('auth');

Route::get('kontaktperson/create/{client}', [KontaktPersonController::class, 'create'])
    ->name('kontaktperson.create')
    ->middleware('auth');

Route::post('kontaktperson/post/{client}', [KontaktPersonController::class, 'store'])
    ->name('kontaktperson.post.store')
    ->middleware('auth');

Route::get('kontaktperson/{kontaktPerson}/edit', [KontaktPersonController::class, 'edit'])
    ->name('kontaktperson.edit')
    ->middleware('auth');

Route::put('kontaktperson/{kontaktPerson}', [KontaktPersonController::class, 'update'])
    ->name('kontaktperson.update')
    ->middleware('auth');

Route::delete('kontaktperson/{kontaktPerson}', [KontaktPersonController::class, 'destroy'])
    ->name('kontaktperson.destroy')
    ->middleware('auth');

Route::put('kontaktperson/{kontaktPerson}/restore', [KontaktPersonController::class, 'restore'])
    ->name('kontaktperson.restore')
    ->middleware('auth');

// Reports

Route::get('reports', [ReportsController::class, 'index'])
    ->name('reports')
    ->middleware('auth');

// BackUp

Route::get('backup', [BackUpController::class, 'index'])
    ->name('backup')
    ->middleware('auth');

Route::get('backup/store', [BackUpController::class, 'store'])
    ->name('backup.store')
    ->middleware('auth');

Route::get('backup/download/{file}', [BackUpController::class, 'downloadBackUp'])
    ->name('backup.download')
    ->middleware('auth');

// Calendar

Route::get('calendar', [CalendarController::class, 'index'])
    ->name('calendar')
    ->middleware('auth');

// Statystyki

Route::get('stats', [StatsController::class, 'index'])
    ->name('stats')
    ->middleware('auth');

// Activity Log

Route::get('activity', [ActivityLogController::class, 'index'])
    ->name('activity')
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

Route::delete('zapytania/{zapytania}/destroy', [ZapytaniaController::class, 'destroy'])
    ->name('zapytania.destroy')
    ->middleware('auth');

Route::put('zapytania/{zapytania}/restore', [ZapytaniaController::class, 'restore'])
    ->name('zapytania.restore')
    ->middleware('auth');


Route::get('zapytania/{zapytania}/pdf', [ZapytaniaController::class, 'pdf'])
    ->name('zapytania.pdf')
    ->middleware('auth');

Route::post('zapytania/{zapytania}/wznowienie', [ZapytaniaController::class, 'wznowienie'])
    ->name('zapytania.wznowienie')
    ->middleware('auth');

Route::post('zapytania/{zapytania}/storeWznowienie', [ZapytaniaController::class, 'storeWznowienie'])
    ->name('zapytania.wznowienie.store')
    ->middleware('auth');

//  Mail Zapytania

Route::get('zapytania/{zapytania}/mail', [ZapytaniaController::class, 'mail'])
    ->name('mail.zapytania')
    ->middleware('auth');
// Wznowinie zapytania

//Route::get('zapytania/{zapytania}/wznowienie', [ZapytaniaController::class, 'wznowienie'])
//    ->name('zapytania.wznowienie')
//    ->middleware('auth');

Route::get('zapytania/{zapytania}/deletewznowienie', [ZapytaniaController::class, 'deleteWznowienie'])
    ->name('zapytania.delete.wznowienie')
    ->middleware('auth');

// Future Projects

Route::get('futureproject', [FutureProjectController::class, 'index'])
    ->name('futureproject')
    ->middleware('auth');

Route::get('futureproject/create', [FutureProjectController::class, 'create'])
    ->name('futureproject.create')
    ->middleware('auth');

Route::post('futureproject', [FutureProjectController::class, 'store'])
    ->name('futureproject.store')
    ->middleware('auth');

Route::get('futureproject/{futureProject}/edit', [FutureProjectController::class, 'edit'])
    ->name('futureproject.edit')
    ->middleware('auth');

Route::put('futureproject/{futureProject}', [FutureProjectController::class, 'update'])
    ->name('futureproject.update')
    ->middleware('auth');

Route::delete('futureproject/{futureProject}', [FutureProjectController::class, 'destroy'])
    ->name('futureproject.destroy')
    ->middleware('auth');

Route::put('futureproject/{futureProject}/restore', [FutureProjectController::class, 'restore'])
    ->name('futureproject.restore')
    ->middleware('auth');

// Oferta

Route::get('oferta', [OfertaController::class, 'index'])
    ->name('oferta')
    ->middleware('auth');

Route::get('oferta/create', [OfertaController::class, 'create'])
    ->name('oferta.create')
    ->middleware('auth');

Route::get('/oferta/create/data/{zapytania}/{client}', [OfertaController::class, 'createData'])
    ->name('oferta.create.data')
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

// Zadania

Route::get('zadania', [ZadaniaController::class, 'index'])
    ->name('zadania')
    ->middleware('auth');

Route::get('zadania/create', [ZadaniaController::class, 'create'])
    ->name('zadania.create')
    ->middleware('auth');

Route::post('zadania', [ZadaniaController::class, 'store'])
    ->name('zadania.store')
    ->middleware('auth');

Route::get('zadania/{zadania}/edit', [ZadaniaController::class, 'edit'])
    ->name('zadania.edit')
    ->middleware('auth');

Route::put('zadania/{zadania}', [ZadaniaController::class, 'update'])
    ->name('zadania.update')
    ->middleware('auth');

Route::delete('zadania/{zadania}', [ZadaniaController::class, 'destroy'])
    ->name('zadania.destroy')
    ->middleware('auth');

Route::put('zadania/{zadania}/restore', [ZadaniaController::class, 'restore'])
    ->name('zadania.restore')
    ->middleware('auth');

// LinkedIn

Route::get('linkedin', [LinkedinController::class, 'index'])
    ->name('linkedin')
    ->middleware('auth');

Route::get('linkedin/create', [LinkedinController::class, 'create'])
    ->name('linkedin.create')
    ->middleware('auth');

Route::post('linkedin', [LinkedinController::class, 'store'])
    ->name('linkedin.store')
    ->middleware('auth');

Route::get('linkedin/{linkedin}/edit', [LinkedinController::class, 'edit'])
    ->name('linkedin.edit')
    ->middleware('auth');

Route::post('linkedin/{linkedin}', [LinkedinController::class, 'update'])
    ->name('linkedin.update')
    ->middleware('auth');

Route::delete('linkedin/{linkedin}', [LinkedinController::class, 'destroy'])
    ->name('linkedin.destroy')
    ->middleware('auth');

Route::put('linkedin/{linkedin}/restore', [LinkedinController::class, 'restore'])
    ->name('linkedin.restore')
    ->middleware('auth');

Route::get('linkedin/{linkedin}/click', [LinkedinController::class, 'click'])
    ->name('linkedin.click')
    ->middleware('auth');

// Strony www

Route::get('stronywww', [StronyWwwController::class, 'index'])
    ->name('stronywww')
    ->middleware('auth');

Route::get('stronywww/create', [StronyWwwController::class, 'create'])
    ->name('stronywww.create')
    ->middleware('auth');

Route::post('stronywww', [StronyWwwController::class, 'store'])
    ->name('stronywww.store')
    ->middleware('auth');

Route::get('stronywww/{stronyWww}/edit', [StronyWwwController::class, 'edit'])
    ->name('stronywww.edit')
    ->middleware('auth');

Route::post('stronywww/{stronyWww}', [StronyWwwController::class, 'update'])
    ->name('stronywww.update')
    ->middleware('auth');

Route::delete('stronywww/{stronyWww}', [StronyWwwController::class, 'destroy'])
    ->name('stronywww.destroy')
    ->middleware('auth');

Route::put('stronywww/{stronyWww}/restore', [StronyWwwController::class, 'restore'])
    ->name('stronywww.restore')
    ->middleware('auth');

Route::get('stronywww/{stronyWww}/click', [StronyWwwController::class, 'click'])
    ->name('stronywww.click')
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

// Email

Route::get('email', [EmailController::class, 'index'])
    ->name('email')
    ->middleware('auth');

Route::get('email/create', [EmailController::class, 'create'])
    ->name('email.create')
    ->middleware('auth');

Route::post('email', [EmailController::class, 'store'])
    ->name('email.store')
    ->middleware('auth');

Route::get('email/{email}/edit', [EmailController::class, 'edit'])
    ->name('email.edit')
    ->middleware('auth');

Route::post('email/{email}', [EmailController::class, 'update'])
    ->name('email.update')
    ->middleware('auth');

Route::delete('email/{email}', [EmailController::class, 'destroy'])
    ->name('email.destroy')
    ->middleware('auth');

Route::put('zakres/{zakres}/restore', [EmailController::class, 'restore'])
    ->name('zakres.restore')
    ->middleware('auth');

// Uprawnienia

Route::get('uprawnienia', [UprawnieniaController::class, 'index'])
    ->name('uprawnienia')
    ->middleware('auth');

Route::get('uprawnienia/create', [UprawnieniaController::class, 'create'])
    ->name('uprawnienia.create')
    ->middleware('auth');

Route::post('uprawnienia', [UprawnieniaController::class, 'store'])
    ->name('uprawnienia.store')
    ->middleware('auth');

Route::get('uprawnienia/{uprawnienia}/edit', [UprawnieniaController::class, 'edit'])
    ->name('uprawnienia.edit')
    ->middleware('auth');

Route::post('uprawnienia/{uprawnienia}', [UprawnieniaController::class, 'update'])
    ->name('uprawnienia.update')
    ->middleware('auth');

Route::delete('uprawnienia/{uprawnienia}', [UprawnieniaController::class, 'destroy'])
    ->name('uprawnienia.destroy')
    ->middleware('auth');

Route::put('uprawnienia/{uprawnienia}/restore', [UprawnieniaController::class, 'restore'])
    ->name('uprawnienia.restore')
    ->middleware('auth');

// Waluta

Route::get('waluta', [WalutaController::class, 'index'])
    ->name('waluta')
    ->middleware('auth');

Route::get('waluta/create', [WalutaController::class, 'create'])
    ->name('waluta.create')
    ->middleware('auth');

Route::post('waluta', [WalutaController::class, 'store'])
    ->name('waluta.store')
    ->middleware('auth');

Route::get('waluta/{waluta}/edit', [WalutaController::class, 'edit'])
    ->name('waluta.edit')
    ->middleware('auth');

Route::post('waluta/{waluta}', [WalutaController::class, 'update'])
    ->name('waluta.update')
    ->middleware('auth');

Route::delete('waluta/{waluta}', [WalutaController::class, 'destroy'])
    ->name('waluta.destroy')
    ->middleware('auth');

Route::put('waluta/{waluta}/restore', [WalutaController::class, 'restore'])
    ->name('waluta.restore')
    ->middleware('auth');

// Objekty

Route::get('objekt', [ObjektController::class, 'index'])
    ->name('objekt')
    ->middleware('auth');

Route::get('objekt/create', [ObjektController::class, 'create'])
    ->name('objekt.create')
    ->middleware('auth');

Route::post('objekt', [ObjektController::class, 'store'])
    ->name('objekt.store')
    ->middleware('auth');

Route::get('objekt/{objekt}/edit', [ObjektController::class, 'edit'])
    ->name('objekt.edit')
    ->middleware('auth');

Route::post('objekt/{objekt}', [ObjektController::class, 'update'])
    ->name('objekt.update')
    ->middleware('auth');

Route::delete('objekt/{objekt}', [ObjektController::class, 'destroy'])
    ->name('objekt.destroy')
    ->middleware('auth');

Route::put('objekt/{objekt}/restore', [ObjektController::class, 'restore'])
    ->name('objekt.restore')
    ->middleware('auth');

// Faza

Route::get('faza', [FazaController::class, 'index'])
    ->name('faza')
    ->middleware('auth');

Route::get('faza/create', [FazaController::class, 'create'])
    ->name('faza.create')
    ->middleware('auth');

Route::post('faza', [FazaController::class, 'store'])
    ->name('faza.store')
    ->middleware('auth');

Route::get('faza/{faza}/edit', [FazaController::class, 'edit'])
    ->name('faza.edit')
    ->middleware('auth');

Route::post('faza/{faza}', [FazaController::class, 'update'])
    ->name('faza.update')
    ->middleware('auth');

Route::delete('faza/{faza}', [FazaController::class, 'destroy'])
    ->name('faza.destroy')
    ->middleware('auth');

Route::put('faza/{faza}/restore', [FazaController::class, 'restore'])
    ->name('faza.restore')
    ->middleware('auth');

// Kursy

Route::get('kursy', [KursyController::class, 'index'])
    ->name('kursy')
    ->middleware('auth');

Route::get('kursy/create', [KursyController::class, 'create'])
    ->name('kursy.create')
    ->middleware('auth');

Route::post('kursy', [KursyController::class, 'store'])
    ->name('kursy.store')
    ->middleware('auth');

Route::get('kursy/{kursy}/edit', [KursyController::class, 'edit'])
    ->name('kursy.edit')
    ->middleware('auth');

Route::post('kursy/{kursy}', [KursyController::class, 'update'])
    ->name('kursy.update')
    ->middleware('auth');

Route::delete('kursy/{kursy}', [KursyController::class, 'destroy'])
    ->name('kursy.destroy')
    ->middleware('auth');

Route::put('kursy/{kursy}/restore', [KursyController::class, 'restore'])
    ->name('kursy.restore')
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

// Block edit access

Route::post('users/{user}/block', [UsersController::class, 'block'])
    ->name('users.block')
    ->middleware('auth');

Route::post('users/{user}/unblock', [UsersController::class, 'unblock'])
    ->name('users.unblock')
    ->middleware('auth');

