<?php

namespace App\Http\Controllers;

use App\Events\Application\PartnerService;
use App\Http\Requests\StorePartnerRequest;
use Illuminate\Http\JsonResponse;

class PartnerController extends Controller
{
    public function __construct(
        private readonly PartnerService $partnerService
    ) {
    }

    public function list(): JsonResponse
    {
        $partners = $this->partnerService->list();
        return response()->json($partners->toArray());
    }


    public function create(StorePartnerRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $partner = $this->partnerService->register($payload);
        return response()->json($partner->toArray(), 201);
    }
}