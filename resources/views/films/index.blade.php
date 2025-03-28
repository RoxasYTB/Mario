<link rel="stylesheet" href="../newStyle.css">

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 text-center">
            {{ __('Liste des DVDs') }}
        </h2>
    </x-slot>



    <div class="container">
        <div class="app-section">
            <h2 class="app-title">Voici la liste des films disponibles</h2>


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

            <div class="grid" id="filmsGrid">
                @foreach ($films as $index => $film)
                <div class="film-card" data-index="{{ $index }}">
                    <h3 class="film-title">{{$film['title'] }}</h3>
                    <p class="film-details line-clamp-3">{{ $film['description'] }}</p>
                    <div class="film-details" style="display: inline; flex-direction: column; align-items: left;">
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <span class="film-label">Année:</span> {{ $film['releaseYear'] }}
                        </div>
                        <div style="display: flex; align-items: center; gap: 5px;"></div>
                        <span class="film-label">Durée location:</span> {{ $film['rentalDuration'] }} jours

                        <div style="display: flex; align-items: center; gap: 5px;">
                            <span class="film-label">Tarif:</span> {{ $film['rentalRate'] }}€
                        </div>
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <span class="film-label">Évaluation:</span> {{ $film['rating'] }}
                        </div>
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <span class="film-label">Durée:</span> {{ $film['length'] }} minutes
                        </div>
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <span class="film-label">Coût de remplacement:</span> {{ $film['replacementCost'] }}€
                        </div>
                        @if(!empty($film['specialFeatures']))
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <span class="film-label">Bonus:</span> {{ $film['specialFeatures'] }}
                        </div>
                        @endif
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 15px;"></div>

                    <div class="action-buttons">
                        <a href="{{ route('films.edit', $film['filmId']) }}" class="action-button edit-button">
                            Modifier
                        </a>
                        <form action="{{ route('films.destroy', ['id' => $film['filmId']]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-button delete-button">
                                Supprimer
                            </button>
                        </form>
                    </div>

                    <style>
                        .action-buttons {
                            display: flex;
                            gap: 10px;
                            margin-top: 15px;
                        }

                        .action-button {
                            padding: 8px 16px;
                            border-radius: 6px;
                            font-weight: 500;
                            text-align: center;
                            cursor: pointer;
                            transition: background-color 0.3s, transform 0.2s;
                            border: none;
                            flex: 1;
                            display: block;
                            text-decoration: none;
                            font-size: 14px;
                        }

                        .edit-button {
                            background-color: #3b82f6;
                            color: white;
                            width: 100%;
                        }

                        .delete-button {
                            background-color: #3b82f6;
                            color: white;
                            width: 100%;
                        }

                        .action-button:hover {
                            background-color: #2563eb;
                            transform: translateY(-2px);
                        }
                    </style>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-container" id="pagination-container">
                <div class="pagination">
                    <button id="prev-page" class="pagination-button">&laquo; Précédent</button>
                    <div class="page-numbers" id="page-numbers">
                        <!-- Les numéros de page seront générés par JavaScript -->
                    </div>
                    <button id="next-page" class="pagination-button">Suivant &raquo;</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pagination-button {
            padding: 8px 16px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .pagination-button:hover {
            background-color: #2563eb;
        }

        .pagination-button:disabled {
            background-color: #94a3b8;
            cursor: not-allowed;
        }

        .page-numbers {
            display: flex;
            gap: 5px;
        }

        .page-number {
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            background-color: #f1f5f9;
            transition: background-color 0.3s;
        }

        .page-number:hover {
            background-color: #e2e8f0;
        }

        .page-number.active {
            background-color: #3b82f6;
            color: white;
        }

        .page-ellipsis {
            padding: 8px 6px;
            display: flex;
            align-items: center;
            font-weight: bold;
        }
    </style>

    <script>
        // Fonction pour supprimer un film
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

        // Script pour gérer la pagination côté client
        document.addEventListener('DOMContentLoaded', function() {
            // Nombre de films par page
            const itemsPerPage = 9;

            // Récupérer tous les films
            const films = document.querySelectorAll('.film-card');
            const totalFilms = films.length;
            const totalPages = Math.ceil(totalFilms / itemsPerPage);

            // Variables de pagination
            let currentPage = 1;

            // Fonction pour défiler vers le haut de la page
            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            // Fonction pour montrer seulement les films d'une page spécifique
            function showPage(page) {
                const startIndex = (page - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;

                films.forEach((film, index) => {
                    if (index >= startIndex && index < endIndex) {
                        film.style.display = 'block';
                    } else {
                        film.style.display = 'none';
                    }
                });

                // Mettre à jour l'état des boutons de pagination
                document.getElementById('prev-page').disabled = page === 1;
                document.getElementById('next-page').disabled = page === totalPages;

                // Mettre à jour la classe active sur les numéros de page
                const pageNumbers = document.querySelectorAll('.page-number');
                pageNumbers.forEach(btn => {
                    btn.classList.remove('active');
                    if (parseInt(btn.textContent) === page) {
                        btn.classList.add('active');
                    }
                });

                // Mettre à jour la page courante
                currentPage = page;

                // Défiler vers le haut de la page
                scrollToTop();

                // Mettre à jour l'affichage des boutons de pagination
                updatePaginationButtons(page);
            }

            // Fonction pour mettre à jour les boutons de pagination visibles
            function updatePaginationButtons(currentPage) {
                const pageNumbersContainer = document.getElementById('page-numbers');
                pageNumbersContainer.innerHTML = '';

                // Maximum 6 boutons à afficher
                const maxVisibleButtons = 6;

                // Cas simple: si le total est inférieur au maximum, afficher toutes les pages
                if (totalPages <= maxVisibleButtons) {
                    for (let i = 1; i <= totalPages; i++) {
                        addPageButton(i);
                    }
                } else {
                    // Logique pour afficher toujours certains boutons clés
                    // Toujours montrer la première page
                    addPageButton(1);

                    // Déterminer le range des pages à afficher autour de la page courante
                    let startPage = Math.max(2, currentPage - 1);
                    let endPage = Math.min(totalPages - 1, currentPage + 1);

                    // Ajuster si nécessaire pour avoir 3 pages autour de la page courante
                    if (currentPage <= 3) {
                        // Proche du début, montrer plus de pages suivantes
                        endPage = Math.min(totalPages - 1, 4);
                    } else if (currentPage >= totalPages - 2) {
                        // Proche de la fin, montrer plus de pages précédentes
                        startPage = Math.max(2, totalPages - 3);
                    }

                    // Ajouter ellipsis si nécessaire avant la plage
                    if (startPage > 2) {
                        const ellipsis = document.createElement('div');
                        ellipsis.classList.add('page-ellipsis');
                        ellipsis.textContent = '...';
                        pageNumbersContainer.appendChild(ellipsis);
                    }

                    // Ajouter les pages intermédiaires
                    for (let i = startPage; i <= endPage; i++) {
                        addPageButton(i);
                    }

                    // Ajouter ellipsis si nécessaire après la plage
                    if (endPage < totalPages - 1) {
                        const ellipsis = document.createElement('div');
                        ellipsis.classList.add('page-ellipsis');
                        ellipsis.textContent = '...';
                        pageNumbersContainer.appendChild(ellipsis);
                    }

                    // Toujours montrer la dernière page
                    addPageButton(totalPages);
                }
            }

            // Fonction helper pour ajouter un bouton de page
            function addPageButton(pageNum) {
                const pageBtn = document.createElement('div');
                pageBtn.classList.add('page-number');
                if (pageNum === currentPage) pageBtn.classList.add('active');
                pageBtn.textContent = pageNum;
                pageBtn.addEventListener('click', () => showPage(pageNum));
                document.getElementById('page-numbers').appendChild(pageBtn);
            }

            // Ajouter des écouteurs d'événements aux boutons précédent/suivant
            document.getElementById('prev-page').addEventListener('click', () => {
                if (currentPage > 1) {
                    showPage(currentPage - 1);
                }
            });

            document.getElementById('next-page').addEventListener('click', () => {
                if (currentPage < totalPages) {
                    showPage(currentPage + 1);
                }
            });

            // Initialiser la première page et les boutons de pagination
            updatePaginationButtons(1);
            showPage(1);

            // Fonction de recherche pour filtrer les films
            const searchInput = document.getElementById('searchQuery');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase();

                    // Si la requête est vide, afficher la pagination normale
                    if (query === '') {
                        document.getElementById('pagination-container').style.display = 'flex';
                        showPage(1);
                        return;
                    }

                    // Sinon, masquer la pagination et afficher uniquement les résultats de recherche
                    document.getElementById('pagination-container').style.display = 'none';

                    let matchCount = 0;
                    films.forEach(film => {
                        const title = film.querySelector('.film-title').textContent.toLowerCase();
                        const description = film.querySelector('.film-details').textContent.toLowerCase();

                        if (title.includes(query) || description.includes(query)) {
                            film.style.display = 'block';
                            matchCount++;
                        } else {
                            film.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>