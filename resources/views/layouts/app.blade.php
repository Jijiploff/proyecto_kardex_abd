<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema Kardex') — KardexPro</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <style>
        :root {
            --bg: #0a0e1a;
            --bg2: #11192b;
            --bg3: #182235;
            --surface: rgba(18, 25, 42, 0.92);
            --surface-strong: rgba(13, 19, 33, 0.96);
            --border: rgba(127, 162, 255, 0.15);
            --border-strong: rgba(95, 168, 255, 0.28);
            --accent: #4fa3ff;
            --accent-hover: #7bc2ff;
            --accent2: #35e0b8;
            --accent3: #f5c84f;
            --accent4: #a66bff;
            --text: #f5f8ff;
            --text-muted: #98a7c0;
            --shadow-sm: 0 10px 24px rgba(0, 0, 0, 0.16);
            --shadow-md: 0 16px 42px rgba(0, 0, 0, 0.26);
            --shadow-lg: 0 22px 60px rgba(0, 0, 0, 0.34);
            --shadow-glow: 0 0 0 1px rgba(79, 163, 255, 0.16), 0 0 28px rgba(79, 163, 255, 0.18);
            --radius: 18px;
            --sidebar-w: 270px;
            --transition: 220ms cubic-bezier(.2,.8,.2,1);
            --transition-slow: 380ms cubic-bezier(.2,.8,.2,1);
            color-scheme: dark;
        }

        .light {
            --bg: #edf3fb;
            --bg2: #ffffff;
            --bg3: #e9eff9;
            --surface: rgba(255, 255, 255, 0.9);
            --surface-strong: rgba(255, 255, 255, 0.98);
            --border: rgba(57, 92, 160, 0.14);
            --border-strong: rgba(57, 92, 160, 0.2);
            --accent: #246bfd;
            --accent-hover: #0f5df5;
            --accent2: #0ea67d;
            --accent3: #c08a16;
            --accent4: #8b5cf6;
            --text: #0b1220;
            --text-muted: #5c6984;
            color-scheme: light;
        }

        .light html,
        .light body {
            background: var(--bg);
            color: var(--text);
        }

        .light body::before {
            display: none;
        }

        .light body::after {
            background-image: linear-gradient(rgba(8, 20, 44, 0.06) 1px, transparent 1px), linear-gradient(90deg, rgba(8, 20, 44, 0.06) 1px, transparent 1px);
            opacity: .3;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            min-height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow: hidden;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        body { position: relative; }

        body::before,
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
        }

        body::before {
            display: none;
        }

        body::after {
            background-image: linear-gradient(rgba(255, 255, 255, 0.028) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.028) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: radial-gradient(circle at center, black 38%, transparent 92%);
            opacity: .55;
        }

        a { color: inherit; }
        button, input, select, textarea { font: inherit; }

        .shell {
            position: relative;
            z-index: 1;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            min-height: 0;
            background: var(--bg);
            position: relative;
        }

        .light .main {
            background: var(--bg);
        }

        .sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            background: rgba(13, 19, 33, 0.98);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 0;
            position: sticky;
            top: 0;
            height: 100vh;
            z-index: 100;
            box-shadow: 16px 0 48px rgba(0, 0, 0, 0.22);
            backdrop-filter: blur(18px);
        }

        .light .sidebar {
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            box-shadow: 16px 0 40px rgba(0, 0, 0, 0.02);
        }

        .sidebar-brand {
            padding: 26px 22px 20px;
            border-bottom: 1px solid var(--border);
        }

        .light .sidebar-brand {
            border-bottom: 1px solid #f1f5f9;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }

        .brand-logo:hover .brand-icon {
            transform: translateY(-1px) scale(1.03);
            box-shadow: 0 0 0 1px rgba(79, 163, 255, 0.2), 0 0 24px rgba(79, 163, 255, 0.35);
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            background: var(--accent);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.08), 0 10px 24px rgba(79, 163, 255, 0.32);
            transition: transform var(--transition), box-shadow var(--transition);
        }
        
        .light .brand-icon {
            background: #2563eb;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
        }

        .brand-text {
            font-family: 'Rajdhani', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }
        
        .light .brand-text {
            color: #0f172a;
        }

        .brand-text span { color: var(--accent2); }
        .light .brand-text span { color: #10b981; }

        .sidebar-user {
            margin: 18px 16px 6px;
            padding: 14px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border);
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-sm);
        }

        .light .sidebar-user {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            margin: 24px 20px 10px;
            padding: 14px 16px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            background: var(--accent2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #fff;
            font-size: 13px;
            flex-shrink: 0;
            box-shadow: 0 10px 20px rgba(53, 224, 184, 0.25);
        }
        
        .light .user-avatar {
            width: 42px;
            height: 42px;
            background: #10b981;
            font-size: 15px;
            box-shadow: none;
        }

        .user-info { overflow: hidden; display: flex; flex-direction: column; gap: 2px; }
        .user-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .light .user-name { color: #0f172a; font-weight: 700; font-size: 15px; }
        
        .user-role { font-size: 11px; color: var(--text-muted); font-weight: 500; }
        .light .user-role { color: #64748b; font-size: 13px; font-weight: 400; }
        
        .theme-btn {
            margin-left: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            width: 32px;
            height: 32px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .light .theme-btn {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            color: #0f172a;
        }

        .sidebar-nav {
            padding: 10px 16px 18px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-section-label {
            display: block;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
            padding: 24px 24px 10px;
            opacity: 0.75;
        }
        
        .light .nav-section-label {
            color: #94a3b8;
            font-size: 11px;
            opacity: 1;
            padding: 28px 24px 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            margin: 0 16px 6px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            transition: transform var(--transition), background var(--transition), color var(--transition), box-shadow var(--transition);
            border: 1px solid transparent;
        }

        .nav-link:hover {
            background: rgba(79, 163, 255, 0.08);
            color: var(--text);
            transform: translateX(5px);
            border-color: rgba(79, 163, 255, 0.14);
        }

        .light .nav-link {
            color: #64748b;
            font-size: 15px;
        }

        .light .nav-link:hover { 
            background: #f1f5f9; 
            color: #334155; 
            border-color: transparent;
        }

        .nav-link.active {
            background: rgba(79, 163, 255, 0.15);
            color: #fff;
            box-shadow: 0 10px 24px rgba(79, 163, 255, 0.22);
            border-color: rgba(79, 163, 255, 0.3);
        }
        
        .light .nav-link.active {
            background: #eff6ff;
            color: #0f172a;
            border-color: #bfdbfe;
            box-shadow: none;
        }
        
        .nav-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
        }

        .nav-link .nav-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .nav-link svg { width: 18px; height: 18px; }

        .topbar {
            height: 78px;
            padding: 0 34px 0 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(17, 25, 43, 0.94);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(20px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .light .topbar {
            background: rgba(255, 255, 255, 0.96);
            border-bottom-color: rgba(57, 92, 160, 0.10);
            box-shadow: 0 10px 26px rgba(20, 39, 77, 0.06);
        }

        .topbar-title {
            font-family: 'Rajdhani', sans-serif;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text);
        }

        .topbar-breadcrumb {
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: var(--text-muted);
            letter-spacing: 0.03em;
        }

        .topbar-breadcrumb span:nth-child(1) {
            color: var(--accent2);
            font-weight: 600;
        }

        .light .topbar-breadcrumb span:nth-child(1) {
            color: var(--accent);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 999px;
            background: rgba(79, 163, 255, 0.08);
            border: 1px solid rgba(79, 163, 255, 0.18);
            color: var(--text);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
        }

        .light .topbar-badge {
            background: rgba(36, 107, 253, 0.08);
            border-color: rgba(36, 107, 253, 0.16);
            color: var(--text);
        }

        .page-content {
            padding: 34px 40px 40px;
            max-width: 1680px;
            margin: 0 auto;
            width: 100%;
            animation: pageFadeIn 420ms ease both;
        }

        .btn,
        .page-btn,
        .ts-control,
        .input,
        .select,
        .card,
        .alert,
        .table-wrapper,
        .results-table-wrapper,
        .metric-card,
        .stat-card,
        .quick-card,
        .config-card,
        .filter-panel,
        .summary-pill,
        .initial-state {
            border-radius: var(--radius);
        }

        .btn {
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
            padding: 11px 18px;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: var(--text);
            box-shadow: var(--shadow-sm);
            transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition), background var(--transition), color var(--transition);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-glow);
        }

        .btn:active { transform: translateY(0); }

        .btn-primary {
            background: var(--accent);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.12);
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            box-shadow: 0 0 0 1px rgba(79, 163, 255, 0.24), 0 0 28px rgba(79, 163, 255, 0.36);
        }

        .btn-secondary {
            background: rgba(17, 25, 43, 0.98);
            color: var(--text);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            border-color: var(--border-strong);
            background: rgba(30, 43, 71, 0.98);
        }

        .light .btn-primary {
            color: #fff;
            box-shadow: 0 12px 24px rgba(36, 107, 253, 0.18);
        }

        .light .btn-secondary {
            background: rgba(255, 255, 255, 0.98);
            color: var(--text);
            border-color: rgba(57, 92, 160, 0.14);
        }

        .light .btn-secondary:hover {
            background: rgba(240, 245, 252, 0.98);
            border-color: rgba(36, 107, 253, 0.22);
        }

        .card,
        .table-wrapper,
        .results-table-wrapper,
        .config-card,
        .filter-panel,
        .metric-card,
        .stat-card,
        .quick-card,
        .summary-pill,
        .initial-state {
            background: rgba(13, 19, 33, 0.95);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(12px);
            animation: cardRise 520ms ease both;
        }

        .light .card,
        .light .table-wrapper,
        .light .results-table-wrapper,
        .light .config-card,
        .light .filter-panel,
        .light .metric-card,
        .light .stat-card,
        .light .quick-card,
        .light .summary-pill,
        .light .initial-state {
            background: rgba(255, 255, 255, 0.98);
            border-color: rgba(57, 92, 160, 0.12);
            box-shadow: 0 12px 34px rgba(20, 39, 77, 0.08);
        }

        .card,
        .filter-panel,
        .config-card,
        .results-table-wrapper,
        .table-wrapper {
            position: relative;
            overflow: hidden;
        }

        .card::before,
        .filter-panel::before,
        .config-card::before,
        .results-table-wrapper::before,
        .table-wrapper::before {
            display: none;
        }

        .light .card::before,
        .light .filter-panel::before,
        .light .config-card::before,
        .light .results-table-wrapper::before,
        .light .table-wrapper::before {
            opacity: .08;
        }

        .input,
        .select,
        .ts-control {
            width: 100%;
            background: #ffffff;
            border: 1px solid var(--border);
            color: #111827;
            padding: 12px 16px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02), 0 0 0 1px rgba(255, 255, 255, 0.01);
            transition: border-color var(--transition), box-shadow var(--transition), transform var(--transition), background var(--transition);
        }

        .light .input,
        .light .select,
        .light .ts-control {
            background: rgba(255, 255, 255, 0.98);
            color: var(--text);
            border-color: rgba(57, 92, 160, 0.14);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.88), 0 1px 2px rgba(15, 25, 45, 0.04);
        }

        .input:hover,
        .select:hover,
        .ts-control:hover {
            border-color: rgba(79, 163, 255, 0.25);
        }

        .light .input:hover,
        .light .select:hover,
        .light .ts-control:hover {
            border-color: rgba(36, 107, 253, 0.26);
        }

        .input:focus,
        .select:focus,
        .ts-control:focus,
        .ts-control.focus {
            outline: none;
            border-color: rgba(79, 163, 255, 0.7);
            box-shadow: 0 0 0 4px rgba(79, 163, 255, 0.12), 0 0 0 1px rgba(79, 163, 255, 0.25);
            transform: translateY(-1px);
        }

        .light .input:focus,
        .light .select:focus,
        .light .ts-control:focus,
        .light .ts-control.focus {
            box-shadow: 0 0 0 4px rgba(36, 107, 253, 0.10), 0 0 0 1px rgba(36, 107, 253, 0.20);
        }

        .input::placeholder { color: color-mix(in srgb, var(--text-muted) 72%, transparent); }
        .select option { background: var(--bg2); color: var(--text); }
        .light .select option { background: #ffffff; color: var(--text); }

        label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: var(--text);
        }

        thead th {
            background: rgba(255, 255, 255, 0.02);
            color: var(--text-muted);
            font-family: 'Rajdhani', sans-serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .light thead th {
            background: rgba(36, 107, 253, 0.04);
            color: var(--text);
        }

        tbody td { font-size: 14px; }

        tbody tr {
            transition: background var(--transition), transform var(--transition), box-shadow var(--transition);
        }

        tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.012);
        }

        .light tbody tr:nth-child(even) {
            background: rgba(36, 107, 253, 0.018);
        }

        tbody tr:hover {
            background: rgba(79, 163, 255, 0.05);
        }

        .light tbody tr:hover {
            background: rgba(36, 107, 253, 0.06);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .alert {
            padding: 14px 18px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            box-shadow: var(--shadow-sm);
            animation: alertPop 280ms ease both;
        }

        .alert-success { background: rgba(53, 224, 184, 0.08); border-color: rgba(53, 224, 184, 0.24); color: var(--accent2); }
        .alert-error { background: rgba(248, 113, 113, 0.08); border-color: rgba(248, 113, 113, 0.24); color: #ff9191; }
        .alert-info { background: rgba(79, 163, 255, 0.08); border-color: rgba(79, 163, 255, 0.24); color: var(--accent); }

        .light .alert-success { background: rgba(14, 166, 125, 0.08); border-color: rgba(14, 166, 125, 0.22); color: var(--accent2); }
        .light .alert-error { background: rgba(220, 38, 38, 0.08); border-color: rgba(220, 38, 38, 0.18); color: #c24141; }
        .light .alert-info { background: rgba(36, 107, 253, 0.08); border-color: rgba(36, 107, 253, 0.18); color: var(--accent); }

        .page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 14px;
            border: 1px solid var(--border);
            background: rgba(17, 25, 43, 0.98);
            color: var(--text);
            text-decoration: none;
            font-weight: 700;
            transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition), background var(--transition);
        }

        .page-btn:hover {
            border-color: rgba(79, 163, 255, 0.35);
            background: rgba(79, 163, 255, 0.18);
            box-shadow: var(--shadow-glow);
            transform: translateY(-1px);
        }

        .light .page-btn {
            background: rgba(255, 255, 255, 0.98);
            border-color: rgba(57, 92, 160, 0.14);
            color: var(--text);
        }

        .light .page-btn:hover {
            background: rgba(241, 246, 253, 0.98);
            border-color: rgba(36, 107, 253, 0.24);
        }

        .page-btn.active {
            background: var(--accent);
            color: #fff;
            border-color: transparent;
        }

        .light .page-btn.active {
            background: var(--accent);
            color: #fff;
        }

        .page-btn[disabled] {
            opacity: .35;
            cursor: not-allowed;
            box-shadow: none;
        }

        .page-btns {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ts-control {
            border-radius: 14px !important;
            min-height: 48px;
            padding: 8px 12px !important;
        }

        .ts-control input,
        .ts-control .item {
            color: #111827 !important;
        }

        .ts-control input::placeholder {
            color: #6b7280 !important;
            opacity: .7 !important;
        }

        .ts-dropdown {
            background: #ffffff !important;
            border: 1px solid var(--border) !important;
            color: #111827 !important;
            box-shadow: var(--shadow-lg) !important;
            backdrop-filter: blur(12px);
        }

        .ts-dropdown .active {
            background: rgba(36, 107, 253, 0.12) !important;
            color: #111827 !important;
        }

        .ts-dropdown .option { color: #111827 !important; }

        .ts-dropdown .no-results {
            padding: 10px 14px !important;
            color: #6b7280 !important;
            font-size: 12px !important;
        }

        .light .ts-control {
            background: #ffffff !important;
            border: 1px solid var(--border) !important;
            color: #111827 !important;
        }

        .light .ts-dropdown { background: #ffffff !important; }

        .light .ts-dropdown .option,
        .light .ts-dropdown .no-results {
            color: #111827 !important;
        }

        .light .ts-dropdown .active {
            background: rgba(36, 107, 253, 0.12) !important;
            color: #111827 !important;
        }

        #theme-toggle {
            margin-left: auto;
            padding: 8px 12px;
            font-size: 13px;
            border-radius: 12px;
            box-shadow: none;
        }

        #theme-toggle:hover {
            box-shadow: 0 0 0 1px rgba(79, 163, 255, 0.24), 0 0 22px rgba(79, 163, 255, 0.18);
        }

        ::selection {
            background: rgba(79, 163, 255, 0.28);
            color: #fff;
        }

        ::-webkit-scrollbar { width: 12px; height: 12px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(79, 163, 255, 0.42), rgba(166, 107, 255, 0.42));
            border-radius: 999px;
            border: 3px solid transparent;
            background-clip: content-box;
        }

        @keyframes pageFadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes cardRise {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes alertPop {
            from { opacity: 0; transform: translateY(-8px) scale(.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @media (max-width: 1200px) {
            .sidebar { width: 240px; min-width: 240px; }
            .page-content { padding: 28px 24px 32px; }
        }

        @media (max-width: 960px) {
            html, body { overflow: auto; overflow-x: hidden; }
            .shell { flex-direction: column; height: auto; min-height: 100vh; overflow: visible; }
            .sidebar {
                position: relative;
                width: 100%;
                min-width: 0;
                height: auto;
                box-shadow: none;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }
            .main { overflow: visible; min-height: auto; }
            .topbar { padding: 18px 20px; height: auto; gap: 18px; align-items: flex-start; flex-direction: column; }
            .page-content { padding: 20px; }
        }

        @media (max-width: 640px) {
            .brand-text { font-size: 18px; }
            .topbar-title { font-size: 20px; }
            .topbar-right { width: 100%; justify-content: space-between; }
            .topbar, .page-content { padding-left: 16px; padding-right: 16px; }
            .page-content { padding-top: 18px; padding-bottom: 24px; }
            .btn { width: 100%; justify-content: center; }
            .sidebar-brand { padding: 20px 16px 16px; }
            .sidebar-user { margin: 14px 12px 4px; }
            .sidebar-nav { padding: 8px 12px 16px; }
            .nav-link { padding: 12px 12px; }
        }
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
            <button id="theme-toggle" class="theme-btn" title="Alternar tema">🌙</button>
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
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if(session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif
            
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