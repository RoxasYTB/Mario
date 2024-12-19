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
    </style>
</head>
<body>
    <div class="container">
        <h1>RFTG - Liste des films</h1>
        <div class="app-section">
            <h2 class="app-title">Voici la liste des films disponibles</h2>

            <div class="controls" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <form method="GET" action="" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; flex: 1;" onsubmit="return validateDates()">
                    <div class="search-container" style="min-width: 200px; flex: 1;">
                        <input type="text" name="search" class="search-bar" placeholder="Rechercher un titre..." value="{{ request('search') }}" style="width: 100%;">
                        @if(request('search'))
                            <span class="clear-search" onclick="window.location.href='?sort={{ request('sort', 'title_asc') }}&start_year={{ request('start_year', '2006') }}&end_year={{ request('end_year', date('Y')) }}'">✕</span>
                        @endif
                    </div>

                    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                        <input type="number" id="start_year" name="start_year" min="1900" max="{{ date('Y') }}" value="{{ request('start_year', '2006') }}" title="Année de début" placeholder="Année début">
                        <span>→</span>
                        <input type="number" id="end_year" name="end_year" min="1900" max="{{ date('Y') }}" value="{{ request('end_year', date('Y')) }}" title="Année de fin" placeholder="Année fin">
                        <button type="submit" class="button" style="white-space: nowrap;">Rechercher</button>
                    </div>

                    <select name="sort" onchange="this.form.submit()" class="sort-select" style="min-width: 150px;">
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Titre (A-Z)</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Titre (Z-A)</option>
                        <option value="year_asc" {{ request('sort') == 'year_asc' ? 'selected' : '' }}>Année (Croissant)</option>
                        <option value="year_desc" {{ request('sort') == 'year_desc' ? 'selected' : '' }}>Année (Décroissant)</option>
                        <option value="duration_asc" {{ request('sort') == 'duration_asc' ? 'selected' : '' }}>Durée (Croissant)</option>
                        <option value="duration_desc" {{ request('sort') == 'duration_desc' ? 'selected' : '' }}>Durée (Décroissant)</option>
                    </select>
                </form>
            </div>

            <div class="grid">
                @php
                    $currentPage = request()->get('page', 1);
                    $sort = request('sort', 'title_asc');
                    $search = request('search', '');
                    $start_year = request('start_year', '2006');
                    $end_year = request('end_year', date('Y'));
                    
                    if($start_year > $end_year) {
                        echo "<div class='alert alert-danger'>L'année de début ne peut pas être supérieure à l'année de fin</div>";
                    } else {
                        $ch = curl_init();
                        curl_setopt_array($ch, [
                            CURLOPT_URL => 'http://localhost:8080/toad/film/page?page=' . $currentPage . 
                                         '&sort=' . $sort . 
                                         '&search=' . urlencode($search) .
                                         '&start_year=' . urlencode($start_year) .
                                         '&end_year=' . urlencode($end_year),
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_CONNECTTIMEOUT => 3,
                            CURLOPT_TIMEOUT => 5
                        ]);
                        $response = curl_exec($ch);
                        curl_close($ch);
                        
                        $data = json_decode($response, false);
                        $films = $data->films ?? [];
                        
                        // Filtrer les films si une recherche est effectuée
                        if (!empty($search)) {
                            $films = array_filter($films, function($film) use ($search) {
                                return stripos($film->title, $search) !== false;
                            });
                        }
                        
                        $totalPages = $data->totalPages ?? 0;
                        $totalFilms = $data->totalFilms ?? 0;
                    }
                @endphp

                <script>
                function validateDates() {
                    var startYear = document.getElementById('start_year').value;
                    var endYear = document.getElementById('end_year').value;
                    
                    if(parseInt(startYear) > parseInt(endYear)) {
                        alert("L'année de début ne peut pas être supérieure à l'année de fin");
                        return false;
                    }
                    return true;
                }
                </script>

                @forelse ($films as $film)
                    <div class="film-card" onclick="window.location='{{ url('/filmdetail?id=' . $film->filmId) }}'">
                        <h3 class="film-title">{{ $film->title }}</h3>
                        <p class="film-details line-clamp-3">{{ $film->description }}</p>
                        <div class="film-details">
                            <span class="film-label">Année:</span> {{ $film->releaseYear }}<br>
                            <span class="film-label">Durée location:</span> {{ $film->rentalDuration }} jours<br>
                            <span class="film-label">Tarif:</span> {{ $film->rentalRate }}€<br>
                            <span class="film-label">Évaluation:</span> {{ $film->rating }}<br>
                            <span class="film-label">Durée:</span> {{ $film->length }} minutes<br>
                            <span class="film-label">Coût de remplacement:</span> {{ $film->replacementCost }}€<br>
                            @if(!empty($film->specialFeatures))
                                <span class="film-label">Bonus:</span> {{ $film->specialFeatures }}
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center text-gray-500 dark:text-gray-400 py-8">
                        Aucun film disponible pour ces critères de recherche
                    </div>
                @endforelse
            </div>
            @if($totalPages > 0)
                <div class="bottom-controls">
                    <div class="pagination">
                        @if($currentPage > 1)
                            <a href="?page=1&sort={{ $sort }}&search={{ $search }}&start_year={{ $start_year }}&end_year={{ $end_year }}">&lt;&lt;</a>
                            <a href="?page={{ $currentPage - 1 }}&sort={{ $sort }}&search={{ $search }}&start_year={{ $start_year }}&end_year={{ $end_year }}">&lt;</a>
                        @endif

                        @for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                            <a href="?page={{ $i }}&sort={{ $sort }}&search={{ $search }}&start_year={{ $start_year }}&end_year={{ $end_year }}" class="{{ $i == $currentPage ? 'active' : '' }}">{{ $i }}</a>
                        @endfor

                        @if($currentPage < $totalPages)
                            <a href="?page={{ $currentPage + 1 }}&sort={{ $sort }}&search={{ $search }}&start_year={{ $start_year }}&end_year={{ $end_year }}">&gt;</a>
                            <a href="?page={{ $totalPages }}&sort={{ $sort }}&search={{ $search }}&start_year={{ $start_year }}&end_year={{ $end_year }}">&gt;&gt;</a>
                        @endif
                    </div>
                    <button class="filter-button button" onclick="window.location.href='/rentalstats'">Afficher les détails de locations</button>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
