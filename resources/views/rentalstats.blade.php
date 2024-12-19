<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFTG - Statistiques de location</title>
    <link rel="stylesheet" href="./style.css">
    </style>
</head>
<body>
    <div class="container">
        <h1>RFTG - Statistiques de location</h1>
        <div class="app-section">
            <div class="controls" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <button class="button" onclick="window.location.href='/filmlist'" style="white-space: nowrap;">Retour</button>
                
                <form method="GET" action="" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; flex: 1;">
                    <div class="search-container" style="min-width: 200px; flex: 1;">
                        <input type="text" name="search" class="search-bar" placeholder="Rechercher..." value="{{ request('search') }}" style="width: 100%;">
                        @if(request('search'))
                            <span class="clear-search" onclick="window.location.href='?sort={{ request('sort', 'rental_id_asc') }}'">✕</span>
                        @endif
                    </div>

                    <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                        <input type="date" id="start_date" name="start_date" value="{{ request('start_date', date('Y-m-d')) }}" title="Date de début">
                        <span>→</span>
                        <input type="date" id="end_date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}" title="Date de fin">
                        <button type="submit" class="button" style="white-space: nowrap;">Rechercher</button>
                    </div>

                    <select name="sort" onchange="this.form.submit()" class="sort-select" style="min-width: 150px;">
                        <option value="rental_id_asc" {{ request('sort') == 'rental_id_asc' ? 'selected' : '' }}>ID ↑</option>
                        <option value="rental_id_desc" {{ request('sort') == 'rental_id_desc' ? 'selected' : '' }}>ID ↓</option>
                        <option value="rental_date_asc" {{ request('sort') == 'rental_date_asc' ? 'selected' : '' }}>Location ↑</option>
                        <option value="rental_date_desc" {{ request('sort') == 'rental_date_desc' ? 'selected' : '' }}>Location ↓</option>
                        <option value="return_date_asc" {{ request('sort') == 'return_date_asc' ? 'selected' : '' }}>Retour ↑</option>
                        <option value="return_date_desc" {{ request('sort') == 'return_date_desc' ? 'selected' : '' }}>Retour ↓</option>
                    </select>
                </form>
            </div>

            <div class="rental-stats">
                <table>
                    <thead>
                        <tr>
                            <th>Film</th>
                            <th>Client</th>
                            <th>Date de location</th>
                            <th>Date de retour</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $response = file_get_contents('http://localhost:8080/toad/rental/sort');
                            $data = json_decode($response, true);
                            $rentals = $data['locations'] ?? [];

                            if(request('search')) {
                                $search = strtolower(request('search'));
                                $rentals = array_values(array_filter($rentals, function($rental) use ($search) {
                                    return str_contains(strtolower($rental['film_title']), $search) ||
                                           str_contains(strtolower($rental['customer_name']), $search);
                                }));
                            }

                            if(request('start_date') && request('end_date')) {
                                $start_date = strtotime(date('Y-m-d', strtotime(request('start_date'))));
                                $end_date = strtotime(date('Y-m-d', strtotime(request('end_date'))));
                                $rentals = array_values(array_filter($rentals, function($rental) use ($start_date, $end_date) {
                                    $rental_date = strtotime(date('Y-m-d', strtotime($rental['rental_date'])));
                                    $return_date = strtotime(date('Y-m-d', strtotime($rental['return_date'])));
                                    return ($rental_date >= $start_date && $rental_date <= $end_date) &&
                                           $return_date <= $end_date;
                                }));
                            }

                            $sort = request('sort', 'rental_id_asc');
                            [$sort_field, $sort_direction] = explode('_', $sort);
                            
                            usort($rentals, function($a, $b) use ($sort_field, $sort_direction) {
                                $valueA = $sort_field === 'rental_date' || $sort_field === 'return_date' 
                                    ? date('Y-m-d', strtotime($a[$sort_field])) 
                                    : $a[$sort_field] ?? '';
                                $valueB = $sort_field === 'rental_date' || $sort_field === 'return_date'
                                    ? date('Y-m-d', strtotime($b[$sort_field]))
                                    : $b[$sort_field] ?? '';
                                return $sort_direction === 'asc' ? 
                                    strcmp($valueA, $valueB) : 
                                    strcmp($valueB, $valueA);
                            });

                            $showAll = request('showAll', false);
                            $displayData = $showAll ? $rentals : array_slice($rentals, 0, 9);
                        @endphp

                        @forelse($displayData as $rental)
                            <tr>
                                <td>{{ $rental['film_title'] }}</td>
                                <td>{{ $rental['customer_name'] }}</td>
                                <td>{{ date('d/m/Y', strtotime($rental['rental_date'])) }}</td>
                                <td>{{ date('d/m/Y', strtotime($rental['return_date'])) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: red;">Aucune location trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if(count($rentals) > 9)
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
