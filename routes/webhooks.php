<?php

use App\Http\Controllers\Webhooks\PaymentSuccessController;
use Illuminate\Support\Facades\Route;

Route::post('/payments/success', PaymentSuccessController::class)->name('payment.success');
