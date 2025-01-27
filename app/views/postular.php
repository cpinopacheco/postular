<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postular</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/postular.css">
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
                <section class="form-section">
                    <h3>DATOS PERSONALES</h3>

                    <form>
                        <div class="rows-container">
                            <div class="form-row">
                                <label>Nombre:</label>
                                <span class="form-value">Juan Pérez (ejemplo)</span>
                            </div>
                            <div class="form-row">
                                <label>Código:</label>
                                <span class="form-value">986050v (ejemplo)</span>
                            </div>
                            <div class="form-row">
                                <label>Grado:</label>
                                <span class="form-value">Primer Grado (ejemplo)</span>
                            </div>
                            <div class="form-row">
                                <label>Correo Institucional:</label>
                                <input type="email" required>
                            </div>
                            <div class="form-row">
                                <label>Email Personal:</label>
                                <input type="email" required>
                            </div>
                            <div class="form-row">
                                <label>Teléfono IP dotación:</label>
                                <input type="tel" required>
                            </div>
                        </div>
                        <div class="obligations-container">
                            <h2>Obligaciones alumnos en modalidad e-learning año <?php echo date('Y'); ?></h2>
                            <p>
                                Lo anterior, se describe de manera específica por medio de los siguientes puntos:
                            </p>
                            <ol class="obligations-list">
                                <li>Tomar conocimiento de sus obligaciones como alumno.</li>
                                <li>Destinar al menos una hora diaria de conexión a plataforma.</li>
                                <li>Para el personal de Nombramiento Supremo e Institucional, responder a las exigencias académicas dispuestas para los procesos académicos año <?php echo date('Y'); ?>.</li>
                                <li>El correo electrónico proporcionado será utilizado para la difusión de información institucional.</li>
                            </ol>
                            <p>Ante duda o consulta, llamar a los IP: 21480, 21491, 21494 o en su defecto escribir al correo <a href="mailto:seccion.perfeccionamiento@gmail.com">seccion.perfeccionamiento@gmail.com</a>, de este Centro Nacional de Perfeccionamiento y Capacitación.</p>
                            <strong>Nota:</strong> El punto 3 debería actualizarse al año actual si corresponde.
                        </div>
                        <div class="radio-group">
                            <label>Acepto las obligaciones como alumno</label>
                            <div class="radio-options">
                                <label class="radio-label">
                                    <input type="radio" name="acepto" value="si" required>
                                    Sí
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="acepto" value="no" required>
                                    No
                                </label>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="submit" class="submit-btn">Postular</button>
                            <button type="button" class="cancel-btn">Cancelar</button>
                        </div>
                    </form>

                </section>
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