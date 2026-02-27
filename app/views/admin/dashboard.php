<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Perfeccionamiento</title>
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

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Sidebar */
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

        .sidebar-logo {
            width: 50px;
            height: auto;
        }

        .sidebar-title {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link.active {
            background: var(--admin-accent);
            color: #002d20;
        }

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

        /* Main Content */
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

        .welcome-text p {
            color: #64748b;
            margin-top: 0.25rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            background: #f1f5f9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--admin-sidebar);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Layout Sections */
        .layout-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .section-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .layout-grid > .section-card:nth-child(1) { animation-delay: 0.5s; }
        .layout-grid > .section-card:nth-child(2) { animation-delay: 0.6s; }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
        }

        /* Stats Table */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
        }

        .stats-table th {
            text-align: left;
            padding: 1rem;
            color: #64748b;
            font-weight: 600;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
        }

        .stats-table td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9375rem;
        }

        .grade-badge {
            background: #e0f2f1;
            color: #00796b;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .progress-bar {
            height: 8px;
            background: #f1f5f9;
            border-radius: 4px;
            overflow: hidden;
            width: 100px;
        }

        .progress-fill {
            height: 100%;
            background: var(--admin-accent);
            border-radius: 4px;
        }

        /* Recent Activity */
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f8fafc;
        }

        .activity-icon {
            width: 36px;
            height: 36px;
            background: #fff1f0;
            color: #f5222d;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-content p {
            margin: 0;
            font-size: 0.875rem;
        }

        .activity-time {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.25rem;
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 280px; /* Full width for mobile sidebar */
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 1000;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .sidebar-title, .nav-link span {
                display: block; /* Show text when sidebar is active */
            }
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
            }
            .menu-toggle {
                display: flex; /* Show menu toggle button */
            }
            .layout-grid {
                grid-template-columns: 1fr;
            }
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }
            .sidebar-overlay.active {
                opacity: 1;
                visibility: visible;
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .header-top {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.5rem;
            }
            .date-display {
                text-align: left !important;
            }
            .welcome-text h1 {
                font-size: 1.5rem;
            }
            .section-card {
                padding: 1rem;
            }
            .stat-card {
                padding: 1rem;
            }
            .stats-table {
                display: table;
                white-space: normal;
            }
            .stats-table th:nth-child(4), 
            .stats-table td:nth-child(4) { 
                display: none; 
            }
            .stats-table th, .stats-table td {
                padding: 0.75rem 0.5rem;
            }
            .activity-content {
                flex-grow: 1;
                min-width: 0;
            }
            .activity-content p {
                overflow-wrap: break-word;
                word-break: break-word;
                white-space: normal;
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
            <li class="nav-item">
                <a href="/index.php?action=admin" class="nav-link active">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/index.php?action=admin_config" class="nav-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    <span>Configuración</span>
                </a>
            </li>
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
                    Panel Administrativo
                </h1>
                <p>Monitoreo en tiempo real del Proceso <?php echo date('Y'); ?></p>
            </div>
            <div class="date-display" style="text-align: right;">
                <span style="font-weight: 600;"><?php echo date('d M, Y'); ?></span><br>
                <span style="color: #64748b; font-size: 0.875rem;">Última actualización: Ahora mismo</span>
            </div>
        </header>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17 11 19 13 23 9"/></svg>
                    </div>
                </div>
                <div class="stat-value"><?= $totalInscritos ?></div>
                <div class="stat-label">Total Inscritos</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #fef3c7; color: #d97706;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                </div>
                <div class="stat-value"><?= $this->procesoModel->isAbierto() ? 'Abierto' : 'Cerrado' ?></div>
                <div class="stat-label">Estado del Proceso</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="16" y1="11" x2="22" y2="11"/></svg>
                    </div>
                </div>
                <div class="stat-value"><?= $inscritosHoy ?></div>
                <div class="stat-label">Inscritos Hoy</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon" style="background: #edf2ff; color: #4361ee;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                </div>
                <div class="stat-value"><?= $diasRestantes ?></div>
                <div class="stat-label">Días Restantes</div>
            </div>
        </section>

        <div class="layout-grid">
            <section class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Inscritos por Grado</h2>
                </div>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Grado</th>
                            <th>Inscritos</th>
                            <th>Porcentaje</th>
                            <th>Visualización</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats as $row): ?>
                        <tr>
                            <td><span class="grade-badge"><?= htmlspecialchars($row['grado']) ?></span></td>
                            <td><strong><?= $row['total'] ?></strong></td>
                            <td><?= number_format(($row['total'] / max(1, $totalInscritos)) * 100, 1) ?>%</td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?= ($row['total'] / max(1, $totalInscritos)) * 100 ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($stats)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #94a3b8; padding: 2rem;">No hay registros todavía.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <section class="section-card">
                <div class="section-header">
                    <h2 class="section-title">Actividad Reciente</h2>
                </div>
                <div class="activity-list">
                    <?php foreach ($recentExclusions as $excl): ?>
                    <div class="activity-item">
                        <div class="activity-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </div>
                        <div class="activity-content">
                            <p><strong>Exclusión:</strong> Cod. <?= $excl['codigo'] ?></p>
                            <p style="color: #64748b; font-size: 0.8125rem;"><?= htmlspecialchars($excl['razon']) ?></p>
                            <div class="activity-time"><?= date('H:i - d/m/Y', strtotime($excl['fecha'])) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recentExclusions)): ?>
                    <p style="text-align: center; color: #94a3b8; padding: 1rem;">Sin exclusiones recientes.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>
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

        // Cerrar al hacer click en un enlace si estamos en móvil
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
