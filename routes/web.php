<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('verify-email/{id}/{hash}/{expires}', function($id, $hash, $expires) {
    return [
        'hash' => $hash,
        'id' => $id,
        "expires" => $expires
    ];
})->name('verification.verify');

Route::get('/reset-password/{token}/{hash}', function ($token, $hash) {
    return [
        "token" => $token,
        "hash" => $hash
    ];
})->middleware('guest')->name('password.reset');

