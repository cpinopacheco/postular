<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración Admin - Perfeccionamiento</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/common.css">
    <style>
        :root {
            --admin-bg: #f8fafc;
            --admin-sidebar: #004d40;
            --admin-accent: #00cca3;
            --admin-text: #1e293b;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--admin-bg);
            margin: 0;
            display: flex;
            height: 100vh;
            color: var(--admin-text);
            overflow: hidden;
        }

        /* Sidebar - Same as Dashboard for consistency */
        .sidebar {
            width: 280px;
            background: var(--admin-sidebar);
            color: white;
            padding: 3rem 1.5rem;
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            box-sizing: border-box;
            z-index: 1000;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .sidebar-logo { width: 50px; height: auto; }
        .sidebar-title { font-size: 1.5rem; font-weight: 700; letter-spacing: -0.5px; }

        .nav-menu { list-style: none; padding: 0; margin: 0; flex-grow: 1; }
        .nav-item { margin-bottom: 0.5rem; }
        .nav-link {
            display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem;
            color: rgba(255, 255, 255, 0.7); text-decoration: none; border-radius: 12px;
            transition: all 0.2s ease; font-weight: 500;
        }
        .nav-link:hover, .nav-link.active { background: rgba(255, 255, 255, 0.1); color: white; }
        .nav-link.active { background: var(--admin-accent); color: #002d20; }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            background: var(--admin-sidebar);
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 12px;
            cursor: pointer;
            margin-right: 1rem;
            margin-bottom: 1rem;
            
            box-shadow: 0 4px 12px rgba(0, 45, 32, 0.15);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 999;
        }
        
        .sidebar-overlay.active { display: block; }

        .logout-btn {
            margin-top: auto;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.875rem;
            border-radius: 12px;
            color: white;
            text-decoration: none;
            text-align: center;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: rgba(244, 67, 54, 0.1);
            border-color: #f44336;
            color: #f44336;
        }

        .main-content {
            flex-grow: 1;
            padding: 2.5rem;
            max-width: 1400px;
            margin-left: 280px;
            width: calc(100% - 280px);
            height: 100vh;
            overflow-y: auto;
            box-sizing: border-box;
            background: var(--admin-bg);
            transition: margin-left 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .welcome-text h1 {
            font-size: 1.875rem;
            margin: 0;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: var(--admin-sidebar);
        }

        .config-card {
            background: white;
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-section {
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .section-header {
            margin-bottom: 1.5rem;
        }

        .section-header h3 {
            font-size: 1.125rem;
            font-weight: 700;
            margin: 0;
            color: var(--admin-sidebar);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-header p {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .selection-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
        }

        .select-all-btn {
            width: auto;
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            cursor: pointer;
            white-space: nowrap;
        }

        .grid-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .field-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
        }

        .admin-input {
            padding: 0.875rem 1rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9375rem;
            transition: all 0.2s;
            background: #f8fafc;
        }

        .admin-input:focus {
            outline: none;
            border-color: var(--admin-accent);
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 204, 163, 0.1);
        }

        .grid-checkboxes {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
            background: #f8fafc;
            padding: 1rem;
            border-radius: 16px;
            border: 1.5px solid #e2e8f0;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .checkbox-item:hover {
            background: rgba(0, 77, 64, 0.05);
        }

        .checkbox-item input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--admin-sidebar);
        }

        .checkbox-label {
            font-size: 0.875rem;
            color: #475569;
            font-weight: 500;
        }

        .save-btn {
            background: var(--admin-sidebar);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 77, 64, 0.2);
            filter: brightness(1.1);
        }

        .alert-success {
            background: #effaf5;
            border: 1px solid #cae9db;
            color: #2b6a4a;
            padding: 1rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; } to { opacity: 1; }
        }

        /* Help tooltip */
        .help-box {
            background: #f1f5f9;
            padding: 1rem;
            border-radius: 12px;
            margin-top: 1rem;
            font-size: 0.8125rem;
            color: #475569;
            display: flex;
            gap: 0.5rem;
        }
        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 1rem;
            }
            .menu-toggle {
                display: flex;
            }
            .config-card {
                padding: 1.5rem;
            }
            .sidebar-overlay.active {
                opacity: 1;
                visibility: visible;
            }
        }

        @media (max-width: 640px) {
            .header-top {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .welcome-text h1 {
                font-size: 1.5rem;
            }
            .grid-inputs {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            .grid-checkboxes > div {
                padding: 1rem !important;
            }
            .selection-header {
                flex-direction: column;
                align-items: stretch;
            }
            .select-all-btn {
                width: 100%;
                margin-top: 1rem;
                padding: 0.875rem;
                font-size: 0.9375rem;
            }
            .grade-category-container {
                padding: 1rem !important;
            }
            .save-btn {
                width: 100%;
                justify-content: center;
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="admin-body">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <header class="sidebar-header">
            <img src="/public/images/cenpecar-logo.png" alt="Logo" class="sidebar-logo">
            <span class="sidebar-title">Admin Panel</span>
        </header>
        <ul class="nav-menu">
            <li class="nav-item"><a href="/index.php?action=admin" class="nav-link"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg><span>Dashboard</span></a></li>
            <li class="nav-item"><a href="/index.php?action=admin_config" class="nav-link active"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1-2-2 2 2 0 0 1 2-2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg><span>Configuración</span></a></li>
        </ul>
        <a href="/index.php?action=admin_logout" class="logout-btn">Cerrar Sesión</a>
    </aside>

    <main class="main-content">
        <header class="header-top">
            <div class="welcome-text">
                <h1>
                    <button class="menu-toggle" id="menuToggle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </button>
                    Configuración del Sistema
                </h1>
                <p>Gestione los parámetros globales del proceso activo.</p>
            </div>
        </header>

        <?php if (isset($_SESSION['success_msg'])): ?>
            <div class="alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <span><?= htmlspecialchars($_SESSION['success_msg']) ?></span>
                <?php unset($_SESSION['success_msg']); ?>
            </div>
        <?php endif; ?>

        <div class="config-card">
            <form action="/index.php?action=admin_save" method="POST">
                <section class="form-section">
                    <div class="section-header">
                        <h3>Plazos del Proceso</h3>
                        <p>Defina el rango de fechas en que el sistema permitirá nuevas inscripciones.</p>
                    </div>
                    <div class="grid-inputs">
                        <div class="field-group">
                            <label class="field-label">Fecha de Inicio</label>
                            <input type="date" name="proceso_inicio" class="admin-input" value="<?= htmlspecialchars($configRaw['PROCESO_FECHA_INICIO'] ?? '') ?>" required>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Fecha de Término</label>
                            <input type="date" name="proceso_fin" class="admin-input" value="<?= htmlspecialchars($configRaw['PROCESO_FECHA_FIN'] ?? '') ?>" required>
                        </div>
                    </div>
                </section>

                <section class="form-section">
                    <div class="section-header selection-header">
                        <div>
                            <h3>Grados Habilitados</h3>
                            <p>Especifique qué grados jerárquicos se encuentran habilitados para el proceso de inscripción:</p>
                        </div>
                        <button type="button" onclick="toggleAllGrades()" class="admin-input select-all-btn">Seleccionar Todo</button>
                    </div>
                    
                    <?php 
                        $enabledGrades = array_map('trim', explode(',', $configRaw['PROCESO_GRADOS_HABILITADOS'] ?? ''));
                    ?>
                    
                    <div class="grid-checkboxes" style="display: block;">
                        <?php foreach ($availableGrades as $categoria => $contenido): ?>
                            <div class="grade-category-container" style="margin-bottom: 2rem; background: rgba(0, 0, 0, 0.02); padding: 1.5rem; border-radius: 16px; border: 1px dashed #e2e8f0;">
                                <h3 style="margin: 0 0 1.5rem 0; color: var(--admin-sidebar); font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                                    <?= $categoria ?>
                                </h3>

                                <?php if (isset($contenido[0])): // Es una categoría simple (PNS) ?>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.75rem;">
                                        <?php foreach ($contenido as $grade): ?>
                                            <label class="checkbox-item">
                                                <input type="checkbox" name="grados_habilitados[]" value="<?= htmlspecialchars($grade) ?>" 
                                                    <?= in_array($grade, $enabledGrades) ? 'checked' : '' ?>>
                                                <span class="checkbox-label"><?= htmlspecialchars($grade) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: // Es una categoría con subgrupos (PNI) ?>
                                    <?php foreach ($contenido as $subgrupo => $grados): ?>
                                        <div style="margin-bottom: 1.25rem;">
                                            <h4 style="margin: 0 0 0.75rem 0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.25rem;">
                                                <?= $subgrupo ?>
                                            </h4>
                                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.75rem;">
                                                <?php foreach ($grados as $grade): ?>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="grados_habilitados[]" value="<?= htmlspecialchars($grade) ?>" 
                                                            <?= in_array($grade, $enabledGrades) ? 'checked' : '' ?>>
                                                        <span class="checkbox-label"><?= htmlspecialchars($grade) ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="help-box">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        <span>Marque los grados que desea habilitar. Si no marca ninguno, <strong>nadie podrá postular</strong>. Use el botón "Seleccionar Todo" si desea habilitar el proceso para todo el personal.</span>
                    </div>
                </section>

                <div style="text-align: right; margin-top: 2rem;">
                    <button type="submit" class="save-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        function toggleAllGrades() {
            const checkboxes = document.querySelectorAll('input[name="grados_habilitados[]"]');
            const btn = event.target;
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => cb.checked = !allChecked);
            btn.textContent = allChecked ? "Seleccionar Todo" : "Deseleccionar Todo";
        }
    </script>
    <script>
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleMenu() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }

        menuToggle.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);

        sidebar.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 1024) {
                    toggleMenu();
                }
            });
        });
    </script>
</body>
</html>
