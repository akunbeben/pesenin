<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentSuccessController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        logger()->info($request->keys());
        logger()->info($request->headers->get('x-callback-token'));

        return response()->json([
            'success' => true,
            'message' => 'Successfully',
        ]);
    }
}
