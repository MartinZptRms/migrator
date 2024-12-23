<?php

use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

    Route::resource('databases', DatabaseController::class);

    Route::get('databases/{database}/tables/sync', [TableController::class, 'sync'])->name('databases.tables.sync');
    Route::resource('databases.tables', TableController::class);
});
