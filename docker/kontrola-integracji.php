<?php
// Kontrola integracji CRM (API dla HRM, SSO, PDF, maile, push, historia zmian,
// miniatury, harmonogram). Uruchomienie: docker exec -u www-data crm-app php docker/kontrola-integracji.php
// Wszystko, co pisze do bazy, idzie w transakcji wycofywanej na końcu;
// maile i powiadomienia są podstawione (fake), nic nie wychodzi na zewnątrz.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

$wynik = function (string $nazwa, callable $f) {
    try {
        echo str_pad($nazwa, 30), $f(), "\n";
    } catch (Throwable $e) {
        echo str_pad($nazwa, 30), 'BŁĄD ', get_class($e), ': ', mb_substr($e->getMessage(), 0, 160), "\n";
    }
};
$kernel = app(Illuminate\Contracts\Http\Kernel::class);
$get = fn (string $uri, array $h = []) => $kernel->handle(Illuminate\Http\Request::create($uri, 'GET', [], [], [], $h));
$admin = App\Models\User::role(['super-admin', 'Administrator'])->get()
    ->first(fn ($u) => $u->first_name && $u->last_name && strtoupper(trim($u->first_name)) !== 'N/A' && strtoupper(trim($u->last_name)) !== 'N/A');

echo 'Laravel ', app()->version(), ' / PHP ', PHP_VERSION, "\n";

$wynik('API z tokenem HRM', function () use ($get) {
    $r = $get('/api/clients?q=a', ['HTTP_AUTHORIZATION' => 'Bearer '.config('services.hrm.token'), 'HTTP_ACCEPT' => 'application/json']);
    return $r->getStatusCode().', wyników: '.count(json_decode($r->getContent(), true) ?? []);
});
$wynik('API bez tokenu', fn () => (string) $get('/api/clients?q=a', ['HTTP_ACCEPT' => 'application/json'])->getStatusCode());

$wynik('SSO wejście (HRM → CRM)', function () use ($get, $admin) {
    $token = app(App\Services\Sso::class)->podpisz($admin->email);
    $r = $get('/sso/wejscie?token='.urlencode($token));
    $cel = parse_url((string) $r->headers->get('Location'), PHP_URL_PATH);
    $ok = auth()->guard('web')->id() === $admin->id ? 'zalogowany' : 'NIEzalogowany';
    auth()->guard('web')->logout();
    return $r->getStatusCode().' → '.$cel.', '.$ok;
});
$wynik('SSO zły token', function () use ($get) {
    $r = $get('/sso/wejscie?token=zly.token');
    return $r->getStatusCode().' → '.parse_url((string) $r->headers->get('Location'), PHP_URL_PATH);
});

$wynik('PDF zapytania', function () use ($get, $admin) {
    auth()->guard('web')->setUser($admin);
    $z = App\Models\Zapytania::query()->orderByDesc('id')->first();
    $r = $get('/zapytania/'.$z->id.'/pdf');
    ob_start(); $r->sendContent(); $tresc = ob_get_clean();
    return $r->getStatusCode().', '.substr($tresc, 0, 4).', '.(strlen($tresc) > 1000 ? '>1 kB' : strlen($tresc).' B');
});

$wynik('Mail zapytania (render)', function () {
    $z = App\Models\Zapytania::with('client', 'user', 'kraj', 'zakres')->orderByDesc('id')->first();
    $html = (new App\Mail\ZapytaniaMail($z, ['test@example.com']))->render();
    return 'HTML '.(strlen($html) > 500 ? '>500 B' : strlen($html).' B');
});

$wynik('Wzmianka: mail i push', function () use ($admin) {
    $note = App\Models\Note::query()->orderByDesc('id')->first();
    if (! $note) { return 'brak notatek'; }
    $n = new App\Notifications\NoteMentionNotification($note, $admin);
    $push = $n->toWebPush($admin, $n)->toArray();
    $mail = method_exists($n, 'toMail') ? (string) $n->toMail($admin)->render() : '';
    return 'push: '.($push['title'] ?? '?').' | mail: '.($mail ? 'OK' : 'brak toMail');
});

$wynik('Przypomnienia (fake, rollback)', function () {
    DB::beginTransaction();
    Notification::fake();
    Mail::fake();
    try {
        Artisan::call('reminders:dispatch');
        $n = 0; $klasy = [];
        foreach (App\Models\User::all() as $u) {
            foreach ([App\Notifications\ReminderRuleNotification::class] as $k) {
                $wyslane = Notification::sent($u, $k);
                $n += $wyslane->count();
                foreach ($wyslane as $powiadomienie) {
                    $powiadomienie->toWebPush($u, $powiadomienie)->toArray();
                    if (method_exists($powiadomienie, 'toMail')) { $powiadomienie->toMail($u)->render(); }
                }
            }
        }
        return 'powiadomień: '.$n.' (wyrenderowane mail+push)';
    } finally {
        DB::rollBack();
    }
});

$wynik('Historia zmian (rollback)', function () {
    DB::beginTransaction();
    try {
        $przed = DB::table('activity_log')->count();
        $c = App\Models\Client::query()->orderByDesc('id')->first();
        $pole = collect($c->getAttributes())
            ->filter(fn ($v, $k) => is_string($v) && $v !== '' && ! is_numeric($v)
                && ! in_array($k, ['created_at', 'updated_at', 'deleted_at'], true) && ! str_ends_with($k, '_id')
                && strtotime($v) === false)
            ->keys()->first();
        $c->update([$pole => $c->$pole.' ']);
        $ostatni = DB::table('activity_log')->orderByDesc('id')->first();
        return 'pole '.$pole.', wpisów przybyło: '.(DB::table('activity_log')->count() - $przed)
            .', zdarzenie: '.($ostatni->event ?? $ostatni->description ?? '?');
    } finally {
        DB::rollBack();
    }
});

$wynik('Miniatura Glide', function () use ($get, $admin) {
    auth()->guard('web')->setUser($admin);
    $obraz = imagecreatetruecolor(300, 200); ob_start(); imagepng($obraz); $png = ob_get_clean();
    Storage::disk('local')->put('kontrola-glide/test.png', $png);
    try {
        $r = $get('/img/kontrola-glide/test.png?w=50&h=50&fit=crop');
        $wym = method_exists($r, 'getFile') ? implode('x', array_slice(getimagesize($r->getFile()->getPathname()), 0, 2)) : '-';
        return $r->getStatusCode().', '.$r->headers->get('Content-Type').', '.$wym;
    } finally {
        Storage::disk('local')->deleteDirectory('kontrola-glide');
        Storage::disk('local')->deleteDirectory('.glide-cache/kontrola-glide');
    }
});

$wynik('Harmonogram', function () {
    Artisan::call('schedule:list');
    return str_contains(Artisan::output(), 'reminders:dispatch') ? 'reminders:dispatch zaplanowane' : 'BRAK reminders:dispatch';
});
