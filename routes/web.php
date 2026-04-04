<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
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
use App\Http\Controllers\MenuController; // Added MenuController
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

Route::get('forgot-password', [ForgotPasswordController::class, 'create'])
    ->name('password.request')
    ->middleware('guest');

Route::post('forgot-password', [ForgotPasswordController::class, 'store'])
    ->name('password.email')
    ->middleware('guest');

Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])
    ->name('password.reset')
    ->middleware('guest');

Route::post('reset-password', [ResetPasswordController::class, 'store'])
    ->name('password.update')
    ->middleware('guest');

Route::middleware(['auth'])->group(function () {
    Route::get('complete-profile', [AuthenticatedSessionController::class, 'showCompleteProfile'])
        ->name('profile.complete');
    Route::post('complete-profile', [AuthenticatedSessionController::class, 'updateProfile'])
        ->name('profile.complete.store');
});

// Dashboard

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard')
    ->middleware('auth');

// Główne trasy aplikacji
Route::middleware(['auth'])->group(function () {

    // Sekcje ZABLOKOWANE dla roli 'eksport'
    Route::group(['middleware' => [function ($request, $next) {
        if ($request->user() && $request->user()->hasRole('eksport')) {
            abort(403, 'Brak dostępu dla Twojej roli.');
        }
        return $next($request);
    }]], function () {

        // Users
        Route::get('users', [UsersController::class, 'index'])->name('users');
        Route::get('users/create', [UsersController::class, 'create'])->name('users.create');
        Route::post('users', [UsersController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UsersController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
        Route::put('users/{user}/restore', [UsersController::class, 'restore'])->name('users.restore');

        // Edit / Ustawienia
        Route::get('edit', [EditController::class, 'index'])->name('edit');

        // Activity Log / Historia
        Route::get('activity', [ActivityLogController::class, 'index'])->name('activity');

        // Uprawnienia
        Route::get('uprawnienia', [UprawnieniaController::class, 'index'])->name('uprawnienia');
        Route::get('uprawnienia/create', [UprawnieniaController::class, 'create'])->name('uprawnienia.create');
        Route::post('uprawnienia', [UprawnieniaController::class, 'store'])->name('uprawnienia.store');
        Route::get('uprawnienia/{uprawnienia}/edit', [UprawnieniaController::class, 'edit'])->name('uprawnienia.edit');
        Route::put('uprawnienia/{uprawnienia}', [UprawnieniaController::class, 'update'])->name('uprawnienia.update');
        Route::delete('uprawnienia/{uprawnienia}', [UprawnieniaController::class, 'destroy'])->name('uprawnienia.destroy');
        Route::put('uprawnienia/{uprawnienia}/restore', [UprawnieniaController::class, 'restore'])->name('uprawnienia.restore');
//        Route::put('uprawnienia/{uprawnienia}/sync-main-menus', [UprawnieniaController::class, 'syncMainMenus'])->name('uprawnienia.syncMainMenus'); // Dodana trasa

        // Menu
        Route::get('menu', [MenuController::class, 'index'])->name('menu');
        Route::get('menu/create', [MenuController::class, 'create'])->name('menu.create');
        Route::post('menu', [MenuController::class, 'store'])->name('menu.store');
        Route::get('menu/{menu}/edit', [MenuController::class, 'edit'])->name('menu.edit');
        Route::put('menu/{menu}', [MenuController::class, 'update'])->name('menu.update');
        Route::delete('menu/{menu}', [MenuController::class, 'destroy'])->name('menu.destroy');
        Route::put('menu/{menu}/restore', [MenuController::class, 'restore'])->name('menu.restore');

        // Inne potencjalnie wrażliwe trasy ustawień
        Route::get('backup', [BackUpController::class, 'index'])->name('backup');
    });

    // Pozostałe trasy dostępne dla wszystkich (w tym dla 'eksport')

    // Organizations
    Route::get('organizations', [OrganizationsController::class, 'index'])->name('organizations');
    Route::get('organizations/create', [OrganizationsController::class, 'create'])->name('organizations.create');
    Route::post('organizations', [OrganizationsController::class, 'store'])->name('organizations.store');
    Route::get('organizations/{organization}/edit', [OrganizationsController::class, 'edit'])->name('organizations.edit');
    Route::put('organizations/{organization}', [OrganizationsController::class, 'update'])->name('organizations.update');
    Route::delete('organizations/{organization}', [OrganizationsController::class, 'destroy'])->name('organizations.destroy');
    Route::put('organizations/{organization}/restore', [OrganizationsController::class, 'restore'])->name('organizations.restore');

    // Contacts
    Route::get('contacts', [ContactsController::class, 'index'])->name('contacts');
    Route::get('contacts/create', [ContactsController::class, 'create'])->name('contacts.create');
    Route::post('contacts', [ContactsController::class, 'store'])->name('contacts.store');
    Route::get('contacts/{contact}/edit', [ContactsController::class, 'edit'])->name('contacts.edit');
    Route::put('contacts/{contact}', [ContactsController::class, 'update'])->name('contacts.update');
    Route::delete('contacts/{contact}', [ContactsController::class, 'destroy'])->name('contacts.destroy');
    Route::put('contacts/{contact}/restore', [ContactsController::class, 'restore'])->name('contacts.restore');

    // Kontakt
    Route::get('kontakt', [KontaktController::class, 'index'])->name('kontakt');
    Route::get('kontakt/{client_id}/index', [KontaktController::class, 'clientIndex'])->name('kontakt.client');
    Route::get('kontakt/create', [KontaktController::class, 'create'])->name('kontakt.create.general');
    Route::get('kontakt/create/{client}/{kontaktPerson_id?}', [KontaktController::class, 'create'])->name('kontakt.create');
    Route::post('kontakt/post/{client?}', [KontaktController::class, 'store'])->name('kontakt.store');
    Route::get('kontakt/{kontakt}/edit', [KontaktController::class, 'edit'])->name('kontakt.edit');
    Route::put('kontakt/{kontakt}', [KontaktController::class, 'update'])->name('kontakt.update');
    Route::delete('kontakt/{kontakt}', [KontaktController::class, 'destroy'])->name('kontakt.destroy');
    Route::put('kontakt/{kontakt}/restore', [KontaktController::class, 'restore'])->name('kontakt.restore');

    // KontaktPerson
    Route::get('kontaktperson/{client_id}/index', [KontaktPersonController::class, 'index'])->name('kontaktperson');
    Route::get('kontaktperson/create/{client}', [KontaktPersonController::class, 'create'])->name('kontaktperson.create');
    Route::post('kontaktperson/post/{client}', [KontaktPersonController::class, 'store'])->name('kontaktperson.post.store');
    Route::get('kontaktperson/{kontaktPerson}/edit', [KontaktPersonController::class, 'edit'])->name('kontaktperson.edit');
    Route::put('kontaktperson/{kontaktPerson}', [KontaktPersonController::class, 'update'])->name('kontaktperson.update');
    Route::delete('kontaktperson/{kontaktPerson}', [KontaktPersonController::class, 'destroy'])->name('kontaktperson.destroy');
    Route::put('kontaktperson/{kontaktPerson}/restore', [KontaktPersonController::class, 'restore'])->name('kontaktperson.restore');

    // Reports
    Route::get('reports', [ReportsController::class, 'index'])->name('reports');

    // Calendar
    Route::get('calendar', [CalendarController::class, 'index'])->name('calendar');

    // Statystyki
    Route::get('stats', [StatsController::class, 'index'])->name('stats');

    // Clients
    Route::get('clients', [ClientController::class, 'index'])->name('clients');
    Route::get('clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::put('clients/{client}/restore', [ClientController::class, 'restore'])->name('clients.restore');

    // Zapytania
    Route::get('zapytania', [ZapytaniaController::class, 'index'])->name('zapytania');
    Route::get('zapytania/create', [ZapytaniaController::class, 'create'])->name('zapytania.create');
    Route::post('zapytania', [ZapytaniaController::class, 'store'])->name('zapytania.store');
    Route::get('zapytania/{zapytania}/edit', [ZapytaniaController::class, 'edit'])->name('zapytania.edit');
    Route::put('zapytania/{zapytania}', [ZapytaniaController::class, 'update'])->name('zapytania.update');
    Route::delete('zapytania/{zapytania}/destroy', [ZapytaniaController::class, 'destroy'])->name('zapytania.destroy');
    Route::put('zapytania/{zapytania}/restore', [ZapytaniaController::class, 'restore'])->name('zapytania.restore');
    Route::get('zapytania/{zapytania}/pdf', [ZapytaniaController::class, 'pdf'])->name('zapytania.pdf');
    Route::post('zapytania/{zapytania}/wznowienie', [ZapytaniaController::class, 'wznowienie'])->name('zapytania.wznowienie');
    Route::get('zapytania/{zapytania}/wznowienia/create', [ZapytaniaController::class, 'createWznowienie'])->name('zapytania.wznowienia.create');
    Route::post('zapytania/{zapytania}/storeWznowienie', [ZapytaniaController::class, 'storeWznowienie'])->name('zapytania.wznowienie.store');
    Route::get('zapytania/{zapytania}/mail', [ZapytaniaController::class, 'mail'])->name('mail.zapytania');
    Route::get('zapytania/{zapytania}/deletewznowienie', [ZapytaniaController::class, 'deleteWznowienie'])->name('zapytania.delete.wznowienie');

    // Future Projects
    Route::get('futureproject', [FutureProjectController::class, 'index'])->name('futureproject');
    Route::get('futureproject/create', [FutureProjectController::class, 'create'])->name('futureproject.create');
    Route::post('futureproject', [FutureProjectController::class, 'store'])->name('futureproject.store');
    Route::get('futureproject/{futureProject}/edit', [FutureProjectController::class, 'edit'])->name('futureproject.edit');
    Route::put('futureproject/{futureProject}', [FutureProjectController::class, 'update'])->name('futureproject.update');
    Route::delete('futureproject/{futureProject}', [FutureProjectController::class, 'destroy'])->name('futureproject.destroy');
    Route::put('futureproject/{futureProject}/restore', [FutureProjectController::class, 'restore'])->name('futureproject.restore');

    // Oferta
    Route::get('oferta', [OfertaController::class, 'index'])->name('oferta');
    Route::get('oferta/create', [OfertaController::class, 'create'])->name('oferta.create');
    Route::get('/oferta/create/data/{zapytania}/{client}', [OfertaController::class, 'createData'])->name('oferta.create.data');
    Route::post('oferta', [OfertaController::class, 'store'])->name('oferta.store');
    Route::get('oferta/{oferta}/edit', [OfertaController::class, 'edit'])->name('oferta.edit');
    Route::put('oferta/{oferta}', [OfertaController::class, 'update'])->name('oferta.update');
    Route::delete('oferta/{oferta}', [OfertaController::class, 'destroy'])->name('oferta.destroy');
    Route::put('oferta/{oferta}/restore', [OfertaController::class, 'restore'])->name('oferta.restore');

    // Branza
    Route::get('branza', [BranzaController::class, 'index'])->name('branza');
    Route::get('branza/create', [BranzaController::class, 'create'])->name('branza.create');
    Route::post('branza', [BranzaController::class, 'store'])->name('branza.store');
    Route::get('branza/{branza}/edit', [BranzaController::class, 'edit'])->name('branza.edit');
    Route::post('branza/{branza}', [BranzaController::class, 'update'])->name('branza.update');

    // Zadania
    Route::get('zadania', [ZadaniaController::class, 'index'])->name('zadania');
    Route::get('zadania/create', [ZadaniaController::class, 'create'])->name('zadania.create');
    Route::post('zadania', [ZadaniaController::class, 'store'])->name('zadania.store');
    Route::get('zadania/{zadania}/edit', [ZadaniaController::class, 'edit'])->name('zadania.edit');
    Route::put('zadania/{zadania}', [ZadaniaController::class, 'update'])->name('zadania.update');
    Route::delete('zadania/{zadania}', [ZadaniaController::class, 'destroy'])->name('zadania.destroy');
    Route::put('zadania/{zadania}/restore', [ZadaniaController::class, 'restore'])->name('zadania.restore');

    // LinkedIn
    Route::get('linkedin', [LinkedinController::class, 'index'])->name('linkedin');
    Route::get('linkedin/create', [LinkedinController::class, 'create'])->name('linkedin.create');
    Route::post('linkedin', [LinkedinController::class, 'store'])->name('linkedin.store');
    Route::get('linkedin/{linkedin}/edit', [LinkedinController::class, 'edit'])->name('linkedin.edit');
    Route::post('linkedin/{linkedin}', [LinkedinController::class, 'update'])->name('linkedin.update');
    Route::delete('linkedin/{linkedin}', [LinkedinController::class, 'destroy'])->name('linkedin.destroy');
    Route::put('linkedin/{linkedin}/restore', [LinkedinController::class, 'restore'])->name('linkedin.restore');
    Route::get('linkedin/{linkedin}/click', [LinkedinController::class, 'click'])->name('linkedin.click');

    // Strony www
    Route::get('stronywww', [StronyWwwController::class, 'index'])->name('stronywww');
    Route::get('stronywww/create', [StronyWwwController::class, 'create'])->name('stronywww.create');
    Route::post('stronywww', [StronyWwwController::class, 'store'])->name('stronywww.store');
    Route::get('stronywww/{stronyWww}/edit', [StronyWwwController::class, 'edit'])->name('stronywww.edit');
    Route::post('stronywww/{stronyWww}', [StronyWwwController::class, 'update'])->name('stronywww.update');
    Route::delete('stronywww/{stronyWww}', [StronyWwwController::class, 'destroy'])->name('stronywww.destroy');
    Route::put('stronywww/{stronyWww}/restore', [StronyWwwController::class, 'restore'])->name('stronywww.restore');
    Route::get('stronywww/{stronyWww}/click', [StronyWwwController::class, 'click'])->name('stronywww.click');

    // Zakres
    Route::get('zakres', [ZakresController::class, 'index'])->name('zakres');
    Route::get('zakres/create', [ZakresController::class, 'create'])->name('zakres.create');
    Route::post('zakres', [ZakresController::class, 'store'])->name('zakres.store');
    Route::get('zakres/{zakres}/edit', [ZakresController::class, 'edit'])->name('zakres.edit');
    Route::post('zakres/{zakres}', [ZakresController::class, 'update'])->name('zakres.update');
    Route::delete('zakres/{zakres}', [ZakresController::class, 'destroy'])->name('zakres.destroy');
    Route::put('zakres/{zakres}/restore', [ZakresController::class, 'restore'])->name('zakres.restore');

    // Email
    Route::get('email', [EmailController::class, 'index'])->name('email');
    Route::get('email/create', [EmailController::class, 'create'])->name('email.create');
    Route::post('email', [EmailController::class, 'store'])->name('email.store');
    Route::get('email/{email}/edit', [EmailController::class, 'edit'])->name('email.edit');
    Route::post('email/{email}', [EmailController::class, 'update'])->name('email.update');
    Route::delete('email/{email}', [EmailController::class, 'destroy'])->name('email.destroy');

    // Waluta
    Route::get('waluta', [WalutaController::class, 'index'])->name('waluta');
    Route::get('waluta/create', [WalutaController::class, 'create'])->name('waluta.create');
    Route::post('waluta', [WalutaController::class, 'store'])->name('waluta.store');
    Route::get('waluta/{waluta}/edit', [WalutaController::class, 'edit'])->name('waluta.edit');
    Route::post('waluta/{waluta}', [WalutaController::class, 'update'])->name('waluta.update');
    Route::delete('waluta/{waluta}', [WalutaController::class, 'destroy'])->name('waluta.destroy');
    Route::put('waluta/{waluta}/restore', [WalutaController::class, 'restore'])->name('waluta.restore');

    // Objekty
    Route::get('objekt', [ObjektController::class, 'index'])->name('objekt');
    Route::get('objekt/create', [ObjektController::class, 'create'])->name('objekt.create');
    Route::post('objekt', [ObjektController::class, 'store'])->name('objekt.store');
    Route::get('objekt/{objekt}/edit', [ObjektController::class, 'edit'])->name('objekt.edit');
    Route::post('objekt/{objekt}', [ObjektController::class, 'update'])->name('objekt.update');
    Route::delete('objekt/{objekt}', [ObjektController::class, 'destroy'])->name('objekt.destroy');
    Route::put('objekt/{objekt}/restore', [ObjektController::class, 'restore'])->name('objekt.restore');

    // Faza
    Route::get('faza', [FazaController::class, 'index'])->name('faza');
    Route::get('faza/create', [FazaController::class, 'create'])->name('faza.create');
    Route::post('faza', [FazaController::class, 'store'])->name('faza.store');
    Route::get('faza/{faza}/edit', [FazaController::class, 'edit'])->name('faza.edit');
    Route::post('faza/{faza}', [FazaController::class, 'update'])->name('faza.update');
    Route::delete('faza/{faza}', [FazaController::class, 'destroy'])->name('faza.destroy');
    Route::put('faza/{faza}/restore', [FazaController::class, 'restore'])->name('faza.restore');

    // Kursy
    Route::get('kursy', [KursyController::class, 'index'])->name('kursy');
    Route::get('kursy/create', [KursyController::class, 'create'])->name('kursy.create');
    Route::post('kursy', [KursyController::class, 'store'])->name('kursy.store');
    Route::get('kursy/{kursy}/edit', [KursyController::class, 'edit'])->name('kursy.edit');
    Route::post('kursy/{kursy}', [KursyController::class, 'update'])->name('kursy.update');

    // Status Oferta
    Route::get('ofertastatus', [OfertaStatusController::class, 'index'])->name('ofertastatus');
    Route::get('ofertastatus/create', [OfertaStatusController::class, 'create'])->name('ofertastatus.create');
    Route::post('ofertastatus', [OfertaStatusController::class, 'store'])->name('ofertastatus.store');
    Route::get('ofertastatus/{ofertastatus}/edit', [OfertaStatusController::class, 'edit'])->name('ofertastatus.edit');
    Route::post('ofertastatus/{ofertastatus}', [OfertaStatusController::class, 'update'])->name('ofertastatus.update');
    Route::delete('ofertastatus/{ofertastatus}', [OfertaStatusController::class, 'destroy'])->name('ofertastatus.destroy');
    Route::put('ofertastatus/{ofertastatus}/restore', [OfertaStatusController::class, 'restore'])->name('ofertastatus.restore');

    // Kraj
    Route::get('kraj', [KrajController::class, 'index'])->name('kraj');
    Route::get('kraj/create', [KrajController::class, 'create'])->name('kraj.create');
    Route::post('kraj', [KrajController::class, 'store'])->name('kraj.store');
    Route::get('kraj/{kraj}/edit', [KrajController::class, 'edit'])->name('kraj.edit');
    Route::post('kraj/{kraj}', [KrajController::class, 'update'])->name('kraj.update');
    Route::delete('kraj/{kraj}', [KrajController::class, 'destroy'])->name('kraj.destroy');
    Route::put('kraj/{kraj}/restore', [KrajController::class, 'restore'])->name('kraj.restore');

    // Images
    Route::get('/img/{path}', [ImagesController::class, 'show'])->where('path', '.*')->name('image');

    // Block edit access
    Route::post('users/{user}/block', [UsersController::class, 'block'])->name('users.block');
    Route::post('users/{user}/unblock', [UsersController::class, 'unblock'])->name('users.unblock');
});
