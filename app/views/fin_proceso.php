<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceso Finalizado</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/fin_proceso.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <header class="header">
                <img src="/public/images/cenpecar-logo.png" alt="Logo Institucional" class="logo">
                <h1>PROCESO DE INSCRIPCIÓN <?php echo date('Y'); ?></h1>
                <h2>NIVELES DE PERFECCIONAMIENTO</h2>
            </header>

            <main class="main-content">
                <div class="status-container">
                    <div class="status-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    
                    <h3>El proceso de inscripción ha finalizado</h3>
                    <p class="status-message">
                        Le informamos que el plazo establecido para postular a los Procesos de Perfeccionamiento de este año ha concluido.
                    </p>

                    <div class="info-alert">
                        Para más información o consultas sobre su situación, puede contactar con la Sección de Perfeccionamiento.
                    </div>

                    <div class="contact-section">
                        <h4>¿Necesita asistencia?</h4>
                        <div class="contact-grid">
                            <div class="contact-item">
                                <span class="contact-label">Mesa de Ayuda (IP)</span>
                                <div class="ip-badges">
                                    <span>21480</span>
                                    <span>21491</span>
                                    <span>21494</span>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="button-group">
                        <a href="/" class="submit-btn">Volver al Inicio</a>
                    </div>
                </div>
            </main>

            <footer class="footer">
                <h3>SECCIÓN DE PERFECCIONAMIENTO</h3>
                <p>CENTRO NACIONAL DE PERFECCIONAMIENTO Y CAPACITACIÓN</p>
                <p>CARABINEROS DE CHILE</p>
            </footer>
        </div>
    </div>
</body>

</html>