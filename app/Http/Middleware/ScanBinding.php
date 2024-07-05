<?php

namespace App\Http\Middleware;

use App\Http\Resources\BrowseResource;
use App\Models\Scan;
use App\Support\Encoder;
use App\Traits\Fingerprint;
use App\Traits\Orders\Status;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScanBinding
{
    use Fingerprint;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (blank($request->u)) {
            return response()->json(BrowseResource::make([
                'scan' => null,
                'table' => null,
                'meta' => [
                    'message' => 'Invalid request',
                    'code' => Response::HTTP_BAD_REQUEST,
                ],
            ]), Response::HTTP_BAD_REQUEST);
        }

        [$id] = Encoder::decode(
            $request->u,
            $request->scanId,
        );

        $scan = Scan::query()->find($id);

        if (!$scan) {
            return response()->json(BrowseResource::make([
                'scan' => null,
                'table' => null,
                'meta' => [
                    'message' => 'Not found',
                    'code' => Response::HTTP_NOT_FOUND,
                ],
            ]), Response::HTTP_NOT_FOUND);
        }

        if ($this->fingerprint() !== $scan->fingerprint) {
            return response()->json(BrowseResource::make([
                'scan' => null,
                'table' => null,
                'meta' => [
                    'message' => 'Invalid request',
                    'code' => Response::HTTP_BAD_REQUEST,
                ],
            ]), Response::HTTP_BAD_REQUEST);
        }

        $scan->makeHidden('ip', 'agent', 'table', 'order');

        $table = $scan
            ->table
            ->load(['merchant:id,name'])
            ->makeHidden([
                'qr_status',
                'active',
                'merchant_id',
                'created_at',
                'updated_at',
                'deleted_at',
            ]);

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
        }

        if ($scan->created_at->diffInHours() > 1) {
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

        if (!$scan->table) {
            return response()->json(BrowseResource::make([
                'scan' => $scan,
                'table' => null,
                'meta' => [
                    'message' => 'Please rescan the QRCode',
                    'code' => Response::HTTP_FORBIDDEN,
                ],
            ]), Response::HTTP_FORBIDDEN);
        }

        $request->merge([
            'scan' => $scan,
            'table' => $table,
        ]);

        return $next($request);
    }
}
