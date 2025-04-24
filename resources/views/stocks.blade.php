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
        <div id="loading" class="text-center">Chargement des données...</div>
        <div id="error" class="text-center text-red-500" style="display: none;"></div>
        <div id="filmContainer" style="display: none;">
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Films Disponibles</th>
                        <th>Stock Total</th>
                        <th>Films Loués</th>
                    </tr>
                </thead>
                <tbody id="filmTableBody">
                    <!-- Le contenu sera rempli dynamiquement par JavaScript -->
                </tbody>
            </table>
            
            <!-- Conteneur de pagination ajouté -->
            <div class="pagination-container" id="pagination-container" style="display: none;">
                <div class="pagination">
                    <button id="prev-page" class="pagination-button">&laquo; Précédent</button>
                    <div class="page-numbers" id="page-numbers">
                        <!-- Les numéros de page seront générés par JavaScript -->
                    </div>
                    <button id="next-page" class="pagination-button">Suivant &raquo;</button>
                </div>
            </div>
        </div>
        <div id="noData" class="text-center" style="display: none;">Aucun film disponible.</div>
    </div>
</x-app-layout>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Use the web route instead of the API route
        const apiUrl = "/stock-films";
        const loadingElement = document.getElementById("loading");
        const errorElement = document.getElementById("error");
        const filmContainer = document.getElementById("filmContainer");
        const noDataElement = document.getElementById("noData");
        const filmTableBody = document.getElementById("filmTableBody");
        const searchInput = document.getElementById("searchQuery");
        const paginationContainer = document.getElementById("pagination-container");
        
        let films = [];
        // Variables de pagination
        const itemsPerPage = 10; // Nombre d'éléments par page
        let currentPage = 1;
        let filteredFilms = [];
        let totalPages = 0;

        // Fonction pour défiler vers le haut de la page
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Fonction pour créer les boutons de pagination
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

        // Fonction pour afficher une page spécifique
        function showPage(page) {
            currentPage = page;
            const startIndex = (page - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredFilms.length);
            const currentPageItems = filteredFilms.slice(startIndex, endIndex);
            
            filmTableBody.innerHTML = '';
            
            currentPageItems.forEach((film, idx) => {
                // Générer un id unique pour chaque ligne
                const rowId = `film-row-${startIndex + idx}`;
                const dispoId = `dispo-${startIndex + idx}`;
                const louesId = `loues-${startIndex + idx}`;
                // On stocke les valeurs initiales pour chaque ligne
                const filmsDisponibles = film.filmsDisponibles ?? 0;
                const totalLoues = film.totalLoues ?? 0;
                const totalStock = film.totalStock ?? 0;

                const row = document.createElement('tr');
                row.id = rowId;
                row.innerHTML = `
                    <td>${film.title || 'Titre inconnu'}</td>
                    <td id="${dispoId}">${filmsDisponibles}</td>
                    <td>${totalStock}</td>
                    <td>
                        <button class="loues-btn" data-action="minus" data-row="${startIndex + idx}">-</button>
                        <span id="${louesId}">${totalLoues}</span>
                        <button class="loues-btn" data-action="plus" data-row="${startIndex + idx}">+</button>
                    </td>
                `;
                filmTableBody.appendChild(row);
            });

            // Ajouter les listeners pour les boutons + et -
            document.querySelectorAll('.loues-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const rowIdx = parseInt(this.getAttribute('data-row'));
                    const action = this.getAttribute('data-action');
                    const film = filteredFilms[rowIdx];
                    const dispoId = `dispo-${rowIdx}`;
                    const louesId = `loues-${rowIdx}`;
                    const dispoElem = document.getElementById(dispoId);
                    const louesElem = document.getElementById(louesId);

                    let dispo = parseInt(dispoElem.textContent);
                    let loues = parseInt(louesElem.textContent);
                    const totalStock = film.totalStock ?? 0;

                    if (action === 'plus' && dispo > 0) {
                        loues += 1;
                        dispo -= 1;
                    } else if (action === 'minus' && loues > 0) {
                        loues -= 1;
                        dispo += 1;
                    }
                    // Empêcher de dépasser les bornes
                    if (loues < 0) loues = 0;
                    if (dispo < 0) dispo = 0;
                    if (loues + dispo > totalStock) {
                        dispo = totalStock - loues;
                    }

                    louesElem.textContent = loues;
                    dispoElem.textContent = dispo;
                });
            });

            document.getElementById('prev-page').disabled = page === 1;
            document.getElementById('next-page').disabled = page === totalPages;

            // Mettre à jour la classe active sur les numéros de page
            updatePaginationButtons(page);

            // Défiler vers le haut de la page
            scrollToTop();
        }

        // Fonction pour mettre à jour le tableau en fonction de la recherche
        function updateTableWithSearch(searchValue) {
            filteredFilms = searchValue ? 
                films.filter(film => film.title.toLowerCase().includes(searchValue.toLowerCase())) : 
                films;
            
            if (filteredFilms.length === 0) {
                filmContainer.style.display = "block";
                paginationContainer.style.display = "none";
                filmTableBody.innerHTML = '';
                noDataElement.style.display = "block";
                noDataElement.textContent = "Aucun film ne correspond à votre recherche.";
            } else {
                filmContainer.style.display = "block";
                noDataElement.style.display = "none";
                
                // Calculer le nombre total de pages
                totalPages = Math.ceil(filteredFilms.length / itemsPerPage);
                
                // Si le nombre d'éléments est supérieur à itemsPerPage, afficher la pagination
                if (filteredFilms.length > itemsPerPage) {
                    paginationContainer.style.display = "flex";
                } else {
                    paginationContainer.style.display = "none";
                }
                
                // Afficher la première page
                showPage(1);
            }
        }

        // Fetch data from API through our proxy
        fetch(apiUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                loadingElement.style.display = "none";
                
                if (data && data.length > 0) {
                    films = data;
                    updateTableWithSearch('');
                    filmContainer.style.display = "block";
                } else {
                    noDataElement.style.display = "block";
                }
            })
            .catch(error => {
                loadingElement.style.display = "none";
                errorElement.style.display = "block";
                errorElement.textContent = `Erreur lors du chargement des données: ${error.message}`;
            });

        // Event listener pour la recherche
        searchInput.addEventListener("input", function() {
            updateTableWithSearch(searchInput.value);
        });

        // Event listeners pour les boutons de pagination
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
    
    /* Styles pour la pagination */
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
        background-color: #0077cc;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .pagination-button:hover {
        background-color: #005fa3;
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
        background-color: #0077cc;
        color: white;
    }

    .page-ellipsis {
        padding: 8px 6px;
        display: flex;
        align-items: center;
        font-weight: bold;
    }

    /* Style pour les boutons + et - */
    .loues-btn {
        padding: 4px 10px;
        margin: 0 4px;
        background: #0077cc;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .loues-btn:hover {
        background: #005fa3;
    }
</style>

