<?php

use App\Http\Controllers\Webhooks\PaymentSuccessController;
use Illuminate\Support\Facades\Route;

Route::post('/{merchant}/success', PaymentSuccessController::class)->name('payment.success');
