<?php

use App\Livewire\Browse;
use App\Livewire\Redirector;
use App\Livewire\Summary;
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

Route::redirect('/login', '/merchant/login')->name('login');
Route::get('redirector', Redirector::class)->name('redirector');
Route::get('scan/{encrypted}', fn (string $encrypted) => $encrypted);
Route::get('{order}/summary', Summary::class)->name('summary');
Route::get('{scanId}', Browse::class)->name('browse')->where('scanId', '^(?!pulse$).*$');
