<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductsResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        /** @var \App\Models\Scan $scan */
        $scan = $request->scan;

        $scan->load(['table.merchant']);

        $products = Product::query()
            ->where('merchant_id', $scan->table->merchant_id)
            ->where('availability', true)
            ->paginate($request->query('per_page', 12), page: $request->query('page'))
            ->appends('u', $request->query('u'));

        return response()->json(
            ProductsResource::collection($products)->resource,
            Response::HTTP_OK
        );
    }
}
