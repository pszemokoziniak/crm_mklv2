<?php
// Test dymny CRM: wybrane strony GET (tylko odczyt) jako super-admin, przez
// kernel w kontenerze. Wynik: status i rozmiar, do porównania przed/po zmianie
// PHP albo Laravela. Uruchomienie: docker exec -u www-data crm-app php docker/test-dymny.php
// Pominięte celowo GET-y ze skutkami ubocznymi: */click (liczniki),
// zapytania/*/deletewznowienie, backup, notifications, logowanie i SSO.
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Admin z uzupełnionym profilem — inaczej EnsureProfileIsComplete odsyła każdą
// stronę na complete-profile (302).
$admin = App\Models\User::role(['super-admin', 'Administrator'])->get()
    ->first(fn ($u) => $u->first_name && $u->last_name
        && strtoupper(trim($u->first_name)) !== 'N/A' && strtoupper(trim($u->last_name)) !== 'N/A');

$uris = ['/', 'activity', 'branza', 'branza/create', 'calendar', 'clients', 'clients/create',
    'faza', 'faza/create', 'futureproject', 'futureproject/create', 'kontakt', 'kontakt/create',
    'kraj', 'kraj/create', 'kursy', 'kursy/create', 'linkedin', 'linkedin/create', 'menu', 'menu/create',
    'notifications/count', 'objekt', 'objekt/create', 'oferta', 'oferta/create', 'ofertastatus',
    'ofertastatus/create', 'reminder-rules', 'reminder-rules/create',
    'reports', 'search?q=a', 'stats', 'stronywww', 'stronywww/create', 'terminy', 'uprawnienia',
    'uprawnienia/create', 'users', 'users/create', 'waluta', 'waluta/create', 'zadania', 'zadania/create',
    'zakres', 'zakres/create', 'zapytania', 'zapytania/create', 'zgloszenia', 'zgloszenia/create'];

// Strony edycji: najnowszy rekord każdego rodzaju.
foreach ([
    'clients/%d/edit' => App\Models\Client::class,
    'kontakt/%d/index' => App\Models\Client::class,
    'kontaktperson/%d/index' => App\Models\Client::class,
    'oferta/%d/edit' => App\Models\Oferta::class,
    'zapytania/%d/edit' => App\Models\Zapytania::class,
    'zadania/%d/edit' => App\Models\Zadania::class,
    'zgloszenia/%d' => App\Models\Zgloszenie::class,
    'users/%d/edit' => App\Models\User::class,
] as $wzor => $model) {
    $id = $model::query()->orderByDesc('id')->value('id');
    if ($id) {
        $uris[] = sprintf($wzor, $id);
    }
}

$kernel = app(Illuminate\Contracts\Http\Kernel::class);
echo 'PHP ', PHP_VERSION, "\n";
foreach ($uris as $u) {
    app('auth')->guard('web')->setUser($admin);
    $resp = $kernel->handle(Request::create('/'.ltrim($u, '/'), 'GET'));
    if (method_exists($resp, 'getFile')) {
        $size = $resp->getFile()->getSize();
    } else {
        ob_start();
        $resp->sendContent();
        $size = strlen(ob_get_clean());
    }
    printf("%-35s %d %8d\n", preg_replace('#/\d+(/|$)#', '/ID$1', $u), $resp->getStatusCode(), $size);
}
