<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PartnerController;
use Illuminate\Support\Facades\Route;

Route::get('/partners', [PartnerController::class, 'list'])->name('partner.list');
Route::post('/partners', [PartnerController::class, 'create'])->name('partner.create');

Route::get('/events', [EventController::class, 'list'])->name('event.list');
Route::post('/events', [EventController::class, 'create'])->name('event.create');
Route::put('/events/{id}/publish-all', [EventController::class, 'publishAll'])->name('event.publish.all');


Route::get('/orders', [OrderController::class, 'list'])->name('order.list');
//Route::post('/orders', [OrderController::class, 'create'])->name('order.create');
//Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');