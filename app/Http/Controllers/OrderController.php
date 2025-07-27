<?php

namespace App\Http\Controllers;

use App\Events\Application\OrderService;
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
}