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

                $allFilms = json_decode($response);
                if(json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Erreur décodage JSON: ' . json_last_error_msg());
                }

                // Pagination
                $currentPage = request()->query('page', 1);
                $perPage = 9;
                $totalFilms = count($allFilms);
                $totalPages = ceil($totalFilms / $perPage);

                // Limiter la page courante entre 1 et le nombre total de pages
                $currentPage = max(1, min($currentPage, $totalPages));

                // Calculer les films à afficher pour la page actuelle
                $offset = ($currentPage - 1) * $perPage;
                $films = array_slice($allFilms, $offset, $perPage);

                } catch(Exception $e) {
                echo "<div class='alert alert-danger'>Erreur lors de la récupération des films: " . $e->getMessage() . "</div>";
                $films = [];
                $totalPages = 0;
                $currentPage = 1;
                }
                @endphp

                @forelse ($films as $film)
                <div class="film-card">
                    <h3 class="film-title">{{ $film->title }}</h3>
                    <p class="film-details line-clamp-3">{{ $film->description }}</p>
                    <div class="film-details" style="display: inline; flex-direction: column; align-items: left;">
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <span class="film-label">Année:</span> {{ $film->releaseYear }}
                        </div>
                        <div style="display: flex; align-items: center; gap: 5px;"></div>
                        <span class="film-label">Durée location:</span> {{ $film->rentalDuration }} jours

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
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px;"></div>

                    <a href="{{ url('/filmdetail?id=' . $film->filmId) . "&action=edit" }}" class="button" style="text-align: center; display: flex; align-items: center; justify-content: center;">Modifier</a>
                    <form action="http://localhost:8000/films/{{ $film->filmId }}" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" autocomplete="off">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="button" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce film ?')" style="font-size: 17px;width: 356px;height: 49px;">Supprimer</button>
                    </form>
                </div>
                @empty
                <div class="col-span-3 text-center text-gray-500 dark:text-gray-400 py-8">
                    Aucun film disponible pour ces critères de recherche
                </div>
                @endforelse
            </div>

            <!-- Pagination controls -->
            @if(isset($totalPages) && $totalPages > 1)
            <div class="pagination" style="display: flex; justify-content: center; margin-top: 30px; gap: 10px;">
                @if($currentPage > 1)
                <a href="{{ url()->current() }}?page=1" class="pagination-link" style="padding: 8px 12px; border: 1px solid #ddd; text-decoration: none; border-radius: 4px;">&laquo;</a>
                <a href="{{ url()->current() }}?page={{ $currentPage - 1 }}" class="pagination-link" style="padding: 8px 12px; border: 1px solid #ddd; text-decoration: none; border-radius: 4px;">&lsaquo;</a>
                @endif

                @php
                // Calculer la plage de pages à afficher
                $range = 2;
                $startPage = max(1, $currentPage - $range);
                $endPage = min($totalPages, $currentPage + $range);
                @endphp

                @if($startPage > 1)
                <span style="align-self: center;">...</span>
                @endif

                @for($i = $startPage; $i <= $endPage; $i++)
                    @if($i==$currentPage)
                    <span class="pagination-current" style="padding: 8px 12px; background-color: #4a5568; color: white; font-weight: bold; border-radius: 4px;">{{ $i }}</span>
                    @else
                    <a href="{{ url()->current() }}?page={{ $i }}" class="pagination-link" style="padding: 8px 12px; border: 1px solid #ddd; text-decoration: none; border-radius: 4px;">{{ $i }}</a>
                    @endif
                    @endfor

                    @if($endPage < $totalPages)
                        <span style="align-self: center;">...</span>
                        @endif

                        @if($currentPage < $totalPages)
                            <a href="{{ url()->current() }}?page={{ $currentPage + 1 }}" class="pagination-link" style="padding: 8px 12px; border: 1px solid #ddd; text-decoration: none; border-radius: 4px;">&rsaquo;</a>
                            <a href="{{ url()->current() }}?page={{ $totalPages }}" class="pagination-link" style="padding: 8px 12px; border: 1px solid #ddd; text-decoration: none; border-radius: 4px;">&raquo;</a>
                            @endif
            </div>
            <div style="text-align: center; margin-top: 10px;">
                Page {{ $currentPage }} sur {{ $totalPages }} ({{ $totalFilms }} films au total)
            </div>
            @endif
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