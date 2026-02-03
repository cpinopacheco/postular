<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
            border-top: 5px solid <?php echo ($tipo === 'error') ? '#dc3545' : '#28a745'; ?>;
        }
        h2 { margin-top: 0; color: #333; }
        p { color: #666; font-size: 1.1rem; }
        .btn {
            display: inline-block;
            margin-top: 1rem;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2><?php echo ($tipo === 'error') ? 'No es posible postular' : 'Información'; ?></h2>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
        <a href="/" class="btn">Volver al inicio</a>
    </div>
</body>
</html>
