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
    <style>
        :root {
            --background-color: #f0f4f8;
            --text-color: #2d3748;
            --app-background: #ffffff;
            --app-shadow: rgba(0,0,0,0.1);
            --input-border: #cbd5e0;
            --button-bg: #4299e1;
            --button-text: white;
            --table-border: #e2e8f0;
            --table-header-bg: #4299e1;
            --table-header-text: white;
            --table-even-row: #edf2f7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.8;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h1, h2, h3 {
            color: var(--text-color);
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        h1 { font-size: clamp(2em, 5vw, 3em); }
        h2 { font-size: clamp(1.5em, 4vw, 2.5em); }
        h3 { font-size: clamp(1.2em, 3vw, 2em); }

        .app-section {
            background-color: var(--app-background);
            border-radius: 20px;
            padding: clamp(20px, 5vw, 50px);
            margin-bottom: 60px;
            box-shadow: 0 15px 35px var(--app-shadow);
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
        }

        .film-card:hover {
            transform: translateY(-5px);
        }

        .film-title {
            color: var(--button-bg);
            font-weight: bold;
            margin-bottom: 10px;
        }

        .film-details {
            color: var(--text-color);
            margin-bottom: 10px;
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

        .filter-button {
            background-color: var(--button-bg);
            color: var(--button-text);
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        .bottom-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .film-label {
            font-weight: 600;
            color: #4299e1;
            margin-right: 5px;
            font-size: 0.9em;
        }

        @media (prefers-reduced-motion: reduce) {
            .film-card {
                transition: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>RFTG - Liste des films</h1>
        <div class="app-section">
            <h2 class="app-title">Voici la liste des films disponibles</h2>

            <div class="grid">
                @php
                    $currentPage = request()->get('page', 1);
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => 'http://localhost:8080/toad/film/page?page=' . $currentPage,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CONNECTTIMEOUT => 3,
                        CURLOPT_TIMEOUT => 5
                    ]);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    
                    $data = json_decode($response, false);
                    $films = $data->films ?? [];
                    $totalPages = $data->totalPages ?? 0;
                    $totalFilms = $data->totalFilms ?? 0;
                @endphp
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
                        Aucun film disponible
                    </div>
                @endforelse
            </div>
            @if($totalPages > 0)
                <div class="bottom-controls">
                    <div class="pagination">
                        @if($currentPage > 1)
                            <a href="?page=1">&lt;&lt;</a>
                            <a href="?page={{ $currentPage - 1 }}">&lt;</a>
                        @endif

                        @for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                            <a href="?page={{ $i }}" class="{{ $i == $currentPage ? 'active' : '' }}">{{ $i }}</a>
                        @endfor

                        @if($currentPage < $totalPages)
                            <a href="?page={{ $currentPage + 1 }}">&gt;</a>
                            <a href="?page={{ $totalPages }}">&gt;&gt;</a>
                        @endif
                    </div>
                    <button class="filter-button" onclick="window.location.href='/rentalstats'">Afficher les détails de locations</button>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
