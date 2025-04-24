<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function getFilms()
    {
        $port = env('PORT');
        $serveur = env('SERVEUR');
        $apiUrl = "http://".$serveur.$port."/toad/film/all";
        
        try {
            $response = file_get_contents($apiUrl);
        
            if ($response === false) {
                throw new Exception("Erreur lors de l'appel de l'API.");
            }
        
            $films = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Erreur de décodage JSON : " . json_last_error_msg());
            }

            // Transmet les données à la vue 'films'
            return view('films.index', compact('films'));
        
        } catch (Exception $e) {
            // En cas d'erreur, retournez un message d'erreur à la vue
            return view('films.index', ['error' => $e->getMessage()]);
        }
    }

    public function getStock()
    {
        $port = env('PORT');
        $serveur = env('SERVEUR');
    
        $apiUrlStock = "http://".$serveur.$port."/toad/inventory/stockFilm";
        $apiUrlStore = "http://".$serveur.$port."/toad/inventory/getStockByStore";
    
        try {
            // Appels API
            $responseStock = file_get_contents($apiUrlStock);
            $responseStore = file_get_contents($apiUrlStore);
    
            if ($responseStock === false || $responseStore === false) {
                throw new Exception("Erreur lors de l'appel de l'API.");
            }
    
            // Décodage JSON
            $filmsStock = json_decode($responseStock, true);
            $filmsStore = json_decode($responseStore, true);
    
            if (!is_array($filmsStock) || empty($filmsStock) || !is_array($filmsStore) || empty($filmsStore)) {
                throw new Exception("Aucun film disponible.");
            }
    
            // Création d'un tableau fusionné
            $films = [];
    
            foreach ($filmsStock as $film) {
                $titre = trim(strtolower($film['title'] ?? ''));
                $films[$titre] = [
                    'title' => $film['title'] ?? 'Titre inconnu',
                    'filmsDisponibles' => $film['filmsDisponibles'] ?? 'N/A',
                    'address' => 'N/A',
                ];
            }
    
            foreach ($filmsStore as $store) {
                $titre = trim(strtolower($store['title'] ?? ''));
    
                if (isset($films[$titre])) {
                    $films[$titre]['address'] = $store['address'] ?? 'Adresse inconnue';
                }
            }
    
            return view('stocks', ['films' => array_values($films)]);
    
        } catch (Exception $e) {
            return view('stocks', ['error' => $e->getMessage()]);
        }
    }

    public function getFilmDetail($id)
    {
        $port = env('PORT');
        $serveur = env('SERVEUR');

        $apiUrl = 'http://'.$serveur.$port.'/toad/film/getById?id=' . $id;
    
        try {
            $response = file_get_contents($apiUrl);
    
            if ($response === false) {
                throw new Exception("Erreur lors de l'appel de l'API pour le film.");
            }
    
            $film = json_decode($response, true);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Erreur de décodage JSON : " . json_last_error_msg());
            }
    
            return view('films.detail', compact('film'));
    
        } catch (Exception $e) {
            return view('films.detail', ['error' => $e->getMessage()]);
        }
    }
    public function edit(Request $request, $id) {

        $port = env('PORT');
        $serveur = env('SERVEUR');
        $apiUrl = 'http://'.$serveur.$port.'/toad/film/getById?id=' . $id;
        try {
            $response = file_get_contents($apiUrl);
            if ($response === false) {
                throw new Exception("Erreur lors de l'appel de l'API pour le film.");
            }
            $film = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Erreur de décodage JSON : " . json_last_error_msg());
            }
            return view('films.edit', compact('film')); 
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage()); 
        }
    }

    public function update(Request $request, $id) {
        $port = env('PORT');
        $serveur = env('SERVEUR');
        $apiUrl = 'http://'.$serveur.$port.'/toad/film/update/' . $id;
    
        try {
            $client = new \GuzzleHttp\Client();
            
            $queryParams = [
                'query' => [
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'releaseYear' => $request->input('releaseYear'),
                    'languageId' => $film['languageId'] ?? 1,
                    'originalLanguageId' => $film['originalLanguageId'] ?? 1,
                    'rentalDuration' => $request->input('rentalDuration'),
                    'rentalRate' => $request->input('rentalRate'),
                    'length' => $film['length'] ?? 0,
                    'replacementCost' => $film['replacementCost'] ?? 0.0,
                    'rating' => $film['rating'] ?? 'G',
                    'lastUpdate' => date('Y-m-d H:i:s'),
                    'idDirector' => $film['idDirector'] ?? 1
                ]
            ];
    
            $response = $client->put($apiUrl, $queryParams);
    
            if ($response->getStatusCode() === 200) {
                return redirect()->route('films.index')->with('success', 'Film mis à jour avec succès.');
            } else {
                return redirect()->route('films.index')->with('error', 'Erreur lors de la mise à jour du film.');
            }
        } catch (\Exception $e) {
            Log::error('Update error:', ['message' => $e->getMessage()]);
            return redirect()->route('films.index')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    public function create()
{
    return view('films.create');
}

public function store(Request $request)
{
    // Validation des données avant l'envoi
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'releaseYear' => 'required|integer',
        'languageId' => 'required|integer',
        'originalLanguageId' => 'nullable|integer',
        'rentalDuration' => 'required|integer',
        'rentalRate' => 'required|numeric',
        'length' => 'required|integer',
        'replacementCost' => 'required|numeric',
        'rating' => 'required|string',
        'idDirector' => 'required|integer',
    ]);

    $port = env('PORT');
    $serveur = env('SERVEUR');
    $apiUrl = 'http://'.$serveur.$port.'/toad/film/add';
    $client = new \GuzzleHttp\Client();

    try {
        $response = $client->post($apiUrl, [
            'form_params' => [ // On garde form_params pour coller avec les @RequestParam de l'API
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'releaseYear' => $validatedData['releaseYear'],
                'languageId' => (int) $validatedData['languageId'], // Conversion pour respecter le type attendu
                'originalLanguageId' => isset($validatedData['originalLanguageId']) ? (int) $validatedData['originalLanguageId'] : null,
                'rentalDuration' => (int) $validatedData['rentalDuration'],
                'rentalRate' => (float) $validatedData['rentalRate'],
                'length' => (int) $validatedData['length'],
                'replacementCost' => (float) $validatedData['replacementCost'],
                'rating' => $validatedData['rating'],
                'lastUpdate' => now()->format('Y-m-d H:i:s'), // Formatage correct du timestamp
                // 'idDirector' => (int) $validatedData['idDirector'], // Retiré car non utilisé par l'API
            ]
        ]);
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        
        Log::info("Réponse API (Status: $statusCode): $body");

        if ($response->getStatusCode() === 200) {
            return redirect()->route('films.index')->with('success', 'Film ajouté avec succès.');
        } else {
            return redirect()->route('films.create')->with('error', 'Erreur lors de l\'ajout du film.');
        }
    } catch (\Exception $e) {
        return redirect()->route('films.create')->with('error', 'Erreur: ' . $e->getMessage());
    }
}



    public function getFilmUpdate($id)
    {
        $port = env('PORT');
        $serveur = env('SERVEUR');
        $apiUrl = 'http://'.$serveur.$port.'/toad/film/getById?id=' . $id;
    
        try {
            $response = file_get_contents($apiUrl);
    
            if ($response === false) {
                throw new Exception("Erreur lors de l'appel de l'API pour le film.");
            }
    
            $film = json_decode($response, true);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Erreur de décodage JSON : " . json_last_error_msg());
            }
    
            return view('films.detail', compact('film'));
    
        } catch (Exception $e) {
            return view('films.detail', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $port = env('PORT');
        $serveur = env('SERVEUR');
        $deleteUrl = 'http://'.$serveur.$port.'/toad/film/delete/' . $id;
    
        try {
            // Suppression du film et de ses données associées (suppression en cascade)
            $client = new \GuzzleHttp\Client();
            $response = $client->delete($deleteUrl);
    
            if ($response->getStatusCode() === 200) {
                // Si l'API répond par un succès, renvoyer un message de succès
                return redirect()->route('films.index')->with('success', 'Film et ses données associées supprimés avec succès.');
            } else {
                // Si l'API répond par une erreur, afficher un message d'erreur
                return redirect()->route('films.index')->with('error', 'Erreur lors de la suppression du film.');
            }
        } catch (\Exception $e) {
            // En cas d'exception, retourner un message d'erreur
            return redirect()->route('films.index')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
    
    
    

    public function getRentalLivraison($id)
    {
        $port = env('PORT');
        $serveur = env('SERVEUR');
        $apiUrl = 'http://'.$serveur.$port.'/toad/rental/films-en-cours';
    
        try {
            $response = file_get_contents($apiUrl);
    
            if ($response === false) {
                throw new Exception("Erreur lors de l'appel de l'API pour le film.");
            }
    
            $film = json_decode($response, true);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Erreur de décodage JSON : " . json_last_error_msg());
            }
    
            return view('livraison', compact('film'));
    
        } catch (Exception $e) {
            return view('livraison', ['error' => $e->getMessage()]);
        }
    }

    public function getStockDetail($id)
{
    $port = env('PORT');
    $serveur = env('SERVEUR');
    $apiUrl = 'http://'.$serveur.$port.'/toad/inventory/getById?id=' . $id; // L'URL de l'API pour un stock spécifique
    
    try {
        // Récupérer la réponse JSON du webservice
        $response = file_get_contents($apiUrl);

        // Vérifier si la réponse est valide
        if ($response === false) {
            throw new Exception("Erreur lors de l'appel de l'API pour le stock.");
        }

        // Décoder la réponse JSON en tableau associatif PHP
        $stock = json_decode($response, true);

        // Vérifier si le JSON est bien décodé
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Erreur de décodage JSON : " . json_last_error_msg());
        }

        // Transmet les données à la vue 'stock_detail'
        return view('stock_detail', compact('stock'));
    
    } catch (Exception $e) {
        return view('stock', ['error' => $e->getMessage()]);
    }
}

public function getStockUpdate($id)
{
    $port = env('PORT');
    $serveur = env('SERVEUR');
    $apiUrl = 'http://'.$serveur.$port.'/toad/inventory/getById?id=' . $id; // L'URL de l'API pour un stock spécifique
    
    try {
        // Récupérer la réponse JSON du webservice
        $response = file_get_contents($apiUrl);

        // Vérifier si la réponse est valide
        if ($response === false) {
            throw new Exception("Erreur lors de l'appel de l'API pour le stock.");
        }

        // Décoder la réponse JSON en tableau associatif PHP
        $stock = json_decode($response, true);

        // Vérifier si le JSON est bien décodé
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Erreur de décodage JSON : " . json_last_error_msg());
        }

        // Transmet les données à la vue 'stock_update' pour mise à jour
        return view('stock_update', compact('stock'));
    
    } catch (Exception $e) {
        // En cas d'erreur, retournez un message d'erreur à la vue
        return view('stock_update', ['error' => $e->getMessage()]);
    }
}

public function getAllStocks()
{
    $port = env('PORT');
    $serveur = env('SERVEUR');
    $apiUrl = 'http://'.$serveur.$port.'/toad/inventory/all'; // L'URL de l'API pour récupérer les stocks
    
    try {
        // Récupérer la réponse JSON du webservice
        $response = file_get_contents($apiUrl);

        // Vérifier si la réponse est valide
        if ($response === false) {
            throw new Exception("Erreur lors de l'appel de l'API pour les stocks.");
        }

        // Décoder la réponse JSON en tableau associatif PHP
        $stocks = json_decode($response, true);

        // Vérifier si le JSON est bien décodé
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Erreur de décodage JSON : " . json_last_error_msg());
        }

        // Transmet les données à la vue 'stocks'
        return view('stocks', compact('stocks'));
    
    } catch (Exception $e) {
        // En cas d'erreur, retournez un message d'erreur à la vue
        return view('stocks', ['error' => $e->getMessage()]);
    }
}
public function getStockDelete($id)
{
    $port = env('PORT');
    $serveur = env('SERVEUR');
    $apiUrl = 'http://'.$serveur.$port.'/toad/inventory/delete/' . $id; // L'URL de l'API pour supprimer un stock spécifique
    
    try {
        // Récupérer la réponse JSON du webservice (ici on s'attend à un status de réussite)
        $response = file_get_contents($apiUrl);

        // Vérifier si la réponse est valide
        if ($response === false) {
            throw new Exception("Erreur lors de l'appel de l'API pour supprimer le stock.");
        }

        // Vérifier si le JSON est bien décodé
        $status = json_decode($response, true);

        // Si une erreur est renvoyée, lever une exception
        if ($status['error']) {
            throw new Exception($status['error']);
        }

        // Transmet la réussite de la suppression à la vue
        return redirect()->route('stocks.index')->with('success', 'Stock supprimé avec succès');
    
    } catch (Exception $e) {
        // En cas d'erreur, retournez un message d'erreur à la vue
        return redirect()->route('stocks.index')->with('error', $e->getMessage());
    }
}

public function login(Request $request)
{
    $client = new \GuzzleHttp\Client();

    $request->validate([
        'email' => 'required|string',
        'password' => 'required|string',
    ]);

    $port = env('PORT');
    $serveur = env('SERVEUR');
    $email = $request->input('email');
    $password = $request->input('password');

    $apiUrl = "http://".$serveur.$port."/toad/staff/getByEmail?email=" . urlencode($email);

    try {
        // Utilisation de Guzzle pour faire la requête GET
        $response = $client->request('GET', $apiUrl);

        // On décode la réponse JSON
        $staff = json_decode($response->getBody()->getContents(), true);

        if (!$staff) {
            return back()->with('error', 'Utilisateur non trouvé.');
        }

        // Vérification du mot de passe
        if ($staff['pasword'] !== $password) { 
            return back()->with('error', 'Mot de passe incorrect.');
        }

        // Stockage des informations de l'utilisateur en session
        session([
            'staff_id' => $staff['staffId'],
            'first_name' => $staff['firstName'],
            'last_name' => $staff['lastName'],
            'email' => $staff['email'],
            'store_id' => $staff['storeId'],
            'role_id' => $staff['roleId'],
            'is_logged_in' => true,
        ]);

        return redirect()->route('films.index')->with('success', 'Connexion réussie.');

    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}
 


}
