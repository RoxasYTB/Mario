<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FilmController extends Controller
{
    // Base URL de l'API
    private $baseUrl = 'http://localhost:8080/toad/film';

    public function index()
    {
        // Rediriger vers /filmlist
        return redirect()->route('filmlist');
    }

    public function search(Request $request)
    {
        // Rediriger vers /filmlist
        return redirect()->route('filmlist');
    }

    /**
     * Ajouter un film
     */
    public function addFilm(Request $request)
    {
        $response = Http::post("{$this->baseUrl}/add", $request->only([
            'title',
            'description',
            'releaseYear',
            'languageId',
            'originalLanguageId',
            'rentalDuration',
            'rentalRate',
            'length',
            'replacementCost',
            'rating',
            'lastUpdate',
            'idDirector'
        ]));

        return $response->json();
    }

    /**
     * Afficher les détails d'un film
     */
    public function show($id)
    {
        // Récupérer les détails du film via l'API
        $response = file_get_contents("http://localhost:8080/toad/film/getById?id={$id}");
        $film = json_decode($response);

        // Vérification si le film existe
        if (!$film) {
            abort(404, 'Film non trouvé.');
        }

        return view('films.show', compact('film'));
    }

    /**
     * Modifier un film
     */
    public function edit($filmId)
    {
        // Récupérer les détails du film pour pré-remplir le formulaire
        $response = file_get_contents("http://localhost:8080/toad/film/getById?id={$filmId}");
        $film = json_decode($response);

        if (!$film || !isset($film->filmId)) {
            return redirect()->route('filmlist')->with('error', 'Film introuvable.');
        }

        return view('films.edit', compact('film'));
    }

    /**
     * Mettre à jour un film via le formulaire
     */
    public function update(Request $request, $filmId)
    {
        // Validation des données
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'releaseYear' => 'required|integer',
            'languageId' => 'required|integer',
            'originalLanguageId' => 'required|integer',
            'rentalDuration' => 'required|integer',
            'rentalRate' => 'required|numeric',
            'length' => 'required|integer',
            'replacementCost' => 'required|numeric',
            'rating' => 'required|string|max:5',
            'lastUpdate' => 'required|date',
            'idDirector' => 'integer',
        ]);

        // Récupération des données nécessaires
        $data = $request->only([
            'title', 'description', 'releaseYear', 'languageId', 
            'originalLanguageId', 'rentalDuration', 'rentalRate', 
            'length', 'replacementCost', 'rating', 'lastUpdate', 'idDirector'
        ]);

        // Appel de l'API pour mettre à jour le film
        $response = Http::put("http://localhost:8080/toad/film/update/{$filmId}", $data);

        // Vérification de la réponse de l'API
        if ($response->failed()) {
            // Log de la réponse pour le débogage
            \Log::error('Erreur lors de la mise à jour du film: ' . $response->body());
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour du film.');
        }

        // Alerte JavaScript pour indiquer le succès de la mise à jour
        echo "<script>alert('Film mis à jour avec succès.');</script>";

        return redirect()->route('films.show', $filmId);
    }

    /**
     * Supprimer un film via un formulaire de suppression
     */
    public function destroy($filmId)
    {
        $response = Http::delete("http://localhost:8080/toad/film/delete/{$filmId}");

        if ($response->failed()) {
            return redirect()->route('filmlist')->with('error', 'Erreur lors de la suppression du film.');
        }

        return redirect()->route('filmlist')->with('success', 'Film supprimé avec succès.');
    }

    public function create()
    {
        return view('films.create');
    }

    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'releaseYear' => 'required|integer|min:1900|max:' . date('Y'),
            'languageId' => 'required|integer',
            'rentalRate' => 'required|numeric|min:0',
            'length' => 'required|integer|min:0',
            'rating' => 'required|string|max:5',
        ]);

        // Envoyer les données à l'API
        $response = Http::post("{$this->baseUrl}/add", $validatedData);

        // Vérifier si l'ajout a réussi
        if ($response->successful()) {
            return redirect()->route('filmlist')->with('success', 'Film ajouté avec succès.');
        } else {
            return redirect()->back()->with('error', 'Erreur lors de l\'ajout du film.');
        }
    }
}
