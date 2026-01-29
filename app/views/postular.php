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
                            <!-- Datos Informativos (Read Only) con diseño limpio -->
                            <div class="info-grid">
                                <div class="readonly-field-group">
                                    <span class="readonly-label">Nombre del Funcionario</span>
                                    <span class="readonly-value">Juan Pérez (ejemplo)</span>
                                </div>
                                <div class="readonly-field-group">
                                    <span class="readonly-label">Código</span>
                                    <span class="readonly-value">986050v (ejemplo)</span>
                                </div>
                                <div class="readonly-field-group">
                                    <span class="readonly-label">Grado Actual</span>
                                    <span class="readonly-value">Primer Grado (ejemplo)</span>
                                </div>
                            </div>

                            <hr style="border: 0; border-top: 1px solid #eee; margin: 1.5rem 0;">

                            <!-- Campos Editables con Floating Labels -->
                            <div class="floating-input-group">
                                <input type="email" id="email-inst" required placeholder=" " class="floating-input">
                                <label for="email-inst" class="floating-label">Correo Institucional</label>
                            </div>

                            <div class="floating-input-group">
                                <input type="email" id="email-pers" required placeholder=" " class="floating-input">
                                <label for="email-pers" class="floating-label">Email Personal</label>
                            </div>

                            <div class="floating-input-group">
                                <input type="tel" id="tel-ip" required placeholder=" " class="floating-input">
                                <label for="tel-ip" class="floating-label">Teléfono IP dotación</label>
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
                        <div class="radio-group modern-radio-group">
                            <span class="group-label">Confirmación de obligaciones</span>
                            <div class="radio-options">
                                <div class="radio-option">
                                    <input type="radio" name="acepto" id="option-yes" value="si" required>
                                    <label class="radio-card" for="option-yes">
                                        <div class="card-content">
                                            <span class="icon">✅</span>
                                            <span class="text">Sí, acepto las obligaciones</span>
                                        </div>
                                        <div class="selection-indicator"></div>
                                    </label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" name="acepto" id="option-no" value="no" required>
                                    <label class="radio-card" for="option-no">
                                        <div class="card-content">
                                            <span class="icon">❌</span>
                                            <span class="text">No acepto</span>
                                        </div>
                                        <div class="selection-indicator"></div>
                                    </label>
                                </div>
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