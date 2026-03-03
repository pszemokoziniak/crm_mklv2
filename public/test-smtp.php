<?php

use Illuminate\Support\Facades\Mail;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "<h1>Test wysyłki SMTP</h1>";

try {
    $to = "pszemo.koziniak@gmail.com"; // Zmień na swój adres do testu

    Mail::raw('To jest testowa wiadomość z CRM MKLBAU', function ($message) use ($to) {
        $message->to($to)
                ->subject('Test SMTP - ' . date('H:i:s'));
    });

    echo "<p style='color: green;'>Sukces! Laravel nie zgłosił błędu podczas wysyłki.</p>";
    echo "<p>Jeśli mail nie dotarł, sprawdź folder SPAM lub ustawienia serwera home.pl.</p>";

} catch (\Exception $e) {
    echo "<p style='color: red;'>BŁĄD: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
