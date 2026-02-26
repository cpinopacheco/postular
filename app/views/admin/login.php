<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Perfeccionamiento</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/login.css">
    <style>
        :root {
            --admin-primary: #004d40;
            --admin-accent: #00cca3;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #002d20 0%, #004d40 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .admin-login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: cardAppear 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            z-index: 10;
        }

        @keyframes cardAppear {
            from { opacity: 0; transform: translateY(20px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .admin-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .admin-logo {
            width: 80px;
            height: auto;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 0 15px rgba(0, 204, 163, 0.3));
        }

        .admin-header h1 {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .admin-header p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .admin-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .admin-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 4px rgba(0, 204, 163, 0.15);
        }

        .submit-btn {
            width: 100%;
            background: var(--admin-accent);
            color: #002d20;
            border: none;
            border-radius: 12px;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0, 204, 163, 0.4);
            filter: brightness(1.1);
        }

        .alert-error {
            background: rgba(244, 67, 54, 0.15);
            border: 1px solid rgba(244, 67, 54, 0.3);
            color: #ff8a80;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }

        /* Decorative blobs */
        .blob {
            position: absolute;
            width: 300px;
            height: 300px;
            background: var(--admin-accent);
            filter: blur(80px);
            border-radius: 50%;
            opacity: 0.1;
            z-index: 1;
        }
        .blob-1 { top: -10%; left: -10%; }
        .blob-2 { bottom: -10%; right: -10%; }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="admin-login-card">
        <header class="admin-header">
            <div class="logo-wrapper" style="margin-bottom: 1rem;">
                <img src="/public/images/cenpecar-logo.png" alt="Logo" class="admin-logo">
            </div>
            <h1>Panel de Control</h1>
            <p>Acceso administrativo exclusivo</p>
        </header>

        <?php if (isset($error)): ?>
            <div class="alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="/index.php?action=admin_login" method="POST">
            <div class="form-group">
                <div class="input-wrapper">
                    <input type="password" name="password" class="admin-input" placeholder="Contraseña de Administrador" required autofocus>
                </div>
            </div>
            
            <button type="submit" class="submit-btn" id="login-btn">
                <span class="btn-text">Iniciar Sesión</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
        </form>

        <div style="margin-top: 2rem; text-align: center;">
            <a href="/" style="color: rgba(255, 255, 255, 0.4); text-decoration: none; font-size: 0.75rem; transition: color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255, 255, 255, 0.4)'">
                Volver al inicio
            </a>
        </div>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.getElementById('login-btn');
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';
            btn.innerHTML = 'Verificando...';
        });
    </script>
</body>
</html>
