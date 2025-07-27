<?php

namespace App\Http\Controllers;

use App\Events\Application\EventService;
use App\Http\Requests\StoreEventRequest;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function __construct(
        private readonly EventService $event
    ) {
    }

    public function list(): JsonResponse
    {
        $events = $this->event->list();
        return response()->json($events->toArray());
    }

    public function create(StoreEventRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $partner = $this->event->create($payload);
        return response()->json($partner->toArray(), 201);
    }

    public function publishAll(string $id): JsonResponse
    {
        $this->event->publishAll($id);
        return response()->json(['message' => 'All events published successfully.']);
    }
}