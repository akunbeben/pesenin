<?php

use App\Livewire\Browse;
use App\Livewire\Redirector;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['link'])->group(function () {
    Route::get('/redirector', Redirector::class)->name('redirector');
    Route::get('/{table:uuid}', Browse::class)->name('browse');
});
