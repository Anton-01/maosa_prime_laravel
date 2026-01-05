<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maosa Prime - Acceso Denegado</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Variables CSS para consistencia */
        :root {
            --primary-text-color: #333;
            --secondary-text-color: #666;
            --accent-color: #ef4444;
            --background-color: #f7fafc;
            --container-bg: #fff;
            --border-color: #e2e8f0;
            --link-color: #3b82f6;
            --link-hover-color: #2563eb;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--background-color);
            color: var(--primary-text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        .error-container {
            text-align: center;
            background-color: var(--container-bg);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            max-width: 500px;
            width: 100%;
            border: 1px solid var(--border-color);
        }

        h1 {
            font-size: 6rem; /* Equivalente a 96px */
            color: var(--accent-color);
            margin-bottom: 0.5rem;
            line-height: 1;
            font-weight: 700;
        }

        h2 {
            font-size: 1.875rem; /* Equivalente a 30px */
            color: var(--secondary-text-color);
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-weight: 400;
        }

        p {
            font-size: 1.125rem; /* Equivalente a 18px */
            color: var(--secondary-text-color);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        p:last-of-type {
            margin-bottom: 0;
        }

        a {
            color: var(--link-color);
            text-decoration: none;
            font-weight: 700;
            transition: color 0.2s ease-in-out;
        }

        a:hover {
            color: var(--link-hover-color);
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="error-container">
    <h1>403</h1>
    <h2>Acceso Denegado</h2>
    <p>
        {{ $exception->getMessage() ?: 'Lo sentimos, no tienes permiso para acceder a esta página.' }}
    </p>
    <p>
        <a href="{{ url('/') }}">Volver a la página de inicio</a>
    </p>
</div>
</body>
</html>
