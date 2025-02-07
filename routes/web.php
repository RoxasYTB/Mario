<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FilmController;
use Illuminate\Support\Facades\Route;
use App\Models\Film;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/filmlist', function () {
    return view('filmlist');
})->middleware(['auth', 'verified'])->name('filmlist');

Route::get('/filmdetail', function () {
    return view('filmdetail');
})->middleware(['auth', 'verified'])->name('filmdetail');

Route::get('/rentalstats', function () {
    return view('rentalstats');
})->middleware(['auth', 'verified'])->name('rentalstats');

Route::get('/style.css', function () {
    return response()
        ->file(resource_path('css/style.css'), [
            'Content-Type' => 'text/css'
        ]);
})->name('style.css');

Route::get('/research', function () {
    return view('components.research-component');
})->middleware(['auth', 'verified'])->name('research');

Route::get('/search-filter', function () {
    return view('components.search-filter');
})->middleware(['auth', 'verified'])->name('search-filter');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/films/create', [FilmController::class, 'create'])->name('films.create');
Route::post('/films', [FilmController::class, 'store'])->name('films.store');
Route::get('/films/{id}', [FilmController::class, 'edit'])->name('films.show');
Route::get('/films/{id}/edit', [FilmController::class, 'edit'])->name('films.edit');
Route::put('/films/{id}', [FilmController::class, 'update'])->name('films.update');
Route::delete('/films/{id}', [FilmController::class, 'destroy'])->name('films.destroy');

// Route de recherche pour les films
Route::get('/film/search', [FilmController::class, 'search'])->name('films.search');

Route::put('/updateFilm/{id}', function ($id) {
    $params = [
        'title' => request()->input('title'),
        'description' => request()->input('description'),
        'releaseYear' => (int)request()->input('releaseYear', 0),
        'languageId' => (int)request()->input('languageId', 1),
        'originalLanguageId' => (int)request()->input('originalLanguageId', 1),
        'rentalDuration' => (int)request()->input('rentalDuration', 6),
        'rentalRate' => (double)request()->input('rentalRate', 0.0),
        'length' => (int)request()->input('length', 0),
        'replacementCost' => (double)request()->input('replacementCost', 0.0),
        'rating' => request()->input('rating'),
        'lastUpdate' => Carbon::now()->format('Y-m-d H:i:s'),
        'idDirector' => (int)request()->input('idDirector', 1)
    ];

    $queryParams = http_build_query($params);
    // Envoi de la requête PUT à l'API
    $response = Http::put("http://localhost:8080/toad/film/update/{$id}?{$queryParams}");

    // Vérification de la réponse de l'API
    if ($response->successful()) {
        return redirect()->route('filmlist')->with('success', 'Film mis à jour avec succès.');
    } else {
        // Log de la réponse pour le débogage
        \Log::error('Erreur lors de la mise à jour du film: ' . $response->body());
        return redirect()->back()->with('error', 'Erreur lors de la mise à jour du film: ' . $response->body() . '<br><br><b> Les données envoyées étaient </b>' . json_encode($params));
    }
});

Route::post('/addFilm', function () {
    $params = [
        'title' => request()->input('title'),
        'description' => request()->input('description'),
        'releaseYear' => (int)request()->input('releaseYear', 0),
        'languageId' => (int)request()->input('languageId', 1),
        'originalLanguageId' => (int)request()->input('originalLanguageId', 1),
        'rentalDuration' => (int)request()->input('rentalDuration', 6),
        'rentalRate' => (double)request()->input('rentalRate', 0.0),
        'length' => (int)request()->input('length', 0),
        'replacementCost' => (double)request()->input('replacementCost', 0.0),
        'rating' => request()->input('rating'),
        'lastUpdate' => '2024-10-04 11:05:15',
        'idDirector' => (int)request()->input('idDirector', 1)
    ];

    $queryParams = http_build_query($params);
    // Envoi de la requête POST à l'API
    $response = Http::post("http://localhost:8080/toad/film/add?{$queryParams}");

    // Vérification de la réponse de l'API
    if ($response->successful()) {
        return redirect()->route('filmlist')->with('success', 'Film ajouté avec succès.');
    } else {
        // Log de la réponse pour le débogage
        \Log::error('Erreur lors de l\'ajout du film: ' . $response->body());
        return redirect()->back()->with('error', 'Erreur lors de l\'ajout du film: ' . $response->body() . '<br><br><strong>Les données envoyées étaient :</strong> ' . json_encode($params));
    }
});


require __DIR__.'/auth.php';
