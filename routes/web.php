<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('login_staff');
});

// Updated login routes
Route::get('/login_staff', [ApiController::class, 'showLoginForm'])->name('login_staff');
Route::post('/login_staff', [ApiController::class, 'login'])->name('login_staff.post');


Route::get('/films/create', [ApiController::class, 'create'])->name('films.create');
Route::post('/films', [ApiController::class, 'store'])->name('films.store');
Route::get('/films/{film}/edit', [ApiController::class, 'edit'])->name('films.edit');
Route::put('/films/{id}', [ApiController::class, 'update'])->name('films.update');
Route::delete('/films/{id}', [ApiController::class, 'destroy'])->name('films.destroy');
Route::get('/films/{film}', [ApiController::class, 'getFilmDetail'])->name('detail');
Route::get('/films', [ApiController::class, 'getFilms'])->name('films.index');

Route::get('/stocks', [ApiController::class, 'getStock'])->name('stocks');


Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::get('/detail', function () {
    return view('detail');
});

Route::get('/stock-films', function () {
    try {
        $server = env('SERVEUR', 'localhost');
        $port = env('PORT', '8080');
        $baseUrl = "{$server}{$port}";
        $url = "http://{$baseUrl}/toad/inventory/stockFilm";

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json();
        }

        return response()->json(['error' => 'Failed to fetch data from API'], 500);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error connecting to the API: ' . $e->getMessage()], 500);
    }
});

Route::get('/films/{filmId}', [ApiController::class, 'getFilmDetail'])->name('detail');
