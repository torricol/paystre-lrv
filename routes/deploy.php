<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/deploy/migrate', function () {
    Artisan::call('migrate', ['--force' => true]);
    // return '<pre>' . Artisan::output() . '</pre>';

    $output = Artisan::output(); // Captura lo que saldría en la terminal

    return '<h1>Éxito</h1><pre>'.$output.'</pre>';
});

Route::get('/deploy/seed', function () {
    Artisan::call('db:seed', ['--force' => true]);
    // return '<pre>'.Artisan::output().'</pre>';

    $output = Artisan::output(); // Captura lo que saldría en la terminal

    return '<h1>Éxito</h1><pre>'.$output.'</pre>';
});

Route::get('/deploy/migrate-seed', function () {
    Artisan::call('migrate', ['--seed' => true, '--force' => true]);
    // return '<pre>'.Artisan::output().'</pre>';

    $output = Artisan::output(); // Captura lo que saldría en la terminal

    return '<h1>Éxito</h1><pre>'.$output.'</pre>';
});

Route::get('/deploy/migrate-fresh-seed', function () {
    Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);

    // return '<pre>'.Artisan::output().'</pre>';
    $output = Artisan::output(); // Captura lo que saldría en la terminal

    return '<h1>Éxito</h1><pre>'.$output.'</pre>';
});
