<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFTG - Liste des films</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    </noscript>
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <div class="container">
        <h1>RFTG - Liste des films</h1>
        <div class="app-section">
            <h2 class="app-title">Voici la liste des films disponibles</h2>

            <div class="grid">
                @php
                try {
                $ch = curl_init();
                $url = 'http://localhost:8080/toad/film/all';

                curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 3,
                CURLOPT_TIMEOUT => 5
                ]);

                $response = curl_exec($ch);

                if($response === false) {
                throw new Exception('Erreur curl: ' . curl_error($ch));
                }

                curl_close($ch);

                $films = json_decode($response);
                if(json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Erreur décodage JSON: ' . json_last_error_msg());
                }
                } catch(Exception $e) {
                echo "<div class='alert alert-danger'>Erreur lors de la récupération des films: " . $e->getMessage() . "</div>";
                $films = [];
                }
                @endphp

                @forelse ($films as $film)
                <div class="film-card">
                    <h3 class="film-title">{{ $film->title }}</h3>
                    <p class="film-details line-clamp-3">{{ $film->description }}</p>
                    <div class="film-details" style="display: flex; flex-direction: column; align-items: left;">
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <span class="film-label">Année:</span> {{ $film->releaseYear }}
                        </div>
                        <div style="display: flex; align-items: center; gap: 5px;"></div>
                        <span class="film-label">Durée location:</span> {{ $film->rentalDuration }} jours
                    </div>
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <span class="film-label">Tarif:</span> {{ $film->rentalRate }}€
                    </div>
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <span class="film-label">Évaluation:</span> {{ $film->rating }}
                    </div>
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <span class="film-label">Durée:</span> {{ $film->length }} minutes
                    </div>
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <span class="film-label">Coût de remplacement:</span> {{ $film->replacementCost }}€
                    </div>
                    @if(!empty($film->specialFeatures))
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <span class="film-label">Bonus:</span> {{ $film->specialFeatures }}
                    </div>
                    @endif

                    <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px;"></div>

                    <a href="{{ url('/filmdetail?id=' . $film->filmId) . "&action=edit" }}" class="button" style="text-align: center; display: flex; align-items: center; justify-content: center;">Modifier</a>
                    <form action="http://localhost:8000/films/{{ $film->filmId }}" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" autocomplete="off">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="button" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce film ?')" style="font-size: 17px;width: 290px;height: 49px;">Supprimer</button>
                    </form>
                </div>
                @empty
                <div class="col-span-3 text-center text-gray-500 dark:text-gray-400 py-8">
                    Aucun film disponible pour ces critères de recherche
                </div>
                @endforelse
            </div> <!-- Move this closing div here -->
        </div>
    </div>

    <script>
        function deleteFilm(filmId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce film ?')) {
                fetch(`http://localhost:8080/toad/film/delete/${filmId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => {
                        if (response.ok) {
                            alert('Film supprimé avec succès');
                            location.reload(); // Recharger la page pour mettre à jour la liste
                        } else {
                            alert('Erreur lors de la suppression du film');
                        }
                    })
                    .catch(error => {
                        alert('Erreur: ' + error.message);
                    });
            }
        }
    </script>
</body>

</html>