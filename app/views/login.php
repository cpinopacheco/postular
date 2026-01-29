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
                    <h3>Bienvenido</h3>
                    <form class="login-form">
                        <div class="form-group">
                            <label for="codigo">Código de Funcionario</label>
                            
                            <div class="login-input-container">
                                <div class="floating-input-group" style="flex: 1;">
                                    <input type="text" id="codigo" placeholder=" " required maxlength="6" pattern="\d{6}" class="floating-input" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                    <label for="codigo" class="floating-label">Número</label>
                                </div>
                                
                                <span class="separator">-</span>
                                
                                <div class="floating-input-group" style="width: 80px;">
                                    <input type="text" id="verificador" placeholder=" " required maxlength="1" pattern="[a-zA-Z0-9]" class="floating-input" style="text-align: center;" oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');">
                                    <label for="verificador" class="floating-label" style="left: 50%; transform: translateX(-50%);">DV</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="submit-btn">
                            Ingresar al Sistema
                        </button>
                    </form>
                </section>

                <section class="info-section">
                    <h3>Proceso de Inscripción de Niveles de Perfeccionamiento</h3>
                    <p>Orientado al Personal de Nombramiento Supremo y Personal de Nombramiento Institucional.</p>

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