<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/device/data', function (Request $request) {
    $payload = $request->all();
    Log::info('Payload primit de la dispozitiv:', $payload);
    return response()->json([
        'status' => 'success',
        'message' => 'Datele au fost primite și logate cu succes.',
        'received' => $payload,
    ]);
});
