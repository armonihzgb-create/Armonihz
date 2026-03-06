<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$app->make('config');

use Illuminate\Support\Facades\Mail;

Mail::raw('Correo de prueba SMTP desde Armonihz.', function ($m) {
    $m->to('armonihzgb@gmail.com')->subject('✅ Test SMTP Armonihz');
});

echo "Correo enviado correctamente!\n";
