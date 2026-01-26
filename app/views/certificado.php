<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/certificado.css">
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="content-wrapper">
                <header class="header">
                    <img src="/public/images/cenpecar-logo.png" alt="Logo Institucional" class="logo">
                    <div class="header-text">
                        DOCUMENTO QUE CERTIFICA LA INSCRIPCIÓN COMO ALUMNO EN EL NIVEL DE PERFECCIONAMIENTO CORRESPONDIENTE A SU GRADO PARA EL PROCESO <?php echo date('Y'); ?>.
                    </div>
                </header>

                <main>
                    <h2 class="section-title">DATOS PERSONALES</h2>
                    <div class="data-container">
                        <div class="data-row">
                            <span class="data-label">Nombre:</span>
                            <span class="data-value">Juan Pérez (ejemplo)</span>
                        </div>

                        <div class="data-row">
                            <span class="data-label">Código:</span>
                            <span class="data-value">986050v (ejemplo)</span>
                        </div>

                        <div class="data-row">
                            <span class="data-label">Calificación:</span>
                            <span class="data-value">{calificación}</span>
                        </div>

                        <div class="data-row">
                            <span class="data-label">Fecha de inscripción:</span>
                            <span class="data-value">{fecha}</span>
                        </div>

                        <div class="data-row">
                            <span class="data-label">Número de registro:</span>
                            <span class="data-value">{identificación}</span>
                        </div>
                    </div>
                </main>
            </div>

            <footer class="footer">
                <p class="footer-note">
                    Recuerde que al inscribirse en el curso, usted declara conocer y aceptar las "Obligaciones del Alumno".
                </p>
            </footer>
        </div>
    </div>
</body>

</html>