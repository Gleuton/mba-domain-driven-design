<?php

use Illuminate\Support\Facades\Route;


Route::get('/users', static function () {
    return response()->json(['message' => 'List of users']);
});

Route::get('/posts', static function () {
    return response()->json(['message' => 'List of posts']);
});
