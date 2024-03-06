<?php

use Illuminate\Support\Facades\Route;
use Scolmore\ZeroTrust\ZeroTrust;

Route::group(['middleware' => 'web', 'prefix' => 'zero-trust'], static function () {
    Route::get(
        uri: 'callback',
        action: [ZeroTrust::class, 'callback']
    )->name('zero-trust.callback');

    Route::get(
        uri: 'select-directory/{directory}',
        action: [ZeroTrust::class, 'selectedDirectory']
    )->name('zero-trust.select-directory');

    Route::post(
        uri: 'logout',
        action: [ZeroTrust::class, 'logout']
    )->name('zero-trust.logout');

    Route::get(
        uri: 'session-finished',
        action: [ZeroTrust::class, 'finished']
    )->name('zero-trust.finished')->middleware('guest');
});
