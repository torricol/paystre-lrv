<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    Route::view('streaming-services', 'pages.streaming-services')->name('streaming-services');
    Route::view('accounts', 'pages.accounts')->name('accounts');
    Route::view('clients', 'pages.clients')->name('clients');
    Route::view('subscriptions', 'pages.subscriptions')->name('subscriptions');
    Route::view('payments', 'pages.payments')->name('payments');
    Route::view('notifications', 'pages.notifications')->name('notifications');
    Route::view('templates', 'pages.templates')->name('templates');
    Route::view('reports', 'pages.reports')->name('reports');
    Route::view('settings', 'pages.settings')->name('settings');
});

require __DIR__.'/auth.php';
require __DIR__.'/deploy.php';
