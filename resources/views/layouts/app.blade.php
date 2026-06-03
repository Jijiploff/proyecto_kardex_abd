<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema Kardex') — KardexPro</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ══════════════════════════════════════════
           RESET & BASE
        ══════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:          #0e1117;
            --bg2:         #161b27;
            --bg3:         #1e2436;
            --border:      #252d42;
            --accent:      #4f8ef7;
            --accent2:     #38d9a9;
            --accent3:     #f7c94f;
            --text:        #e8ecf4;
            --text-muted:  #6b7a99;
            --sidebar-w:   240px;
            --radius:      12px;
            --transition:  0.22s cubic-bezier(.4,0,.2,1);
        }

        /* Light mode overrides (applied via `class="light"` on <html>) */
        .light {
            --bg:          #f6f8fa;
            --bg2:         #ffffff;
            --bg3:         #f1f5f9;
            --border:      #e6eef8;
            --accent:      #1e6fff;
            --accent2:     #059669;
            --accent3:     #f59e0b;
            --text:        #0b1220;
            --text-muted:  #6b7280;
            --sidebar-w:   240px;
            --radius:      10px;
            --transition:  0.18s cubic-bezier(.4,0,.2,1);
        }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow: hidden;
        }

        /* ══════════════════════════════════════════
           LAYOUT SHELL
        ══════════════════════════════════════════ */
        .shell {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ══════════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            background: var(--bg2);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 0;
            position: relative;
            z-index: 10;
            transition: width var(--transition);
        }

        /* Brand */
        .sidebar-brand {
            padding: 28px 24px 20px;
            border-bottom: 1px solid var(--border);
        }
        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--accent2));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .brand-text {
            font-family: 'Syne', sans-serif;
            font-size: 17px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.3px;
        }
        .brand-text span {
            color: var(--accent);
        }

        /* User card */
        .sidebar-user {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, #4f8ef7 0%, #7b5ea7 100%);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            flex-shrink: 0;
        }
        .user-info { overflow: hidden; }
        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-role {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 3px;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 10px 12px 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 14px;
            border-radius: 9px;
            text-decoration: none;
            color: var(--text-muted);
            font-size: 13.5px;
            font-weight: 500;
            transition: background var(--transition), color var(--transition);
            position: relative;
            cursor: pointer;
        }
        .nav-link:hover {
            background: var(--bg3);
            color: var(--text);
        }
        .nav-link.active {
            background: rgba(79,142,247,.13);
            color: var(--accent);
        }
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 60%;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }
        .nav-link .nav-icon {
            width: 20px; height: 20px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .nav-link svg { width: 18px; height: 18px; }

        /* Danger / logout */
        .nav-link.danger:hover {
            background: rgba(239,68,68,.1);
            color: #f87171;
        }

        /* Bottom area */
        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid var(--border);
        }

        /* ══════════════════════════════════════════
           MAIN CONTENT
        ══════════════════════════════════════════ */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: var(--bg);
        }

        /* Topbar */
        .topbar {
            height: 60px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 28px;
            background: var(--bg2);
            gap: 12px;
        }
        .topbar-title {
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
        }
        .topbar-breadcrumb {
            font-size: 12px;
            color: var(--text-muted);
            display: flex; align-items: center; gap: 6px;
        }
        .topbar-right {
            margin-left: auto;
            display: flex; align-items: center; gap: 10px;
        }
        .topbar-badge {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(79,142,247,.15);
            color: var(--accent);
            border: 1px solid rgba(79,142,247,.25);
        }

        /* Page content */
        .page-content {
            flex: 1;
            overflow-y: auto;
            padding: 28px 32px;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }

        /* ══════════════════════════════════════════
           ANIMATIONS
        ══════════════════════════════════════════ */
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(14px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .page-content { animation: fadeUp .35s ease both; }

        /* ══════════════════════════════════════════
           UTILITIES
        ══════════════════════════════════════════ */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all var(--transition);
        }
        .btn-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 0 0 0 rgba(79,142,247,0);
        }
        .btn-primary:hover {
            background: #6ba3ff;
            box-shadow: 0 0 18px rgba(79,142,247,.35);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: var(--bg3);
            color: var(--text);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover {
            background: var(--border);
        }

        .card {
            background: var(--bg2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
        }

        .input, .select {
            background: var(--bg3);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 9px 14px;
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            width: 100%;
            transition: border-color var(--transition);
            outline: none;
        }
        .input:focus, .select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79,142,247,.12);
        }
        .input::placeholder { color: var(--text-muted); }
        .select option { background: var(--bg2); }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        /* Flash messages */
        .alert {
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }
        .alert-success { background: rgba(56,217,169,.1); border-color: rgba(56,217,169,.3); color: var(--accent2); }
        .alert-error   { background: rgba(239,68,68,.1);  border-color: rgba(239,68,68,.3);  color: #f87171; }
        .alert-info    { background: rgba(79,142,247,.1); border-color: rgba(79,142,247,.3); color: var(--accent); }
    </style>

    @stack('styles')
</head>
<body>
<div class="shell">

    <!-- ═══════════════ SIDEBAR ═══════════════ -->
    <aside class="sidebar">

        <!-- Brand -->
        <div class="sidebar-brand">
            <a href="{{ route('home') }}" class="brand-logo">
                <div class="brand-icon">📦</div>
                <span class="brand-text">Kardex<span>Pro</span></span>
            </a>
        </div>

        <!-- User card -->
        @php
            $authUser     = auth()->user();
            $userName     = $authUser->name     ?? 'Usuario';
            $userRole     = $authUser->role     ?? 'Administrador';
            $userInitials = strtoupper(substr($userName, 0, 2));
        @endphp
        <div class="sidebar-user">
            <div class="user-avatar">{{ $userInitials }}</div>
            <div class="user-info">
                <div class="user-name">{{ $userName }}</div>
                <div class="user-role">{{ $userRole }}</div>
            </div>
            <button id="theme-toggle" class="btn btn-secondary" title="Alternar tema" style="margin-left:auto;padding:6px 10px;font-size:13px">🌙</button>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <span class="nav-section-label">Principal</span>

            <a href="{{ route('home') }}"
               class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </span>
                Home
            </a>

            <span class="nav-section-label">Inventario</span>

            <a href="{{ route('kardex.index') }}"
               class="nav-link {{ request()->routeIs('kardex.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
                        <path d="M6 8h.01M10 8h4M6 12h12"/>
                    </svg>
                </span>
                Consulta del Kardex
            </a>

            <span class="nav-section-label">Rendimiento</span>

            <a href="{{ route('pruebas.index') }}"
               class="nav-link {{ request()->routeIs('pruebas.*') ? 'active' : '' }}">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                </span>
                Pruebas de Rendimiento
            </a>

        </nav>

    </aside>

    <!-- ═══════════════ MAIN ═══════════════ -->
    <div class="main">
        <!-- Topbar -->
        <header class="topbar">
            <div>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                <div class="topbar-breadcrumb">
                    <span>KardexPro</span>
                    <span>›</span>
                    <span>@yield('breadcrumb', 'Inicio')</span>
                </div>
            </div>
            <div class="topbar-right">
                @yield('topbar-actions')
                <span class="topbar-badge">v1.0</span>
            </div>
        </header>

        <!-- Page content -->
        <main class="page-content">
            @yield('content')
        </main>
    </div>

</div>
 
<script>
    (function(){
        const btn = document.getElementById('theme-toggle');
        if(!btn) return;

        function applyTheme(theme){
            if(theme === 'light'){
                document.documentElement.classList.add('light');
                btn.textContent = '🌞';
            } else {
                document.documentElement.classList.remove('light');
                btn.textContent = '🌙';
            }
            try { localStorage.setItem('kardex-theme', theme); } catch(e){}
        }

        btn.addEventListener('click', function(){
            const current = localStorage.getItem('kardex-theme') || 'dark';
            applyTheme(current === 'dark' ? 'light' : 'dark');
        });

        const stored = localStorage.getItem('kardex-theme');
        if(stored) applyTheme(stored);
    })();
</script>

@stack('scripts')
</body>
</html>