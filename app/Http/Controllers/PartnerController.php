<?php

namespace App\Http\Controllers;

use App\Events\Application\PartnerService;
use App\Events\Domain\Entities\Partner\PartnerCollection;
use Illuminate\Http\JsonResponse;

class PartnerController extends Controller
{
    public function __construct(
        private readonly PartnerService $partnerService
    )
    {
    }

    public function list(): JsonResponse
    {
        $partners = $this->partnerService->list();
        return response()->json($partners->toArray());
    }


    public function show($id)
    {
    }

}