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
                <div class="logo-wrapper">
                    <img src="/public/images/cenpecar-logo.png" alt="Logo Institucional" class="logo">
                </div>
                <h1>PROCESO DE INSCRIPCIÓN <?php echo date('Y'); ?></h1>
                <h2>NIVELES DE PERFECCIONAMIENTO</h2>
            </header>

            <main class="main-content">
                <section class="login-section">
                    <h3>Bienvenido</h3>
                    <p style="text-align: center; color: #666; margin-bottom: 0.75rem; margin-top: 0.5rem; font-size: 1rem; font-weight: 500;">Ingrese su código de funcionario:</p>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert-error">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span><?php echo htmlspecialchars($error); ?></span>
                        </div>
                    <?php endif; ?>

                    <form class="login-form" action="/index.php?action=validar" method="POST">
                        <div class="form-group">
                            <div class="login-input-container">
                                <div class="floating-input-group" style="flex: 1;">
                                    <input type="text" id="codigo" name="codigo" placeholder="EJ: 123456A" required maxlength="7" pattern="[0-9]{6}[A-Z]" class="floating-input" style="text-transform: uppercase; padding: 1rem;">
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
                            <div class="phone-badge">21513</div>
                            <div class="phone-badge">27293</div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="footer">
                <h3>SECCIÓN DE PERFECCIONAMIENTO</h3>
                <p>CENTRO NACIONAL DE PERFECCIONAMIENTO Y CAPACITACIÓN</p>
                <p>CARABINEROS DE CHILE</p>
                <div style="margin-top: 1.5rem; opacity: 0.3; font-size: 0.7rem;">
                    <a href="/index.php?action=admin_login" style="color: inherit; text-decoration: none;">Gestión</a>
                </div>
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

                // 2. Validación estricta: 6 números + 1 letra
                codigoInput.addEventListener('input', function(e) {
                    let value = this.value.toUpperCase().replace(/[^0-9A-Z]/g, '');
                    let validatedValue = '';
                    
                    for (let i = 0; i < value.length; i++) {
                        if (i < 6) {
                            // Primeros 6 deben ser números
                            if (/[0-9]/.test(value[i])) {
                                validatedValue += value[i];
                            }
                        } else if (i === 6) {
                            // El séptimo debe ser letra
                            if (/[A-Z]/.test(value[i])) {
                                validatedValue += value[i];
                            }
                        }
                    }
                    
                    this.value = validatedValue;
                });
            }

            // 3. Manejo del estado de carga (Loading State)
            loginForm.addEventListener('submit', function() {
                loginBtn.classList.add('loading');
                loginBtn.disabled = true;
            });
        });
    </script>
</body>

</html>