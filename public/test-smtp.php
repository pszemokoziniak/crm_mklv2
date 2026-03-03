<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

echo "<h1>Debugowanie Połączenia SMTP</h1>";

// Włącz wyświetlanie wszystkich błędów
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $host = config('mail.mailers.smtp.host');
    $port = config('mail.mailers.smtp.port');
    $user = config('mail.mailers.smtp.username');
    $pass = config('mail.mailers.smtp.password');
    $enc  = config('mail.mailers.smtp.encryption');

    echo "<strong>Konfiguracja:</strong><br>";
    echo "Host: $host<br>";
    echo "Port: $port<br>";
    echo "User: $user<br>";
    echo "Enc: $enc<br><br>";

    echo "1. Próba otwarcia socketu... ";
    $socket = @fsockopen(($enc === 'ssl' ? 'ssl://' : '') . $host, $port, $errno, $errstr, 10);
    if (!$socket) {
        echo "<span style='color: red;'>BŁĄD: Nie można otworzyć połączenia do $host:$port. ($errno) $errstr</span><br>";
        echo "Prawdopodobnie serwer (firewall) blokuje połączenia wychodzące na tym porcie.<br>";
    } else {
        echo "<span style='color: green;'>POŁĄCZONO!</span><br>";
        fclose($socket);
    }

    echo "2. Próba wysyłki przez Symfony Mailer...<br>";

    // Dla portu 465 (SSL) Symfony EsmtpTransport potrzebuje true jako trzeci parametr
    $isTls = ($enc === 'tls' || $enc === 'ssl' || $port == 465);

    $transport = new EsmtpTransport($host, $port, $isTls);
    $transport->setUsername($user);
    $transport->setPassword($pass);

    $mailer = new Mailer($transport);

    $email = (new Email())
        ->from(config('mail.from.address'))
        ->to('pszemo.koziniak@gmail.com')
        ->subject('Test Debug SMTP - ' . date('H:i:s'))
        ->text('Wiadomość testowa z serwera: ' . gethostname());

    $mailer->send($email);

    echo "<p style='color: green;'>SUKCES! Wiadomość została wysłana do serwera SMTP.</p>";

} catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
    echo "<p style='color: red;'>BŁĄD TRANSPORTU: " . $e->getMessage() . "</p>";
    echo "Szczegóły: " . $e->getDebug();
} catch (\Exception $e) {
    echo "<p style='color: red;'>BŁĄD OGÓLNY: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
