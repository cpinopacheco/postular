<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
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
                    
                    <?php if (isset($error)): ?>
                        <div class="alert-error">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span><?php echo htmlspecialchars($error); ?></span>
                        </div>
                    <?php endif; ?>

                    <form class="login-form" action="/index.php?action=validar" method="POST">
                        <div class="form-group">
                            <label for="codigo">Código de Funcionario</label>
                            
                            <div class="login-input-container">
                                <div class="floating-input-group" style="flex: 1;">
                                    <input type="text" id="codigo" name="codigo_num" placeholder=" " required maxlength="6" pattern="\d{6}" class="floating-input" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                    <label for="codigo" class="floating-label">Número</label>
                                </div>
                                
                                <span class="separator">-</span>
                                
                                <div class="floating-input-group" style="width: 80px;">
                                    <input type="text" id="verificador" name="codigo_dv" placeholder=" " required maxlength="1" pattern="[a-zA-Z0-9]" class="floating-input" style="text-align: center; text-transform: uppercase;" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');">
                                    <label for="verificador" class="floating-label" style="left: 50%; transform: translateX(-50%);">DV</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="submit-btn" id="login-btn">
                            <span class="spinner"></span>
                            <span class="btn-text">Ingresar</span>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.querySelector('.login-form');
            const loginBtn = document.getElementById('login-btn');
            const codigoInput = document.getElementById('codigo');

            // 1. Auto-foco inteligente
            if (codigoInput) {
                codigoInput.focus();
            }

            // 2. Manejo del estado de carga (Loading State)
            loginForm.addEventListener('submit', function() {
                loginBtn.classList.add('loading');
                loginBtn.disabled = true;
            });
        });
    </script>
</body>

</html>