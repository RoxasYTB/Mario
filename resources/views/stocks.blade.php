<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ __('Stock DVD') }}
        </h2>
    </x-slot>
    <br>

    <div class="search-container">
        <input type="search" id="searchQuery" class="search-input" placeholder="Rechercher un film...">
        
    </div>
<br>
    <div class="wrapper">
        @if (isset($error))
            <p style="color: red;">{{ $error }}</p>
        @elseif (isset($films) && count($films) > 0)
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Stock Disponible</th>
                        <th>Magasin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($films as $film)
                        <tr>
                            <td>{{ $film['title'] ?? 'Titre inconnu' }}</td>
                            <td>{{ $film['filmsDisponibles'] ?? 'N/A' }}</td>
                            <td>{{ $film['address'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Aucun film disponible.</p>
        @endif
    </div>
</x-app-layout>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchQuery");
        const rows = document.querySelectorAll(".stock-table tbody tr");

        searchInput.addEventListener("input", function() {
            const searchValue = searchInput.value.toLowerCase();

            rows.forEach(row => {
                const title = row.querySelector("td:first-child").textContent.toLowerCase();
                
                if (title.includes(searchValue)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    });
</script>
<style>
    /* Centrer et limiter la largeur du tableau */
    .wrapper {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    /* Style du tableau */
    .stock-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
    }

    .stock-table th, .stock-table td {
        padding: 12px;
        text-align: left;
    }

    .stock-table th {
        background: #0077cc;
        color: white;
        font-weight: bold;
    }

    .stock-table tr {
        transition: background 0.3s ease;
    }

    .stock-table tr:nth-child(even) {
        background: #f9f9f9;
    }

    .stock-table tr:hover {
        background: #e3f2fd;
    }

    /* Style de la barre de recherche */
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
        border: none;
        background: none;
        padding: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
    }

    .search-icon {
        width: 16px;
        height: 16px;
    }
</style>

