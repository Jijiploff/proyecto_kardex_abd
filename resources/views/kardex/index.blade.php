@extends('layouts.app')

@section('title', 'Consulta Kardex — KardexPro')
@section('page-title', 'Consulta del Kardex')
@section('breadcrumb', 'Kardex')

@section('topbar-actions')
    <button class="btn btn-secondary" onclick="exportarKardex()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Exportar
    </button>
@endsection

@push('styles')
<style>
    .filter-panel {
        background: linear-gradient(180deg, rgba(19, 28, 46, 0.96), rgba(13, 19, 33, 0.95));
        border: 1px solid var(--border);
        border-radius: 22px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
    }

    .filter-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--accent), var(--accent4), var(--accent2));
    }

    .filter-panel-header,
    .table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--border);
    }

    .filter-panel-title,
    .table-title {
        font-family: 'Rajdhani', sans-serif;
        font-size: 20px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .filter-grid-main {
        display: grid;
        grid-template-columns: 1.45fr 1fr;
        gap: 22px;
        margin-bottom: 22px;
    }

    .filter-section {
        background: rgba(255, 255, 255, 0.015);
        padding: 22px;
        border-radius: 18px;
        border: 1px solid var(--border);
        transition: transform var(--transition), border-color var(--transition), box-shadow var(--transition);
    }

    .filter-section:hover,
    .filter-section:focus-within {
        transform: translateY(-1px);
        border-color: rgba(79, 163, 255, 0.32);
        box-shadow: 0 0 0 4px rgba(79, 163, 255, 0.05);
    }

    .filter-section-label {
        font-family: 'Rajdhani', sans-serif;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: var(--accent2);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: linear-gradient(90deg, rgba(79, 163, 255, 0.4), transparent);
    }

    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .form-group label { font-size: 12px; font-weight: 700; color: var(--text); }

    .consultar-por-box {
        background: transparent;
        border: none;
        padding: 0;
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .radio-option {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border);
        padding: 13px 15px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: transform var(--transition), border-color var(--transition), box-shadow var(--transition), background var(--transition);
        cursor: pointer;
    }

    .radio-option:hover {
        border-color: rgba(79, 163, 255, 0.35);
        background: rgba(79, 163, 255, 0.04);
        transform: translateY(-1px);
    }

    .radio-option:has(input:checked) {
        border-color: rgba(79, 163, 255, 0.55);
        background: rgba(79, 163, 255, 0.06);
        box-shadow: 0 0 0 4px rgba(79, 163, 255, 0.06);
    }

    .radio-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
    }

    .radio-sub { display: flex; gap: 8px; flex: 1; }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 30px;
    }

    .metric-card {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 24px;
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
    }

    .metric-card::after {
        content: '';
        position: absolute;
        inset: auto -20% -55% -20%;
        height: 120px;
        background: radial-gradient(circle, rgba(79, 163, 255, 0.18), transparent 70%);
        pointer-events: none;
        filter: blur(8px);
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg), 0 0 0 1px rgba(79, 163, 255, 0.12);
    }

    .metric-icon-wrap {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.05);
    }

    .metric-content { display: flex; flex-direction: column; gap: 6px; }
    .metric-value {
        font-family: 'Rajdhani', sans-serif;
        font-size: 30px;
        font-weight: 700;
        letter-spacing: 0.04em;
        color: var(--text);
    }

    .metric-label {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.14em;
    }

    .summary-strip {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px;
        margin-bottom: 28px;
    }

    .summary-pill {
        padding: 22px;
        position: relative;
        overflow: hidden;
        border-left: 4px solid transparent;
    }

    .pill-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 8px;
        letter-spacing: 0.12em;
    }

    .pill-value {
        font-family: 'Rajdhani', sans-serif;
        font-size: 30px;
        font-weight: 700;
        letter-spacing: 0.04em;
    }

    .table-wrapper {
        border-radius: 22px;
        overflow: hidden;
        margin-bottom: 36px;
    }

    .search-input-wrap { position: relative; }
    .search-input-wrap svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--text-muted);
    }
    .search-input-wrap input { padding-left: 38px; min-width: 260px; }

    table thead th {
        padding: 16px 24px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--text-muted);
        text-align: left;
        white-space: nowrap;
    }

    tbody td {
        padding: 18px 24px;
        font-size: 14px;
        border-bottom: 1px solid var(--border);
        color: var(--text);
        vertical-align: middle;
    }

    tbody tr:hover { background: rgba(79, 163, 255, 0.03); }

    .badge {
        padding: 6px 12px;
        border-radius: 999px;
        font-weight: 800;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .badge-entrada { background: rgba(53, 224, 184, 0.10); color: var(--accent2); border: 1px solid rgba(53, 224, 184, 0.2); }
    .badge-salida { background: rgba(248, 113, 113, 0.10); color: #ff8f8f; border: 1px solid rgba(248, 113, 113, 0.2); }
    .badge-ajuste { background: rgba(245, 200, 79, 0.10); color: var(--accent3); border: 1px solid rgba(245, 200, 79, 0.2); }

    .num { font-family: 'Rajdhani', sans-serif; font-weight: 700; letter-spacing: 0.03em; }
    .num.pos { color: var(--accent2); }
    .num.neg { color: #ff8f8f; }

    .pagination {
        padding: 22px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-top: 1px solid var(--border);
        background: rgba(255,255,255,0.01);
    }

    .page-btn:hover { border-color: rgba(79, 163, 255, 0.35); }

    .empty-state {
        padding: 82px 40px;
        text-align: center;
        background: rgba(255,255,255,0.01);
    }

    .empty-state .icon {
        font-size: 64px;
        margin-bottom: 24px;
        opacity: 0.95;
        filter: drop-shadow(0 0 16px rgba(245, 200, 79, 0.18));
        animation: floatY 4s ease-in-out infinite;
    }

    .table-header .btn,
    .filter-panel-header .btn {
        white-space: nowrap;
    }

    .sort-icon {
        color: var(--accent);
        opacity: 0.8;
        margin-left: 4px;
    }

    @keyframes floatY {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }

    @media (max-width: 1200px) {
        .filter-grid-main { grid-template-columns: 1fr; }
        .metrics-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 900px) {
        .summary-strip { grid-template-columns: 1fr; }
        .pagination { flex-direction: column; align-items: stretch; }
    }

    @media (max-width: 640px) {
        .metrics-grid { grid-template-columns: 1fr; }
        .filter-panel, .filter-section, .table-header, .pagination { padding-left: 18px; padding-right: 18px; }
        .search-input-wrap input { min-width: 100%; }
    }

    .light .filter-panel,
    .light .filter-section,
    .light .table-wrapper,
    .light .summary-pill,
    .light .metric-card,
    .light .empty-state {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(244, 248, 255, 0.98));
        border-color: rgba(57, 92, 160, 0.12);
        box-shadow: 0 12px 34px rgba(20, 39, 77, 0.08);
    }

    .light .filter-section:hover,
    .light .filter-section:focus-within,
    .light .metric-card:hover,
    .light .quick-card:hover {
        border-color: rgba(36, 107, 253, 0.24);
        box-shadow: 0 14px 30px rgba(36, 107, 253, 0.08);
    }

    .light .filter-panel-title,
    .light .table-title,
    .light .pill-value,
    .light .metric-value,
    .light .num,
    .light .empty-state h3 {
        color: var(--text);
    }

    .light .radio-option {
        background: rgba(36, 107, 253, 0.03);
        border-color: rgba(57, 92, 160, 0.12);
    }

    .light .radio-option:hover,
    .light .radio-option:has(input:checked) {
        background: rgba(36, 107, 253, 0.07);
        border-color: rgba(36, 107, 253, 0.24);
    }

    .light .results-table thead th {
        background: rgba(36, 107, 253, 0.04);
        color: var(--text);
    }

    .light .results-table tbody tr:nth-child(even) {
        background: rgba(36, 107, 253, 0.015);
    }

    .light .results-table tbody tr:hover {
        background: rgba(36, 107, 253, 0.06);
    }

    .light .badge-entrada { background: rgba(14, 166, 125, 0.10); color: var(--accent2); border-color: rgba(14, 166, 125, 0.20); }
    .light .badge-salida { background: rgba(220, 38, 38, 0.10); color: #c24141; border-color: rgba(220, 38, 38, 0.20); }
    .light .badge-ajuste { background: rgba(192, 138, 22, 0.10); color: var(--accent3); border-color: rgba(192, 138, 22, 0.20); }

    .indices-panel {
        margin-top: 14px;
        padding: 16px;
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(79, 163, 255, 0.06), rgba(166, 107, 255, 0.04));
        border: 1px solid rgba(79, 163, 255, 0.18);
    }

    .indices-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .indices-panel-title {
        font-family: 'Rajdhani', sans-serif;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: var(--text);
    }

    .indices-status-pill {
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        background: rgba(255,255,255,0.04);
        border: 1px solid var(--border);
        color: var(--text-muted);
        white-space: nowrap;
    }

    .indices-status-pill.ok {
        background: rgba(53, 224, 184, 0.12);
        border-color: rgba(53, 224, 184, 0.25);
        color: var(--accent2);
    }

    .indices-status-pill.bad {
        background: rgba(248, 113, 113, 0.12);
        border-color: rgba(248, 113, 113, 0.25);
        color: #ff8f8f;
    }

    .indices-status-copy {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.5;
        margin-bottom: 12px;
    }

    .indices-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .indices-hint {
        margin-top: 10px;
        font-size: 11px;
        line-height: 1.5;
        color: var(--text-muted);
    }

    .light .indices-panel {
        background: linear-gradient(180deg, rgba(36, 107, 253, 0.05), rgba(139, 92, 246, 0.03));
        border-color: rgba(36, 107, 253, 0.16);
    }

    .light .indices-status-pill {
        background: rgba(255,255,255,0.82);
        border-color: rgba(57, 92, 160, 0.14);
        color: var(--text-muted);
    }

    .light .indices-status-pill.ok {
        background: rgba(14, 166, 125, 0.10);
        color: var(--accent2);
    }

    .light .indices-status-pill.bad {
        background: rgba(220, 38, 38, 0.10);
        color: #c24141;
    }
</style>
@endpush

@section('content')

    <!-- ═══════════════ FILTER PANEL ═══════════════ -->
    <div class="filter-panel">
        <div class="filter-panel-header">
            <div class="filter-panel-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M21 4H3L10 12.46V19L14 21V12.46L21 4Z"/>
                </svg>
                Parámetros de Consulta
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    Reiniciar
                </button>
                <button type="submit" form="filtros-form" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    Ejecutar Búsqueda
                </button>
            </div>
        </div>

        <form method="GET" action="{{ route('kardex.index') }}" id="filtros-form">
            <div class="filter-grid-main">
                {{-- Sección Producto --}}
                <div class="filter-section">
                    <div class="filter-section-label">Identificación de Producto</div>
                    <div class="form-group">
                        <label for="producto">Seleccione un Producto</label>
                        <select name="producto" id="producto" class="select-buscador">
                            @if(isset($productoModel))
                                <option value="{{ $productoModel->Producto }}" selected>
                                    {{ $productoModel->Producto }} — {{ $productoModel->Descripcion }}
                                </option>
                            @endif
                        </select>
                        @error('producto')
                            <span style="color:var(--accent3);font-size:11px;margin-top:8px;display:flex;align-items:center;gap:4px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                {{-- Sección Estrategia --}}
                <div class="filter-section">
                    <div class="filter-section-label">Motor de Consulta</div>
                    <div class="form-group">
                        <label for="estrategia">Estrategia de Optimización</label>
                        <select name="estrategia" id="estrategia" class="select">
                            <option value="tradicional" {{ request('estrategia') == 'tradicional' ? 'selected' : '' }}>Tradicional</option>
                            <option value="optimizada" {{ request('estrategia') == 'optimizada' ? 'selected' : '' }}>Optimizada</option>
                            <option value="optimizada_indices" {{ request('estrategia') == 'optimizada_indices' ? 'selected' : '' }}>Optimizada + Índices</option>
                        </select>
                        <div id="panel-indices" class="indices-panel" style="display: {{ request('estrategia') == 'optimizada_indices' ? 'block' : 'none' }};">
                            <div class="indices-panel-head">
                                <div class="indices-panel-title">Estado de Índices</div>
                                <div id="indices-status-pill" class="indices-status-pill">Cargando…</div>
                            </div>
                            <div id="estado-indices" class="indices-status-copy">Verificando índices disponibles…</div>
                            <div class="indices-actions">
                                <button type="button" class="btn btn-primary" onclick="toggleIndices('crear')">⚡ Activar Índices</button>
                                <button type="button" class="btn btn-secondary" onclick="toggleIndices('eliminar')">🗑 Desactivar Índices</button>
                            </div>
                        </div>
                        <div class="indices-hint">Este panel crea o elimina índices reales en la base de datos antes de lanzar la consulta optimizada.</div>
                    </div>
                </div>
            </div>

            <div class="filter-section">
                <div class="filter-section-label">Configuración Temporal</div>
                <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 32px;">
                    <div class="consultar-por-box">
                        <label class="radio-option">
                            <input type="radio" name="consultar_por" id="cp_fechas" value="fechas"
                                {{ request('consultar_por', 'fechas') == 'fechas' ? 'checked' : '' }}
                                onchange="toggleConsultarPor('fechas')">
                            <span class="radio-label">Rango Personalizado</span>
                        </label>

                        <label class="radio-option">
                            <input type="radio" name="consultar_por" id="cp_mes" value="mes"
                                {{ request('consultar_por') == 'mes' ? 'checked' : '' }}
                                onchange="toggleConsultarPor('mes')">
                            <span class="radio-label">Consulta Mensual</span>
                            <div class="radio-sub">
                                <select name="mes" id="sel_mes" class="select" style="padding:4px 8px; width:120px;" {{ request('consultar_por') != 'mes' ? 'disabled' : '' }}>
                                    @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $idx => $m)
                                        <option value="{{ str_pad($idx+1, 2, '0', STR_PAD_LEFT) }}" {{ request('mes') == str_pad($idx+1, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                                <select name="anio_mes" id="sel_anio_mes" class="select" style="padding:4px 8px; width:90px;" {{ request('consultar_por') != 'mes' ? 'disabled' : '' }}>
                                    @for($y = date('Y'); $y >= 2000; $y--)
                                        <option value="{{ $y }}" {{ request('anio_mes', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </label>

                        <label class="radio-option">
                            <input type="radio" name="consultar_por" id="cp_anio" value="anio"
                                {{ request('consultar_por') == 'anio' ? 'checked' : '' }}
                                onchange="toggleConsultarPor('anio')">
                            <span class="radio-label">Consulta Anual</span>
                            <select name="anio" id="sel_anio" class="select" style="margin-left:auto; padding:4px 8px; width:100px;" {{ request('consultar_por') != 'anio' ? 'disabled' : '' }}>
                                @for($y = date('Y'); $y >= 2000; $y--)
                                    <option value="{{ $y }}" {{ request('anio', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </label>
                    </div>

                    <div id="bloque_fechas" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; {{ request('consultar_por','fechas') != 'fechas' ? 'opacity:.3;pointer-events:none;' : '' }}">
                        <div class="form-group">
                            <label>Fecha de Inicio</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="input" value="{{ request('fecha_inicio') }}">
                        </div>
                        <div class="form-group">
                            <label>Fecha de Término</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="input" value="{{ request('fecha_fin') }}">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- ═══════════════ SUMMARY STRIP ═══════════════ -->
    @if(isset($kardex) && $totalRegistros > 0)
    <div class="summary-strip">
        <div class="summary-pill" style="border-left: 5px solid var(--accent2);">
            <div class="pill-label">Entradas del Periodo</div>
            <div class="pill-value" style="color: var(--accent2);">{{ number_format($totalEntradas, 2) }}</div>
        </div>
        <div class="summary-pill" style="border-left: 5px solid #f87171;">
            <div class="pill-label">Salidas del Periodo</div>
            <div class="pill-value" style="color: #f87171;">{{ number_format($totalSalidas, 2) }}</div>
        </div>
        <div class="summary-pill" style="border-left: 5px solid var(--accent);">
            <div class="pill-label">Stock Acumulado Final</div>
            <div class="pill-value" style="color: var(--accent);">{{ number_format($stockActual, 2) }}</div>
        </div>
    </div>
    @endif

    <!-- ═══════════════ INFO METRICS ═══════════════ -->
    @if(isset($kardex) && $totalRegistros > 0)
    <div class="metrics-grid">
        {{-- Registros encontrados --}}
        <div class="metric-card">
            <div class="metric-icon-wrap" style="background: rgba(79, 142, 247, 0.12); color: var(--accent);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <div class="metric-content">
                <span class="metric-label">Registros encontrados</span>
                <span class="metric-value">{{ number_format($totalRegistros) }}</span>
            </div>
        </div>

        {{-- Tiempo de respuesta --}}
        <div class="metric-card">
            <div class="metric-icon-wrap" style="background: rgba(56, 217, 169, 0.12); color: var(--accent2);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="metric-content">
                <span class="metric-label">Tiempo de respuesta</span>
                <span class="metric-value">{{ $tiempoRespuesta }} segundos</span>
            </div>
        </div>

        {{-- Fecha de consulta --}}
        <div class="metric-card">
            <div class="metric-icon-wrap" style="background: rgba(79, 142, 247, 0.12); color: var(--accent);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="metric-content">
                <span class="metric-label">Fecha de consulta</span>
                <span class="metric-value">{{ $fechaConsulta }}</span>
            </div>
        </div>

        {{-- Estrategia utilizada --}}
        <div class="metric-card">
            <div class="metric-icon-wrap" style="background: rgba(247, 201, 79, 0.12); color: var(--accent3);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
            </div>
            <div class="metric-content">
                <span class="metric-label">Estrategia utilizada</span>
                <span class="metric-value">{{ $nombreEstrategiaDisplay ?? 'Tradicional' }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- ═══════════════ KARDEX TABLE ═══════════════ -->
    <div class="table-wrapper">
        <div class="table-header">
            <span class="table-title">Resultados de la consulta</span>
            <div class="search-input-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" placeholder="Buscar en resultados…" id="search-table">
            </div>
        </div>

        @if(isset($sinDatosParaPeriodo) && $sinDatosParaPeriodo)
        <div class="empty-state" style="padding: 40px 20px;">
            <div class="icon" style="color: var(--accent3); font-size: 48px; margin-bottom: 20px;">📅</div>
            <h3 style="font-family: 'Syne', sans-serif; font-size: 18px; color: var(--text); margin-bottom: 12px;">Sin registros para este periodo</h3>
            <p style="color: var(--text-muted); max-width: 500px; margin: 0 auto 24px; line-height: 1.6;">
                Actualmente no existen datos almacenados para el año <strong>{{ request('anio') ?? request('anio_mes') ?? 'seleccionado' }}</strong>. 
                Esto suele ocurrir en periodos futuros o muy recientes sin movimientos registrados.
            </p>
            <div style="background: rgba(247, 201, 79, 0.05); border: 1px solid rgba(247, 201, 79, 0.2); border-radius: 10px; padding: 16px; display: inline-block;">
                <span style="color: var(--accent3); font-weight: 600; font-size: 13px;">💡 Sugerencia:</span>
                <span style="color: var(--text-muted); font-size: 13px; margin-left: 5px;">Prueba consultando años anteriores (como 2023 o 2024) para visualizar información histórica.</span>
            </div>
        </div>
        @elseif(isset($kardex) && $kardex->total() > 0)
        <div style="overflow-x:auto;">
            <table id="kardex-table">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">Fecha y Hora <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(1)">Documento <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(2)">Producto <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(3)">Tipo de Movimiento <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(4)" style="text-align:right;">Costo Unitario <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(5)" style="text-align:right;">Cantidad <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(6)" style="text-align:right;">Valor Total <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(7)" style="text-align:right;">Stock Calculado <span class="sort-icon">⇅</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kardex as $movimiento)
                    <tr>
                        <td class="muted">{{ \Carbon\Carbon::parse($movimiento->fecha)->format('d/m/Y') }}</td>
                        <td class="muted">{{ $movimiento->comprobante ?? '—' }}</td>
                        <td>{{ $productoModel->Descripcion ?? '—' }}</td>
                        <td>
                            @if($movimiento->tipo == 'entrada')
                                <span class="badge badge-entrada">{{ $movimiento->tipo_nombre ?? 'Entrada' }}</span>
                            @elseif($movimiento->tipo == 'salida')
                                <span class="badge badge-salida">{{ $movimiento->tipo_nombre ?? 'Salida' }}</span>
                            @else
                                <span class="badge badge-ajuste">{{ $movimiento->tipo_nombre ?? 'Ajuste' }}</span>
                            @endif
                        </td>
                        <td style="text-align:right;" class="muted">
                            S/ {{ number_format($movimiento->costo_unitario ?? 0, 2) }}
                        </td>
                        <td style="text-align:right;">
                            @if($movimiento->tipo == 'entrada')
                                <span class="num pos">+{{ number_format(abs($movimiento->cantidad), 2) }}</span>
                            @elseif($movimiento->tipo == 'salida')
                                <span class="num neg">{{ number_format($movimiento->cantidad, 2) }}</span>
                            @else
                                <span class="num" style="color:var(--accent3);">{{ number_format($movimiento->cantidad, 2) }}</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <span class="num">S/ {{ number_format($movimiento->costo_total ?? 0, 2) }}</span>
                        </td>
                        <td style="text-align:right;">
                            <span class="num">{{ number_format($movimiento->saldo ?? 0, 2) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <span>
                Mostrando {{ $kardex->firstItem() ?? 1 }}–{{ $kardex->lastItem() ?? $kardex->count() }}
                de {{ $kardex->total() }} registros
            </span>
            @if(method_exists($kardex, 'links'))
                <div class="page-btns">
                    {!! $kardex->appends(request()->query())->links('pagination.kardex') !!}
                </div>
            @endif
        </div>

        @elseif(isset($kardex))
        <div class="empty-state">
            <div class="icon">🔍</div>
            <h3>Sin coincidencias</h3>
            <p>No se encontraron movimientos para los filtros aplicados. Intenta con otros parámetros.</p>
        </div>
        @else
        <div class="empty-state">
            <div class="icon">🗂️</div>
            <h3>Consulta el Inventario</h3>
            <p>Selecciona un producto y aplica filtros para visualizar el historial de movimientos.</p>
        </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
    /* ══════════════════════════════════════════════
       CONSULTAR POR — toggle de controles
    ══════════════════════════════════════════════ */
    function toggleConsultarPor(modo) {
        // Fechas: bloque visible, campos habilitados
        const bloqueFechas = document.getElementById('bloque_fechas');
        const fi = document.getElementById('fecha_inicio');
        const ff = document.getElementById('fecha_fin');

        // Mes
        const selMes     = document.getElementById('sel_mes');
        const selAnioMes = document.getElementById('sel_anio_mes');

        // Año
        const selAnio = document.getElementById('sel_anio');

        if (modo === 'fechas') {
            bloqueFechas.style.opacity = '1';
            bloqueFechas.style.pointerEvents = 'auto';
            fi.disabled = false;
            ff.disabled = false;
            selMes.disabled = true;
            selAnioMes.disabled = true;
            selAnio.disabled = true;
        } else if (modo === 'mes') {
            bloqueFechas.style.opacity = '.3';
            bloqueFechas.style.pointerEvents = 'none';
            fi.disabled = true;
            ff.disabled = true;
            selMes.disabled = false;
            selAnioMes.disabled = false;
            selAnio.disabled = true;
        } else if (modo === 'anio') {
            bloqueFechas.style.opacity = '.3';
            bloqueFechas.style.pointerEvents = 'none';
            fi.disabled = true;
            ff.disabled = true;
            selMes.disabled = true;
            selAnioMes.disabled = true;
            selAnio.disabled = false;
        }
    }

    /* ══════════════════════════════════════════════
       ESTRATEGIA — hints descriptivos
    ══════════════════════════════════════════════ */
    const estrategiaHints = {
        tradicional:  'Consulta estándar sin optimizaciones adicionales. Útil como referencia base.',
        indexada:     'Usa índices B-Tree compuestos en producto_id, almacen_id y fecha para mayor velocidad.',
        cache:        'Recupera resultados desde Redis (TTL 5 min). Ideal para consultas repetidas.',
        materializada:'Accede a una vista pre-calculada. Muy rápido; datos actualizados por job programado.',
        particionada: 'Consulta solo las particiones de tabla correspondientes al rango temporal dado.',
    };
    function actualizarHintEstrategia(val) {
        const hint = document.getElementById('estrategia-hint');
        if (hint) hint.textContent = estrategiaHints[val] ?? '';
    }
    // Inicializar hint al cargar
    document.addEventListener('DOMContentLoaded', () => {
        const sel = document.getElementById('estrategia');
        if (sel) actualizarHintEstrategia(sel.value);

        // Inicializar Tom Select para el buscador de productos con carga remota (AJAX)
        new TomSelect(".select-buscador", {
            valueField: 'Producto',
            labelField: 'Descripcion',
            searchField: ['Producto', 'Descripcion'],
            maxItems: 1,
            loadThrottle: 300,
            placeholder: "Escriba para buscar...",
            allowEmptyOption: true,
            load: function(query, callback) {
                if (!query.length) return callback();
                var url = '{{ route("api.productos.buscar") }}?q=' + encodeURIComponent(query);
                fetch(url)
                    .then(response => response.json())
                    .then(json => {
                        callback(json);
                    }).catch(() => {
                        callback();
                    });
            },
            render: {
                option: function(item, escape) {
                    return `<div class="py-2 px-3 border-b border-gray-800 hover:bg-gray-700">
                                <div class="font-bold text-sm text-blue-400">${escape(item.Producto)}</div>
                                <div class="text-xs opacity-75">${escape(item.Descripcion)}</div>
                            </div>`;
                },
                item: function(item, escape) {
                    return `<div class="flex items-center gap-2">
                                <span class="font-bold text-blue-400">${escape(item.Producto)}</span>
                                <span class="opacity-75">- ${escape(item.Descripcion)}</span>
                            </div>`;
                },
                no_results: function(data, escape) {
                    return '<div class="no-results py-2 px-3 text-xs text-muted">No se encontraron productos para "' + escape(data.input) + '"</div>';
                }
            }
        });
    });

    /* ══════════════════════════════════════════════
       LIMPIAR FILTROS
    ══════════════════════════════════════════════ */
    function limpiarFiltros() {
        // Selects y dates
        document.querySelectorAll('#filtros-form select').forEach(el => {
            if (el.tomselect) {
                el.tomselect.clear();
            } else {
                el.selectedIndex = 0;
            }
        });
        document.querySelectorAll('#filtros-form input[type="date"]').forEach(el => {
            el.value = '';
        });
        // Resetear radio a "fechas"
        const radioFechas = document.getElementById('cp_fechas');
        if (radioFechas) { radioFechas.checked = true; toggleConsultarPor('fechas'); }
        // Reset hint
        actualizarHintEstrategia('tradicional');
    }

    /* ══════════════════════════════════════════════
       BÚSQUEDA EN TABLA
    ══════════════════════════════════════════════ */
    document.getElementById('search-table')?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#kardex-table tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    /* ══════════════════════════════════════════════
       ORDENAR COLUMNAS
    ══════════════════════════════════════════════ */
    let sortDir = {};
    function sortTable(colIndex) {
        const table = document.getElementById('kardex-table');
        if (!table) return;
        const tbody = table.querySelector('tbody');
        const rows  = Array.from(tbody.querySelectorAll('tr'));
        sortDir[colIndex] = !sortDir[colIndex];
        rows.sort((a, b) => {
            const aText = a.cells[colIndex]?.textContent.trim() ?? '';
            const bText = b.cells[colIndex]?.textContent.trim() ?? '';
            const aNum  = parseFloat(aText.replace(/[^0-9.-]/g, ''));
            const bNum  = parseFloat(bText.replace(/[^0-9.-]/g, ''));
            if (!isNaN(aNum) && !isNaN(bNum))
                return sortDir[colIndex] ? aNum - bNum : bNum - aNum;
            return sortDir[colIndex]
                ? aText.localeCompare(bText, 'es')
                : bText.localeCompare(aText, 'es');
        });
        rows.forEach(r => tbody.appendChild(r));
    }

    /* ══════════════════════════════════════════════
       EXPORTAR
    ══════════════════════════════════════════════ */
    function exportarKardex() {
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'xlsx');
        window.location.href = '{{ route("kardex.index") }}?' + params.toString();
    }

    const estrategiaSelect = document.getElementById('estrategia');
    const panelIndices = document.getElementById('panel-indices');
    const estadoIndicesEl = document.getElementById('estado-indices');
    const statusPill = document.getElementById('indices-status-pill');

    function actualizarPanelIndices(indices) {
        if (!estadoIndicesEl || !statusPill || !indices) return;

        const activos = Boolean(indices.todos_activos);
        estadoIndicesEl.textContent = activos
            ? 'Índices activos. La consulta optimizada puede aprovecharlos de forma real.'
            : 'Índices inactivos. La estrategia optimizada seguirá funcionando, pero sin aceleración por índice.';

        statusPill.textContent = activos ? 'Activos' : 'Inactivos';
        statusPill.classList.toggle('ok', activos);
        statusPill.classList.toggle('bad', !activos);
    }

    async function cargarEstadoIndices() {
        if (!panelIndices || panelIndices.style.display === 'none') return;

        try {
            const response = await fetch('{{ route("kardex.indices.estado") }}', {
                headers: { 'Accept': 'application/json' },
            });
            const data = await response.json();

            if (data && data.motor && data.motor !== 'sqlsrv') {
                estadoIndicesEl.textContent = 'Administración de índices disponible solo para SQL Server. Motor actual: ' + data.motor + '.';
                statusPill.textContent = 'No disponible';
                statusPill.classList.remove('ok');
                statusPill.classList.add('bad');
                return;
            }

            actualizarPanelIndices(data);
        } catch (error) {
            estadoIndicesEl.textContent = 'No fue posible obtener el estado actual de los índices.';
            statusPill.textContent = 'Error';
            statusPill.classList.remove('ok');
            statusPill.classList.add('bad');
        }
    }

    async function toggleIndices(accion) {
        if (!panelIndices || panelIndices.style.display === 'none') return;

        const formData = new FormData();
        formData.append('accion', accion);

        try {
            const response = await fetch('{{ route("kardex.indices.toggle") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok || !data.ok) {
                estadoIndicesEl.textContent = data.error || 'No fue posible actualizar los índices.';
                statusPill.textContent = 'Error';
                statusPill.classList.remove('ok');
                statusPill.classList.add('bad');
                return;
            }

            actualizarPanelIndices(data.indices_activos);
            estadoIndicesEl.textContent = data.mensaje + '. La consulta se recargará para medir el impacto.';

            const params = new URLSearchParams(window.location.search);
            params.set('estrategia', 'optimizada_indices');
            window.location.href = '{{ route("kardex.index") }}?' + params.toString();
        } catch (error) {
            estadoIndicesEl.textContent = 'Error al intentar modificar los índices.';
            statusPill.textContent = 'Error';
            statusPill.classList.remove('ok');
            statusPill.classList.add('bad');
        }
    }

    estrategiaSelect?.addEventListener('change', () => {
        const mostrarPanel = estrategiaSelect.value === 'optimizada_indices';
        if (panelIndices) panelIndices.style.display = mostrarPanel ? 'block' : 'none';
        if (mostrarPanel) cargarEstadoIndices();
    });

    cargarEstadoIndices();
</script>
@endpush