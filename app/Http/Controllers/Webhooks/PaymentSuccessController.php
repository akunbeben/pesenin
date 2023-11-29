<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentSuccessController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        abort_if($request->headers->get('x-callback-token') !== config('services.xendit.verification_key'), 404);

        Payment::query()->create($request->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Successfully',
        ]);
    }
}
