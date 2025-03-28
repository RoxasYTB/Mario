<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 text-center">
            {{ __('Liste des DVDs') }}
        </h2>
    </x-slot>

    <div class="py-8">
            <div style="display: flex; flex-direction: column; width: 100%;">
                <div style="display: flex; flex-direction: row; width: 100%;">
                    <!-- Barre de recherche -->
                    <div class="search-container" style="flex: 80%;margin-left:20px;">
                        <input type="search" id="searchQuery" class="search-input" placeholder="Rechercher un film..." style="width: 100%;">
                        <button style="display:none;" class="search-button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="search-icon">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                    </div>
                    <!-- Bouton d'ajout -->
                    <div style="flex: 20%; display: flex; align-items: center; justify-content: center;">
                        <a href="{{ route('films.create') }}" class="add-button" style="height: 40px;">
                            ➕ Ajouter un film
                        </a>
                    </div>
                </div>
            </div>

    </div>

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

                    <a href="{{ url('/films/edit?id=' . $film->filmId) . "&action=edit" }}" class="button" style="text-align: center; display: flex; align-items: center; justify-content: center;">Modifier</a>
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

    <!-- Styles améliorés -->
    <style>
        /* Grille des films */
        .film-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            padding: 0;
            list-style: none;
        }

        /* Carte de film */
        .film-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            transition: transform 0.2s;
        }

        .film-card:hover {
            transform: translateY(-5px);
        }

        .film-number {
            width: 2.5rem;
            height: 2.5rem;
            background: black;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .film-title {
            margin-bottom: 1rem;
        }

        .film-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: bold;
        }

        .film-link:hover {
            text-decoration: underline;
        }

        /* Menu contextuel */
        .menu-container {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .menu-button {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 120px;
            z-index: 50;
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-item {
            display: block;
            padding: 8px 12px;
            color: #374151;
            text-decoration: none;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background: #f3f4f6;
        }

        /* Barre de recherche */
        .search-container {
            display: flex;
            align-items: center;
            width: 300px;
            margin: 10px auto;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 25px;
            background: #fff;
        }

        .search-input {
            flex: 1;
            border: none;
            padding: 12px 16px;
            outline: none;
        }

        .search-button {
            padding: 12px;
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
        }

        .search-icon {
            width: 20px;
            height: 20px;
        }

        /* Bouton Ajouter */
        .add-button {
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 500;
            color: #fff;
            background:rgb(37, 129, 235);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .add-button:hover {
            background:rgb(78, 90, 194);
        }
    </style>
     <style>
        :root {
            --background-color: #f0f4f8;
            --text-color: #2d3748;
            --app-background: #fff;
            --app-shadow: rgba(0, 0, 0, .1);
            --input-border: #cbd5e0;
            --button-bg: #4299e1;
            --button-text: #fff;
            --table-border: #e2e8f0;
            --table-header-bg: #4299e1;
            --table-header-text: #fff;
            --table-even-row: #edf2f7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', system-ui, -apple-system, sans-serif;
            background: var(--background-color);
            color: var(--text-color);
            line-height: 1.8;
        }

        .container {
            max-width: 1399px;
            margin: 0 auto;
            padding: 2.5rem 1.25rem;
        }

        h1,
        h2,
        h3 {
            color: var(--text-color);
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        h1 {
            font-size: clamp(2em, 5vw, 3em);
        }

        h2 {
            font-size: clamp(1.5em, 4vw, 2.5em);
        }

        h3 {
            font-size: clamp(1.2em, 3vw, 2em);
        }

        .app-section {
            background: var(--app-background);
            border-radius: 1.25rem;
            padding: 3.125rem;
            margin-bottom: 3.75rem;
            box-shadow: 0 .9375rem 2.1875rem var(--app-shadow);
            will-change: transform;
        }

        .app-title {
            color: var(--button-bg);
            border-bottom: 4px solid var(--button-bg);
            padding-bottom: 20px;
            margin-bottom: 50px;
            text-align: center;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            contain: content;
        }

        .film-card {
            background-color: var(--app-background);
            border-radius: 12px;
            box-shadow: 0 5px 15px var(--app-shadow);
            padding: 20px;
            cursor: pointer;
            transform: translateZ(0);
            will-change: transform;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .film-card:hover {
            transform: translateY(-5px);
        }

        .film-card .film-title {
            font-size: 1.25rem;
            margin-bottom: 15px;
            text-align: left;
        }

        .film-card .button {
            width: 100%;
            margin: 8px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 42px;
            font-size: 1rem;
        }

        .film-card form {
            width: 100%;
        }

        .film-title {
            color: var(--button-bg);
            font-weight: bold;
            margin-bottom: 10px;
        }

        .film-details {
            color: var(--text-color);
            margin-bottom: 15px;
            font-size: 0.95rem;
        }

        .film-details>div {
            margin-bottom: 5px;
        }

        .film-label {
            font-weight: 600;
            color: var(--button-bg);
            margin-right: 5px;
            font-size: 0.9em;
        }

        .controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            gap: 1rem;
        }

        .search-container {
            position: relative;
            display: inline-block;
        }

        .search-bar,
        .sort-select {
            padding: .625rem;
            border: 1px solid var(--input-border);
            border-radius: .3125rem;
        }

        .search-bar {
            width: 36.875rem;
            margin-right: .625rem;
        }

        .search-bar:focus,
        .sort-select:focus {
            outline: none;
            border-color: var(--button-bg);
        }

        .sort-select {
            background: var(--button-bg);
            color: var(--button-text);
            cursor: pointer;
        }

        .clear-search {
            position: absolute;
            right: .9375rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-color);
            font-weight: bold;
            padding: .3125rem;
            transition: opacity .2s;
        }

        .clear-search:hover {
            opacity: .7;
        }

        .button {
            background: var(--button-bg);
            color: var(--button-text);
            padding: .625rem 1.25rem;
            border: 0;
            border-radius: .3125rem;
            cursor: pointer;
            margin: .3125rem;
            transition: opacity .2s;
        }

        .button:hover {
            opacity: .9;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }

        .pagination a {
            padding: 10px 15px;
            border: 1px solid var(--table-border);
            border-radius: 5px;
            text-decoration: none;
            color: var(--text-color);
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: var(--button-bg);
            color: var(--button-text);
        }

        .pagination-link {
            background-color: var(--app-background);
            transition: all 0.2s ease;
        }

        .pagination-link:hover {
            background-color: var(--button-bg);
            color: var(--button-text);
            border-color: var(--button-bg);
        }

        .pagination-current {
            background-color: var(--button-bg);
            color: var(--button-text);
            border: 1px solid var(--button-bg);
        }

        .bottom-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .rental-stats table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.25rem;
        }

        .rental-stats th,
        .rental-stats td {
            border: 1px solid var(--table-border);
            padding: .75rem;
            text-align: left;
        }

        .rental-stats th {
            background: var(--table-header-bg);
            color: var(--table-header-text);
            position: relative;
        }

        .rental-stats th::after {
            content: '↕';
            position: absolute;
            right: .5rem;
            top: 50%;
            transform: translateY(-50%);
        }

        .rental-stats th.asc::after {
            content: '↑';
        }

        .rental-stats th.desc::after {
            content: '↓';
        }

        .rental-stats tr:nth-child(even) {
            background: var(--table-even-row);
        }

        input[type="number"],
        input[type="date"] {
            padding: .5rem;
            border: 1px solid var(--input-border);
            border-radius: .25rem;
            background: var(--app-background);
            color: var(--text-color);
            font-size: .875rem;
            margin: 0 .5rem;
        }

        input[type="number"]:focus,
        input[type="date"]:focus {
            outline: none;
            border-color: var(--button-bg);
            box-shadow: 0 0 0 2px rgba(66, 153, 225, .2);
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Line clamp utility for truncating text */
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Alert styling */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-danger {
            background-color: #fed7d7;
            color: #c53030;
            border: 1px solid #f56565;
        }

        @media (prefers-reduced-motion: reduce) {
            .film-card {
                transition: none;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .app-section {
                padding: 1.5rem;
            }

            .search-bar {
                width: 100%;
            }

            .controls {
                flex-direction: column;
            }

            input[type="number"],
            input[type="date"] {
                width: 100%;
                margin: .5rem 0;
            }

            .sort-select {
                width: 100%;
            }

            .pagination {
                flex-wrap: wrap;
            }

            .film-details {
                font-size: 0.9rem;
            }
        }
    </style>
</x-app-layout>
