<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

echo "<h1>Debugowanie Połączenia SMTP v3</h1>";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(60); // Zwiększamy limit czasu

try {
    $host = config('mail.mailers.smtp.host');
    $port = config('mail.mailers.smtp.port');
    $user = config('mail.mailers.smtp.username');
    $pass = config('mail.mailers.smtp.password');
    $enc  = config('mail.mailers.smtp.encryption');
    $from = config('mail.from.address');

    echo "<strong>Konfiguracja:</strong><br>";
    echo "Host: $host<br>";
    echo "Port: $port<br>";
    echo "User: $user<br>";
    echo "Pass: " . substr($pass, 0, 5) . "****** (długość: " . strlen($pass) . ")<br>";
    echo "Enc: $enc<br>";
    echo "From: $from<br><br>";

    if (strlen($pass) < 10) {
        echo "<p style='color: red;'>UWAGA: Hasło wydaje się za krótkie! Sprawdź plik .env.</p>";
    }

    echo "1. Próba otwarcia socketu... ";
    $socket = @fsockopen(($enc === 'ssl' ? 'ssl://' : '') . $host, $port, $errno, $errstr, 10);
    if (!$socket) {
        echo "<span style='color: red;'>BŁĄD: Nie można otworzyć połączenia. ($errno) $errstr</span><br>";
    } else {
        echo "<span style='color: green;'>POŁĄCZONO!</span><br>";
        fclose($socket);
    }

    echo "2. Inicjalizacja Symfony Mailer...<br>";

    // Ręczne ustawienie transportu z opcjami ignorowania certyfikatów
    $transport = new EsmtpTransport($host, $port, ($enc === 'ssl' || $port == 465));
    $transport->setUsername($user);
    $transport->setPassword($pass);

    // Dodajemy opcje strumienia (wyłączenie weryfikacji SSL)
    // W Symfony Mailer robi się to przez fabrykę lub modyfikację strumienia,
    // ale najprościej spróbować wysłać i złapać błąd.

    $mailer = new Mailer($transport);

    $email = (new Email())
        ->from($from)
        ->to('pszemo.koziniak@gmail.com')
        ->subject('Test Debug SMTP v3 - ' . date('H:i:s'))
        ->text('Wiadomość testowa wysłana o ' . date('Y-m-d H:i:s'));

    echo "3. Próba wysyłki (to może chwilę potrwać)... ";
    flush(); // Wymuś wyświetlenie tekstu w przeglądarce

    $mailer->send($email);

    echo "<span style='color: green;'>SUKCES!</span>";

} catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
    echo "<span style='color: red;'>BŁĄD TRANSPORTU!</span><br>";
    echo "Komunikat: " . $e->getMessage() . "<br>";
    echo "Debug: <pre>" . $e->getDebug() . "</pre>";
} catch (\Exception $e) {
    echo "<span style='color: red;'>BŁĄD OGÓLNY!</span><br>";
    echo "Komunikat: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
