<?php

use App\Models\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\MigratorController;
use App\Http\Controllers\ConnectionController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        // $array = Collection::make([]);
        // for ($i = 0; $i < 1000000; $i++) {
        //     $array->push([Str::uuid()]);
        // }
        // dd($array);
        return view('dashboard');
    })->name('dashboard.index');

    Route::resource('connections', ConnectionController::class);

    Route::resource('databases', DatabaseController::class);

    Route::get('databases/{database}/tables/sync', [TableController::class, 'sync'])->name('databases.tables.sync');
    Route::post('databases/{database}/tables/columns/sync', [TableController::class, 'syncColumns'])->name('databases.tables.multiple.columns.sync');
    Route::resource('databases.tables', TableController::class);

    Route::get('databases/{database}/tables/{table}/columns/sync', [ColumnController::class, 'sync'])->name('databases.tables.columns.sync');
    Route::resource('databases.tables.columns', ColumnController::class);

    Route::get('services/{service}/setting', [ServiceController::class, 'setting'])->name('services.setting');
    Route::get('services/{service}/start', [MigratorController::class, 'start'])->name('services.start');
    Route::resource('services', ServiceController::class);
});
