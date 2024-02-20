<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Order;
use App\Traits\Orders\Serving;
use App\Traits\Orders\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentSuccessController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Merchant $merchant, Request $request)
    {
        $merchant = !$merchant->getKey()
            ? Merchant::query()->firstWhere('uuid', $request->merchant)
            : $merchant;

        abort_if($request->headers->get('x-callback-token') !== $merchant->webhook_token, 404);

        $order = Order::query()->firstWhere('number', $request->external_id);

        try {
            DB::transaction(function () use ($order, $request, $merchant) {
                $order->update([
                    'status' => match ($request->status) {
                        'UNPAID' => Status::Pending,
                        'PAID', 'SETTLED' => Status::Success,
                        'EXPIRED' => Status::Expired,
                    },
                    'serving' => match ($request->status) {
                        'UNPAID' => Serving::NotReady,
                        'PAID', 'SETTLED' => Serving::Waiting,
                        'EXPIRED' => Serving::NotReady,
                    }
                ]);

                $order->payment()->create([
                    'merchant_id' => $merchant->getKey(),
                    'business_id' => $merchant->business_id,
                    'event' => 'invoice.updated',
                    'data' => $request->toArray(),
                ]);
            });
        } catch (\Throwable $th) {
            logger()->error($th->getMessage(), $th->getTrace());

            return response()->json([
                'success' => false,
                'message' => 'Invoice update failed',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice updated',
        ]);
    }
}
