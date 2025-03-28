<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Add the stock-films route to the API routes
Route::get('/stock-films', function () {
    try {
        $response = Http::get('http://localhost:8080/toad/inventory/stockFilm');
        
        if ($response->successful()) {
            return $response->json();
        }
        
        return response()->json(['error' => 'Failed to fetch data from API'], 500);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error connecting to the API: ' . $e->getMessage()], 500);
    }
});


