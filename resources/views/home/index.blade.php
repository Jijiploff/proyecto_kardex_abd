@extends('layouts.app')

@section('title', 'Home — KardexPro')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Home')

@push('styles')
<style>
    .dashboard-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(280px, 0.9fr);
        gap: 18px;
        margin-bottom: 18px;
    }
    .hero-card {
        padding: 28px;
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
        min-height: 190px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 18px;
        background: linear-gradient(135deg, rgba(79, 163, 255, 0.08), rgba(53, 224, 184, 0.05));
        animation: riseIn 640ms cubic-bezier(.2,.8,.2,1) both;
    }
    .hero-card::before,
    .hero-card::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        pointer-events: none;
        filter: blur(2px);
    }
    .hero-card::before {
        width: 260px;
        height: 260px;
        right: -120px;
        top: -130px;
        background: radial-gradient(circle, rgba(79, 163, 255, 0.24), transparent 68%);
    }
    .hero-card::after {
        width: 180px;
        height: 180px;
        right: 26px;
        bottom: -90px;
        background: radial-gradient(circle, rgba(166, 107, 255, 0.18), transparent 70%);
    }
    .hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        width: fit-content;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: rgba(255, 255, 255, 0.03);
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
    }
    .hero-title {
        font-family: 'Rajdhani', sans-serif;
        font-size: clamp(28px, 4vw, 44px);
        line-height: 1;
        letter-spacing: .05em;
        font-weight: 700;
        color: var(--text);
        max-width: 11ch;
        position: relative;
        z-index: 1;
    }
    .hero-copy {
        max-width: 56ch;
        font-size: 14px;
        line-height: 1.7;
        color: var(--text-muted);
        position: relative;
        z-index: 1;
    }
    .hero-metrics {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        position: relative;
        z-index: 1;
    }
    .hero-metric {
        padding: 14px 16px;
        border-radius: 16px;
        background: rgba(8, 13, 24, 0.24);
        border: 1px solid rgba(255, 255, 255, 0.06);
        backdrop-filter: blur(10px);
    }
    .hero-metric-label {
        display: block;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .16em;
        color: var(--text-muted);
        margin-bottom: 8px;
    }
    .hero-metric-value {
        font-family: 'Rajdhani', sans-serif;
        font-size: 22px;
        line-height: 1;
        font-weight: 700;
        color: var(--text);
    }
    .hero-side {
        padding: 24px;
        border: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 16px;
        min-height: 190px;
        animation: riseIn 720ms cubic-bezier(.2,.8,.2,1) both;
        animation-delay: 80ms;
    }
    .hero-side .stat-card {
        padding: 18px;
        border-radius: 16px;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 30px;
    }
    .stat-card {
        border: 1px solid var(--border);
        padding: 24px 26px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        position: relative;
        overflow: hidden;
        transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition);
        min-height: 176px;
        animation: riseIn 700ms cubic-bezier(.2,.8,.2,1) both;
    }
    .stat-card:nth-child(1) { grid-column: span 4; animation-delay: 70ms; }
    .stat-card:nth-child(2) { grid-column: span 4; animation-delay: 130ms; }
    .stat-card:nth-child(3) { grid-column: span 4; animation-delay: 190ms; }
    .stat-card:nth-child(4) { grid-column: span 4; animation-delay: 250ms; }
    .stat-card:nth-child(5) { grid-column: span 4; animation-delay: 310ms; }
    .stat-card:nth-child(6) { grid-column: span 4; animation-delay: 370ms; }
    .stat-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(79, 163, 255, 0.12), transparent 42%);
        pointer-events: none;
    }
    .stat-card:hover {
        border-color: rgba(79, 163, 255, 0.35);
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    .stat-header {
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px;
    }
    .stat-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .14em;
        color: var(--text-muted);
    }
    .stat-icon {
        width: 50px; height: 50px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.04);
    }
    .stat-value {
        font-family: 'Rajdhani', sans-serif;
        font-size: clamp(34px, 4vw, 46px);
        font-weight: 700;
        letter-spacing: .04em;
        color: var(--text);
        line-height: 1;
        margin-top: auto;
    }
    .stat-delta {
        font-size: 12px;
        color: var(--accent2);
        font-weight: 600;
    }

    .section-title {
        font-family: 'Rajdhani', sans-serif;
        font-size: 20px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--text);
        margin-bottom: 14px;
    }

    .quick-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 16px;
    }
    .quick-card {
        padding: 24px 26px;
        text-decoration: none;
        display: flex; align-items: flex-start; gap: 16px;
        position: relative;
        overflow: hidden;
        transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition), background var(--transition);
        min-height: 128px;
        animation: riseIn 760ms cubic-bezier(.2,.8,.2,1) both;
        animation-delay: 440ms;
    }
    .quick-card:hover {
        border-color: rgba(79, 163, 255, 0.4);
        background: rgba(79, 163, 255, 0.05);
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    .quick-icon {
        width: 56px; height: 56px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .quick-body h3 {
        font-family: 'Rajdhani', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: .04em;
        margin-bottom: 4px;
    }
    .quick-body p {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .quick-card:nth-child(1) .quick-icon { box-shadow: 0 0 0 1px rgba(79, 163, 255, 0.08); }
    .quick-card:nth-child(2) .quick-icon { box-shadow: 0 0 0 1px rgba(53, 224, 184, 0.08); }

    @keyframes softPulse {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-2px); }
    }

    .stat-icon { animation: softPulse 3.8s ease-in-out infinite; }
    .stat-card:nth-child(odd) { animation-name: riseInLeft; }
    .stat-card:nth-child(even) { animation-name: riseInRight; }

    @keyframes riseIn {
        from { opacity: 0; transform: translateY(18px) scale(.985); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes riseInLeft {
        from { opacity: 0; transform: translateX(-12px) translateY(18px) scale(.985); }
        to { opacity: 1; transform: translateX(0) translateY(0) scale(1); }
    }
    @keyframes riseInRight {
        from { opacity: 0; transform: translateX(12px) translateY(18px) scale(.985); }
        to { opacity: 1; transform: translateX(0) translateY(0) scale(1); }
    }

    @media (max-width: 640px) {
        .dashboard-hero { grid-template-columns: 1fr; }
        .hero-card,
        .hero-side { padding: 22px; min-height: auto; }
        .hero-metrics { grid-template-columns: 1fr; }
        .stats-grid { grid-template-columns: 1fr; }
        .stat-card { grid-column: auto !important; min-height: auto; padding: 22px; }
        .stat-value { font-size: 34px; }
        .quick-links { grid-template-columns: 1fr; }
        .quick-card { min-height: auto; padding: 22px; }
    }

    @media (max-width: 960px) and (min-width: 641px) {
        .dashboard-hero { grid-template-columns: 1fr; }
        .stats-grid { grid-template-columns: repeat(6, minmax(0, 1fr)); }
        .stat-card:nth-child(1),
        .stat-card:nth-child(2),
        .stat-card:nth-child(3),
        .stat-card:nth-child(4),
        .stat-card:nth-child(5),
        .stat-card:nth-child(6) { grid-column: span 3; }
    }

    @media (prefers-reduced-motion: reduce) {
        .stat-card,
        .quick-card,
        .hero-card,
        .hero-side,
        .stat-icon {
            animation: none !important;
            transition: none !important;
        }
    }

    .light .stat-card,
    .light .quick-card,
    .light .card {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(244, 248, 255, 0.98));
        border-color: rgba(57, 92, 160, 0.12);
        box-shadow: 0 12px 34px rgba(20, 39, 77, 0.08);
    }

    .light .stat-card:hover,
    .light .quick-card:hover {
        border-color: rgba(36, 107, 253, 0.24);
        box-shadow: 0 14px 30px rgba(36, 107, 253, 0.08);
    }

    .light .section-title,
    .light .stat-value,
    .light .quick-body h3,
    .light .quick-body p {
        color: var(--text);
    }

    .light .stat-delta,
    .light .quick-body p {
        color: var(--text-muted);
    }

    .light .hero-card,
    .light .hero-side {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(244, 248, 255, 0.98));
        border-color: rgba(57, 92, 160, 0.12);
        box-shadow: 0 12px 34px rgba(20, 39, 77, 0.08);
    }

    .light .hero-kicker,
    .light .hero-metric {
        background: rgba(255, 255, 255, 0.86);
        border-color: rgba(57, 92, 160, 0.10);
    }
</style>
@endpush

@section('content')

    <div class="dashboard-hero">
        <div class="hero-card">
            <div class="hero-kicker">Vista general en tiempo real</div>
            <div>
                <div class="hero-title">Control visual del Kardex</div>
                <p class="hero-copy">
                    Un panel más amplio para leer de un vistazo el estado del inventario, los documentos y la actividad reciente sin sentir las tarjetas apretadas.
                </p>
            </div>
            <div class="hero-metrics">
                <div class="hero-metric">
                    <span class="hero-metric-label">Documentos</span>
                    <div class="hero-metric-value">{{ number_format($documentosEmitidos ?? 0) }}</div>
                </div>
                <div class="hero-metric">
                    <span class="hero-metric-label">Stock total</span>
                    <div class="hero-metric-value">{{ number_format($stockTotal ?? 0, 2) }}</div>
                </div>
                <div class="hero-metric">
                    <span class="hero-metric-label">Hoy</span>
                    <div class="hero-metric-value">{{ number_format($movimientosHoy ?? 0) }}</div>
                </div>
            </div>
        </div>

        <div class="hero-side card">
            <div class="stat-header">
                <span class="stat-label">Última actualización</span>
                <div class="stat-icon" style="background:rgba(79,142,247,.15);">⚡</div>
            </div>
            <div class="stat-value" style="font-size: clamp(30px, 3vw, 40px);">{{ $ultimaFechaMovimiento ? \Illuminate\Support\Carbon::parse($ultimaFechaMovimiento)->format('d/m/Y') : 'Sin datos' }}</div>
            <div class="stat-delta">Fecha del último documento encontrado en la base de datos</div>
            <div class="hero-copy" style="max-width:none; margin-top:auto;">
                Este bloque funciona como resumen rápido y mantiene el foco en la información más reciente del sistema.
            </div>
        </div>
    </div>

    <!-- Stats row -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Productos registrados</span>
                <div class="stat-icon" style="background:rgba(79,142,247,.15);">📦</div>
            </div>
            <div class="stat-value">{{ number_format($productosRegistrados ?? 0) }}</div>
            <div class="stat-delta">Catálogo real de productos</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Documentos emitidos</span>
                <div class="stat-icon" style="background:rgba(56,217,169,.15);">🧾</div>
            </div>
            <div class="stat-value">{{ number_format($documentosEmitidos ?? 0) }}</div>
            <div class="stat-delta">Movimientos fuente del kardex</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Movimientos registrados</span>
                <div class="stat-icon" style="background:rgba(247,201,79,.15);">📋</div>
            </div>
            <div class="stat-value">{{ number_format($movimientosRegistrados ?? 0) }}</div>
            <div class="stat-delta">Líneas en DETADOC</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Productos con stock</span>
                <div class="stat-icon" style="background:rgba(167,92,247,.15);">📈</div>
            </div>
            <div class="stat-value">{{ number_format($productosConStock ?? 0) }}</div>
            <div class="stat-delta">Inventario actualmente disponible</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Stock total</span>
                <div class="stat-icon" style="background:rgba(53,224,184,.15);">⚙️</div>
            </div>
            <div class="stat-value">{{ number_format($stockTotal ?? 0, 2) }}</div>
            <div class="stat-delta">Suma acumulada de StockAc</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Movimientos hoy</span>
                <div class="stat-icon" style="background:rgba(79,142,247,.15);">🕒</div>
            </div>
            <div class="stat-value">{{ number_format($movimientosHoy ?? 0) }}</div>
            <div class="stat-delta">
                {{ $ultimaFechaMovimiento ? 'Último movimiento: ' . \Illuminate\Support\Carbon::parse($ultimaFechaMovimiento)->format('d/m/Y') : 'Sin fecha registrada' }}
            </div>
        </div>
    </div>

    <!-- Quick access -->
    <div class="section-title">Acceso rápido</div>
    <div class="quick-links">
        <a href="{{ route('kardex.index') }}" class="quick-card">
            <div class="quick-icon" style="background:rgba(79,142,247,.12);">🗂️</div>
            <div class="quick-body">
                <h3>Consulta del Kardex</h3>
                <p>Visualiza el historial de movimientos de inventario por producto y almacén.</p>
            </div>
        </a>
    </div>

@endsection