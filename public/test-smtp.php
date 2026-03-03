<?php

use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "<h1>Szczegółowy Test SMTP</h1>";

try {
    $host = env('MAIL_HOST');
    $port = env('MAIL_PORT');
    $user = env('MAIL_USERNAME');
    $pass = env('MAIL_PASSWORD');
    $enc  = env('MAIL_ENCRYPTION');

    echo "Łączenie z: $host:$port ($enc) jako $user...<br>";

    // Używamy bezpośrednio transportu Symfony, aby wyciągnąć logi
    $transport = new EsmtpTransport($host, $port, $enc === 'tls');
    $transport->setUsername($user);
    $transport->setPassword($pass);

    $mailer = new Mailer($transport);

    $email = (new Email())
        ->from(env('MAIL_FROM_ADDRESS'))
        ->to('pszemo.koziniak@gmail.com')
        ->subject('Test Debug SMTP - ' . date('H:i:s'))
        ->text('Treść testowa');

    $mailer->send($email);

    echo "<p style='color: green;'>Serwer SMTP zaakceptował wiadomość!</p>";
    echo "<p>Jeśli mail nadal nie dotarł do pszemo.koziniak@gmail.com, sprawdź czy nie trafił do SPAMU lub czy domena mkl.pl ma poprawne rekordy SPF.</p>";

} catch (\Exception $e) {
    echo "<p style='color: red;'>BŁĄD: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
