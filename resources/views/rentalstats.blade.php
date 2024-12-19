<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFTG - Statistiques de location</title>
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
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.8;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 1.25rem;
        }

        h1 {
            font-size: 3em;
            color: var(--text-color);
            text-align: center;
            margin-bottom: 1.875rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .app-section {
            background-color: var(--app-background);
            border-radius: 1.25rem;
            padding: 3.125rem;
            margin-bottom: 3.75rem;
            box-shadow: 0 0.9375rem 2.1875rem var(--app-shadow);
        }

        .button {
            background-color: var(--button-bg);
            color: var(--button-text);
            padding: 0.625rem 1.25rem;
            border: none;
            border-radius: 0.3125rem;
            cursor: pointer;
            margin: 0.3125rem;
            will-change: opacity;
            transition: opacity 0.2s;
        }

        .button:hover {
            opacity: 0.9;
        }

        .controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            gap: 1rem;
        }

        .search-bar {
            padding: 0.625rem;
            border: 1px solid var(--input-border);
            border-radius: 0.3125rem;
            width: 36.875rem;
            margin-right: 0.625rem;
        }

        .sort-select {
            padding: 0.625rem;
            border: 1px solid var(--input-border);
            border-radius: 0.3125rem;
            background-color: var(--button-bg);
            color: var(--button-text);
            cursor: pointer;
        }

        .rental-stats table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.25rem;
        }

        .rental-stats th, 
        .rental-stats td {
            border: 1px solid var(--table-border);
            padding: 0.75rem;
            text-align: left;
        }

        .rental-stats th {
            background-color: var(--table-header-bg);
            color: var(--table-header-text);
            position: relative;
        }

        .rental-stats th::after {
            content: '↕';
            position: absolute;
            right: 0.5rem;
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
            background-color: var(--table-even-row);
        }

        .search-container {
            position: relative;
            display: inline-block;
        }

        .clear-search {
            position: absolute;
            right: 0.9375rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-color);
            font-weight: bold;
            padding: 0.3125rem;
            will-change: opacity;
            transition: opacity 0.2s;
        }

        .clear-search:hover {
            opacity: 0.7;
        }

        a {
            text-decoration: none;
            color: inherit;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>RFTG - Statistiques de location</h1>
        <div class="app-section">
            <div class="controls">
                <button class="button" onclick="window.location.href='/filmlist'">Retour à la liste</button>
                <form method="GET" action="" style="display: inline;">
                    <div class="search-container">
                        <input type="text" name="search" class="search-bar" placeholder="Rechercher un film..." value="{{ request('search') }}">
                        @if(request('search'))
                            <span class="clear-search" onclick="window.location.href='?sort={{ request('sort', 'title_asc') }}'">✕</span>
                        @endif
                    </div>
                    <select name="sort" onchange="this.form.submit()" class="sort-select">
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Titre (A-Z)</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Titre (Z-A)</option>
                        <option value="rentals_asc" {{ request('sort') == 'rentals_asc' ? 'selected' : '' }}>Locations (Croissant)</option>
                        <option value="rentals_desc" {{ request('sort') == 'rentals_desc' ? 'selected' : '' }}>Locations (Décroissant)</option>
                        <option value="available_asc" {{ request('sort') == 'available_asc' ? 'selected' : '' }}>Copies disponibles (Croissant)</option>
                        <option value="available_desc" {{ request('sort') == 'available_desc' ? 'selected' : '' }}>Copies disponibles (Décroissant)</option>
                        <option value="total_asc" {{ request('sort') == 'total_asc' ? 'selected' : '' }}>Total copies (Croissant)</option>
                        <option value="total_desc" {{ request('sort') == 'total_desc' ? 'selected' : '' }}>Total copies (Décroissant)</option>
                    </select>
                </form>
            </div>

            <div class="rental-stats">
                <table>
                    <thead>
                        <tr>
                            <th>Titre du film</th>
                            <th>Copies en location</th>
                            <th>Copies disponibles</th>
                            <th>Total des copies</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $response = file_get_contents('http://localhost:8080/toad/rental/sort');
                            $statsData = json_decode($response)->statistiques;
                            
                            $statsData = array_values(array_filter($statsData, fn($stat) => $stat->filmTitle !== "Film inconnu" && $stat->totalCopies > 0));

                            if(request('search')) {
                                $search = strtolower(request('search'));
                                $statsData = array_values(array_filter($statsData, fn($stat) => str_contains(strtolower($stat->filmTitle), $search)));
                            }

                            $sort = request('sort', 'title_asc');
                            [$sort_field, $sort_direction] = explode('_', $sort);
                            
                            usort($statsData, function($a, $b) use ($sort_field, $sort_direction) {
                                $valueA = match($sort_field) {
                                    'title' => strtolower($a->filmTitle),
                                    'rentals' => $a->copiesEnLocation,
                                    'available' => $a->copiesDisponibles,
                                    'total' => $a->totalCopies,
                                };
                                $valueB = match($sort_field) {
                                    'title' => strtolower($b->filmTitle),
                                    'rentals' => $b->copiesEnLocation,
                                    'available' => $b->copiesDisponibles,
                                    'total' => $b->totalCopies,
                                };
                                return $sort_direction === 'asc' ? $valueA <=> $valueB : $valueB <=> $valueA;
                            });

                            $showAll = request('showAll', false);
                            $displayData = $showAll ? $statsData : array_slice($statsData, 0, 9);
                        @endphp

                        @forelse($displayData as $stat)
                            <tr>
                                <td>{{ $stat->filmTitle }}</td>
                                <td>{{ $stat->copiesEnLocation }}</td>
                                <td>{{ $stat->copiesDisponibles }}</td>
                                <td>{{ $stat->totalCopies }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: red;">Aucun résultat correspondant à votre recherche.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if(count($statsData) > 9)
                    <div style="text-align: center; margin-top: 20px;">
                        @if($showAll)
                            <a href="?{{ http_build_query(array_merge(request()->all(), ['showAll' => false])) }}" class="button">Afficher moins</a>
                        @else
                            <a href="?{{ http_build_query(array_merge(request()->all(), ['showAll' => true])) }}" class="button">Afficher plus</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
