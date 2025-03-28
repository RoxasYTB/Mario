<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
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
            background: rgb(37, 129, 235);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .add-button:hover {
            background: rgb(78, 90, 194);
        }

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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('films.index') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('films.index')" :active="request()->routeIs('films')">
                        {{ __('Films') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('stocks')" :active="request()->routeIs('stocks')">
                        {{ __('Gestion des stocks') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>


                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">

            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

            </div>
        </div>
    </div>
</nav>