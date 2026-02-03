<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/mensaje.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon-container <?php echo ($tipo === 'error') ? 'error' : 'success'; ?>">
                <?php if ($tipo === 'error'): ?>
                    <!-- Icono Error (X) -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                <?php else: ?>
                    <!-- Icono Info/Success (i) -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                <?php endif; ?>
            </div>

            <h2><?php echo ($tipo === 'error') ? 'No es posible postular' : 'Información'; ?></h2>
            <p><?php echo htmlspecialchars($mensaje); ?></p>
            
            <div class="btn-container">
                <a href="/" class="submit-btn" style="text-decoration: none;">Volver al inicio</a>
            </div>
        </div>
    </div>
</body>
</html>
