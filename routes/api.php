<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\PartnerController;
use Illuminate\Support\Facades\Route;

Route::get('/partners', [PartnerController::class, 'list'])->name('partner.list');
Route::post('/partners', [PartnerController::class, 'create'])->name('partner.create');

Route::get('/events', [EventController::class, 'list'])->name('event.list');
Route::post('/events', [EventController::class, 'create'])->name('event.create');