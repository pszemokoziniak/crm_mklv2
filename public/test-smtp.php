<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "<h1>Log Wysyłki Laravel (SwiftMailer) v6</h1>";

try {
    $mailer = app('mailer');
    $transport = $mailer->getSwiftMailer()->getTransport();

    if ($transport instanceof \Swift_SmtpTransport) {
        echo "Używam transportu SMTP: " . $transport->getHost() . ":" . $transport->getPort() . "<br>";
    }

    // Logger do przechwytywania rozmowy SMTP
    $logger = new \Swift_Plugins_Loggers_EchoLogger();
    $transport->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));

    echo "<pre style='background: #f4f4f4; padding: 10px; border: 1px solid #ccc;'>";

    \Illuminate\Support\Facades\Mail::raw('Testowa treść z CRM MKL v6', function ($message) {
        $message->to('pszemo.koziniak@gmail.com')
                ->subject('Test SMTP Log v6 - ' . date('H:i:s'));
    });

    echo "</pre>";
    echo "<h2 style='color: green;'>Wygląda na to, że wysłano! Sprawdź skrzynkę.</h2>";

} catch (\Exception $e) {
    echo "</pre>";
    echo "<h2 style='color: red;'>BŁĄD: " . $e->getMessage() . "</h2>";
    echo "<h3>Pełny ślad błędu:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
