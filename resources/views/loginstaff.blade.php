<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFTG - Village Vacances Rétro - Connexion</title>
    <style>
        /* Réinitialisation et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --background-color: #f0f4f8;
            --text-color: #2d3748;
            --app-background: #ffffff;
            --app-shadow: rgba(0,0,0,0.1);
            --input-border: #cbd5e0;
            --button-bg: #4299e1;
            --button-text: white;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.8;
        }

        .container {
            max-width: 500px;
            margin: 100px auto;
            padding: 40px 20px;
        }

        h1 {
            color: var(--text-color);
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            letter-spacing: -0.5px;
            font-size: 2.5em;
        }

        /* Section d'application */
        .app-section {
            background-color: var(--app-background);
            border-radius: 20px;
            padding: 50px;
            margin-bottom: 60px;
            box-shadow: 0 15px 35px var(--app-shadow);
        }

        /* Formulaires et boutons */
        input[type="text"], input[type="password"], button, input[type="submit"] {
            width: 100%;
            padding: 18px;
            margin-bottom: 25px;
            border: 2px solid var(--input-border);
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: var(--app-background);
            color: var(--text-color);
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: var(--button-bg);
            outline: none;
            box-shadow: 0 0 0 4px rgba(66, 153, 225, 0.3);
        }

        button, input[type="submit"] {
            background-color: var(--button-bg);
            color: var(--button-text);
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            transition: all 0.3s ease;
        }

        button:hover, input[type="submit"]:hover {
            background-color: var(--button-bg);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(66, 153, 225, 0.4);
        }

        button:active, input[type="submit"]:active {
            transform: translateY(1px);
        }
        
        .error-message {
            color: #e53e3e;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="app-section">
            <h1>RFTG - Connexion</h1>
            
            @if(session('error'))
            <div class="error-message">
                {{ session('error') }}
            </div>
            @endif
            
            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <input type="text" name="username" placeholder="Nom d'utilisateur" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <input type="submit" value="Se connecter">
            </form>
        </div>
    </div>

    <script>
        // If login is successful, this script will redirect to the filmlist page
        // The actual authentication will be handled by the controller
    </script>
</body>
</html>
