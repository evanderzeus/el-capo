<?php

use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
});

Route::middleware('auth')->group(function () {
    Route::view('/', 'items.index')->name('home');
    Route::get('/items/{id}', fn (int $id) => view('items.show', ['id' => $id]))->whereNumber('id');

    Route::view('/my-items', 'items.mine');
    Route::view('/my-items/create', 'items.form', ['id' => null]);
    Route::get('/my-items/{id}/edit', fn (int $id) => view('items.form', ['id' => $id]))->whereNumber('id');

    Route::view('/offers', 'offers.index');
    Route::view('/profile', 'profile');
});
