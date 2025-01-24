<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/public/css/login.css">
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
                <section class="login-section">
                    <h3>INGRESE SUS DATOS</h3>
                    <form class="login-form">
                        <div class="form-group">
                            <label for="codigo">CÓDIGO FUNCIONARIO</label>
                            <div class="input-group">
                                <input type="text" id="codigo" placeholder="986050" required>
                                <span class="separator">-</span>
                                <input type="text" id="verificador" maxlength="1" placeholder="5" required style="width: 50px;">
                            </div>
                            <span class="helper-text">Ej: 986050 - 5</span>
                        </div>
                        <button type="submit" class="submit-btn">
                            Ingresar
                        </button>
                    </form>
                </section>

                <section class="info-section">
                    <h3>PROCESO DE INSCRIPCIÓN DE LOS NIVELES DE PERFECCIONAMIENTOS</h3>
                    <p>PARA PERSONAL DE NOMBRAMIENTO SUPREMO Y PERSONAL DE NOMBRAMIENTO INSTITUCIONAL</p>

                    <div class="contact-info">
                        <h4>PARA RESOLVER DUDAS O CONSULTAS, COMUNÍQUESE A LOS SIGUIENTES NÚMEROS IP:</h4>
                        <div class="phone-numbers">
                            <div class="phone-badge">21480</div>
                            <div class="phone-badge">21491</div>
                            <div class="phone-badge">21494</div>
                        </div>
                    </div>
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