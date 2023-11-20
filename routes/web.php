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

Route::get('redirector', Redirector::class)->name('redirector');
Route::get('scan/{encrypted}', fn (string $encrypted) => $encrypted);
Route::get('{scanId}/summary', Browse::class)->name('summary');
Route::get('{scanId}', Browse::class)->name('browse');
