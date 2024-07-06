<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrowseResource;
use App\Traits\Orders\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BrowseController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        /** @var \App\Models\Scan $scan */
        $scan = $request->scan;

        /** @var \App\Models\Table $table */
        $table = $request->table;

        if ($scan->order && $scan->order->status === Status::Pending) {
            return response()->json(BrowseResource::make([
                'scan' => $scan,
                'table' => $table,
                'summary' => $scan->order->number,
                'meta' => [
                    'message' => 'Order is already made, please proceed to payment',
                    'code' => Response::HTTP_OK,
                ],
            ]), Response::HTTP_OK);
        }

        if ($scan->finished) {
            return response()->json(BrowseResource::make([
                'scan' => $scan,
                'table' => $table,
                'summary' => $scan->order->number,
                'meta' => [
                    'message' => 'Order has been completed',
                    'code' => Response::HTTP_BAD_REQUEST,
                ],
            ]), Response::HTTP_BAD_REQUEST);

            return;
        }

        if ($scan->updated_at->diffInHours() > 1) {
            $scan->delete();

            return response()->json(BrowseResource::make([
                'scan' => null,
                'table' => null,
                'meta' => [
                    'message' => 'Please rescan the QRCode',
                    'code' => Response::HTTP_FORBIDDEN,
                ],
            ]), Response::HTTP_FORBIDDEN);
        }

        if (! $scan->table) {
            return response()->json(BrowseResource::make([
                'scan' => $scan,
                'table' => null,
                'meta' => [
                    'message' => 'Please rescan the QRCode',
                    'code' => Response::HTTP_FORBIDDEN,
                ],
            ]), Response::HTTP_FORBIDDEN);
        }

        return response()->json(BrowseResource::make([
            'scan' => $scan,
            'table' => $table,
            'meta' => [
                'message' => 'Scanned successfully',
                'code' => Response::HTTP_OK,
            ],
        ]), Response::HTTP_OK);
    }
}
