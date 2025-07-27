<?php

namespace App\Http\Controllers;

use App\Events\Application\OrderService;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    )
    {
    }

    public function list(): JsonResponse
    {
        $orders = $this->orderService->list();
        return response()->json($orders->toArray());
    }

    public function create(StoreOrderRequest $request): JsonResponse{
        $payload = $request->validated();

        $order = $this->orderService->create($payload);
        return response()->json($order->toArray(), 201);
    }
}