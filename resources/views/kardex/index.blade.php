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
    /* ══════════════════════════════════════════
       FILTER PANEL
    ══════════════════════════════════════════ */
    .filter-panel {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 22px 26px;
        margin-bottom: 24px;
    }
    .filter-panel-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--border);
    }
    .filter-panel-title {
        font-family: 'Syne', sans-serif;
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text);
        display: flex; align-items: center; gap: 8px;
    }

    /* Sección de fila de filtros */
    .filter-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
        align-items: start;
    }
    @media (max-width: 1100px) {
        .filter-row { grid-template-columns: 1fr 1fr; }
    }

    /* Separador de secciones dentro del panel */
    .filter-section-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        margin-bottom: 10px;
        display: flex; align-items: center; gap: 8px;
    }
    .filter-section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    .form-group { display: flex; flex-direction: column; }

    /* ── Radio buttons "Consultar por" ── */
    .consultar-por-box {
        background: var(--bg3);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 14px 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .radio-option {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }
    .radio-option input[type="radio"] {
        appearance: none;
        -webkit-appearance: none;
        width: 16px; height: 16px;
        border: 2px solid var(--border);
        border-radius: 50%;
        background: var(--bg2);
        cursor: pointer;
        flex-shrink: 0;
        transition: all .18s ease;
        position: relative;
    }
    .radio-option input[type="radio"]:checked {
        border-color: var(--accent);
        background: var(--accent);
        box-shadow: inset 0 0 0 3px var(--bg3);
    }
    .radio-label {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-muted);
        cursor: pointer;
        transition: color .18s;
        white-space: nowrap;
    }
    .radio-option:has(input:checked) .radio-label {
        color: var(--text);
    }
    /* sub-controles al lado del radio */
    .radio-sub {
        display: flex; align-items: center; gap: 6px;
        flex: 1;
    }
    .radio-sub select, .radio-sub input {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: 6px;
        padding: 5px 8px;
        color: var(--text);
        font-family: 'DM Sans', sans-serif;
        font-size: 12px;
        outline: none;
        transition: border-color .18s;
        cursor: pointer;
    }
    .radio-sub select:focus, .radio-sub input:focus {
        border-color: var(--accent);
    }
    .radio-sub select:disabled, .radio-sub input:disabled {
        opacity: .35;
        cursor: not-allowed;
    }
    .radio-sub select { min-width: 70px; }

    /* ── Bloque de fechas desde/hasta ── */
    .dates-block {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    /* ── Estrategia selector ── */
    .estrategia-select-wrap {
        position: relative;
    }
    .estrategia-tag {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 2px 8px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 600;
        margin-left: 6px;
        vertical-align: middle;
    }
    .est-tag-traditional { background: rgba(107,122,153,.15); color: var(--text-muted); }
    .est-tag-indexed      { background: rgba(79,142,247,.15);  color: var(--accent); }
    .est-tag-cache        { background: rgba(56,217,169,.15);  color: var(--accent2); }
    .est-tag-materialized { background: rgba(247,201,79,.15);  color: var(--accent3); }
    .est-tag-partitioned  { background: rgba(167,92,247,.15);  color: #c4b5fd; }

    .estrategia-hint {
        margin-top: 6px;
        font-size: 11px;
        color: var(--text-muted);
        line-height: 1.4;
        min-height: 28px;
    }

    /* ── Botones de acción ── */
    .filter-actions {
        display: flex; gap: 10px; align-items: center;
        padding-top: 16px;
        border-top: 1px solid var(--border);
        margin-top: 4px;
    }

    /* ══════════════════════════════════════════
       SUMMARY PILLS
    ══════════════════════════════════════════ */
    .summary-strip {
        display: flex;
        gap: 14px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .summary-pill {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 10px 18px;
        display: flex; flex-direction: column; gap: 3px;
        min-width: 145px;
    }
    .pill-label {
        font-size: 10.5px;
        text-transform: uppercase;
        letter-spacing: .7px;
        font-weight: 600;
        color: var(--text-muted);
    }
    .pill-value {
        font-family: 'Syne', sans-serif;
        font-size: 17px;
        font-weight: 700;
    }
    .pill-value.green  { color: var(--accent2); }
    .pill-value.red    { color: #f87171; }
    .pill-value.yellow { color: var(--accent3); }
    .pill-value.blue   { color: var(--accent); }
    .pill-value.purple { color: #c4b5fd; }

    /* ══════════════════════════════════════════
       TABLE
    ══════════════════════════════════════════ */
    .table-wrapper {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }
    .table-header {
        padding: 16px 22px;
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px;
    }
    .table-title {
        font-family: 'Syne', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: var(--text);
    }
    .search-input-wrap {
        position: relative; display: flex; align-items: center;
    }
    .search-input-wrap svg {
        position: absolute; left: 10px;
        width: 14px; height: 14px;
        color: var(--text-muted);
        pointer-events: none;
    }
    .search-input-wrap input {
        background: var(--bg3);
        border: 1px solid var(--border);
        border-radius: 7px;
        padding: 8px 12px 8px 32px;
        color: var(--text);
        font-family: 'DM Sans', sans-serif;
        font-size: 12.5px;
        width: 210px;
        outline: none;
        transition: border-color var(--transition);
    }
    .search-input-wrap input:focus { border-color: var(--accent); }

    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead th {
        background: var(--bg3);
        padding: 11px 16px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
        transition: color var(--transition);
    }
    thead th:hover { color: var(--text); }
    thead th .sort-icon { margin-left: 4px; opacity: .5; font-style: normal; }

    tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background var(--transition);
    }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: rgba(255,255,255,.025); }
    tbody td { padding: 11px 16px; color: var(--text); vertical-align: middle; }
    td.muted { color: var(--text-muted); font-size: 12px; }

    .badge { display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
    .badge-entrada  { background: rgba(56,217,169,.13); color: var(--accent2); border: 1px solid rgba(56,217,169,.25); }
    .badge-salida   { background: rgba(248,113,113,.13); color: #f87171;       border: 1px solid rgba(248,113,113,.25); }
    .badge-ajuste   { background: rgba(247,201,79,.13);  color: var(--accent3); border: 1px solid rgba(247,201,79,.25); }

    .num { font-family: 'Syne', sans-serif; font-weight: 600; }
    .num.pos { color: var(--accent2); }
    .num.neg { color: #f87171; }

    /* ── Pagination ── */
    .pagination {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 22px;
        border-top: 1px solid var(--border);
        font-size: 12.5px;
        color: var(--text-muted);
    }
    .page-btns { display: flex; gap: 4px; }
    .page-btn {
        width: 30px; height: 30px;
        border-radius: 7px;
        border: 1px solid var(--border);
        background: var(--bg3);
        color: var(--text-muted);
        font-size: 12px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        text-decoration: none;
        transition: all var(--transition);
    }
    .page-btn:hover { border-color: var(--accent); color: var(--accent); }
    .page-btn.active { background: var(--accent); border-color: var(--accent); color: #fff; }

    /* ── Empty state ── */
    .empty-state { padding: 60px 20px; text-align: center; color: var(--text-muted); }
    .empty-state .icon { font-size: 40px; margin-bottom: 12px; }
    .empty-state h3 { font-size: 15px; color: var(--text); margin-bottom: 6px; }
    .empty-state p  { font-size: 13px; }
</style>
@endpush

@section('content')

    <!-- ═══════════════ FILTER PANEL ═══════════════ -->
    <div class="filter-panel">
        <div class="filter-panel-header">
            <div class="filter-panel-title">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                Filtros de Búsqueda
            </div>
            <button type="button" class="btn btn-secondary" style="padding:6px 14px;font-size:12px;" onclick="limpiarFiltros()">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                Limpiar
            </button>
        </div>

        <form method="GET" action="{{ route('kardex.index') }}" id="filtros-form">

            {{-- ── Fila 1: Producto · Almacén · Tipo de Movimiento · Estrategia ── --}}
            <div class="filter-section-label">Parámetros de búsqueda</div>
            <div class="filter-row" style="margin-bottom:20px;">

                {{-- Producto --}}
                <div class="form-group">
                    <label for="producto_id">Tipo de Producto</label>
                    <select name="producto_id" id="producto_id" class="select">
                        <option value="">— Todos los productos —</option>
                        @foreach($productos ?? [] as $producto)
                            <option value="{{ $producto->id }}"
                                {{ request('producto_id') == $producto->id ? 'selected' : '' }}>
                                {{ $producto->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Almacén --}}
                <div class="form-group">
                    <label for="almacen_id">Almacén</label>
                    <select name="almacen_id" id="almacen_id" class="select">
                        <option value="">— Todos los almacenes —</option>
                        @foreach($almacenes ?? [] as $almacen)
                            <option value="{{ $almacen->id }}"
                                {{ request('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tipo de movimiento --}}
                <div class="form-group">
                    <label for="tipo_movimiento">Tipo de Movimiento</label>
                    <select name="tipo_movimiento" id="tipo_movimiento" class="select">
                        <option value="">— Todos —</option>
                        <option value="entrada" {{ request('tipo_movimiento') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                        <option value="salida"  {{ request('tipo_movimiento') == 'salida'  ? 'selected' : '' }}>Salida</option>
                        <option value="ajuste"  {{ request('tipo_movimiento') == 'ajuste'  ? 'selected' : '' }}>Ajuste</option>
                    </select>
                </div>

                {{-- Estrategia de búsqueda --}}
                <div class="form-group">
                    <label for="estrategia">Estrategia de Búsqueda</label>
                    <div class="estrategia-select-wrap">
                        <select name="estrategia" id="estrategia" class="select" onchange="actualizarHintEstrategia(this.value)">
                            <option value="tradicional" {{ request('estrategia','tradicional') == 'tradicional' ? 'selected' : '' }}>
                                Tradicional (sin índices)
                            </option>
                            <option value="indexada" {{ request('estrategia') == 'indexada' ? 'selected' : '' }}>
                                Indexación B-Tree
                            </option>
                            <option value="cache" {{ request('estrategia') == 'cache' ? 'selected' : '' }}>
                                Caché (Redis)
                            </option>
                            <option value="materializada" {{ request('estrategia') == 'materializada' ? 'selected' : '' }}>
                                Vista Materializada
                            </option>
                            <option value="particionada" {{ request('estrategia') == 'particionada' ? 'selected' : '' }}>
                                Particionamiento
                            </option>
                        </select>
                        <div class="estrategia-hint" id="estrategia-hint">
                            Consulta estándar sin optimizaciones adicionales.
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Fila 2: Consultar por · (fechas dependientes) ── --}}
            <div class="filter-section-label">Rango temporal</div>
            <div style="display:grid; grid-template-columns: auto 1fr; gap: 20px; align-items: start; margin-bottom: 4px;">

                {{-- Columna izquierda: radio buttons "Consultar por" --}}
                <div class="form-group" style="min-width:200px;">
                    <label>Consultar por</label>
                    <div class="consultar-por-box">

                        {{-- Opción: Fechas --}}
                        <div class="radio-option">
                            <input type="radio" name="consultar_por" id="cp_fechas" value="fechas"
                                {{ request('consultar_por', 'fechas') == 'fechas' ? 'checked' : '' }}
                                onchange="toggleConsultarPor('fechas')">
                            <label class="radio-label" for="cp_fechas">Rango de fechas</label>
                        </div>

                        {{-- Opción: Mes --}}
                        <div class="radio-option">
                            <input type="radio" name="consultar_por" id="cp_mes" value="mes"
                                {{ request('consultar_por') == 'mes' ? 'checked' : '' }}
                                onchange="toggleConsultarPor('mes')">
                            <label class="radio-label" for="cp_mes">Mes</label>
                            <div class="radio-sub">
                                <select name="mes" id="sel_mes"
                                    {{ request('consultar_por') != 'mes' ? 'disabled' : '' }}>
                                    <option value="01" {{ request('mes') == '01' ? 'selected' : '' }}>Enero</option>
                                    <option value="02" {{ request('mes') == '02' ? 'selected' : '' }}>Febrero</option>
                                    <option value="03" {{ request('mes') == '03' ? 'selected' : '' }}>Marzo</option>
                                    <option value="04" {{ request('mes') == '04' ? 'selected' : '' }}>Abril</option>
                                    <option value="05" {{ request('mes') == '05' ? 'selected' : '' }}>Mayo</option>
                                    <option value="06" {{ request('mes') == '06' ? 'selected' : '' }}>Junio</option>
                                    <option value="07" {{ request('mes') == '07' ? 'selected' : '' }}>Julio</option>
                                    <option value="08" {{ request('mes') == '08' ? 'selected' : '' }}>Agosto</option>
                                    <option value="09" {{ request('mes') == '09' ? 'selected' : '' }}>Septiembre</option>
                                    <option value="10" {{ request('mes') == '10' ? 'selected' : '' }}>Octubre</option>
                                    <option value="11" {{ request('mes') == '11' ? 'selected' : '' }}>Noviembre</option>
                                    <option value="12" {{ request('mes') == '12' ? 'selected' : '' }}>Diciembre</option>
                                </select>
                                <select name="anio_mes" id="sel_anio_mes"
                                    {{ request('consultar_por') != 'mes' ? 'disabled' : '' }}>
                                    @for($y = date('Y'); $y >= 2018; $y--)
                                        <option value="{{ $y }}" {{ request('anio_mes', date('Y')) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- Opción: Año --}}
                        <div class="radio-option">
                            <input type="radio" name="consultar_por" id="cp_anio" value="anio"
                                {{ request('consultar_por') == 'anio' ? 'checked' : '' }}
                                onchange="toggleConsultarPor('anio')">
                            <label class="radio-label" for="cp_anio">Año</label>
                            <div class="radio-sub">
                                <select name="anio" id="sel_anio"
                                    {{ request('consultar_por') != 'anio' ? 'disabled' : '' }}>
                                    @for($y = date('Y'); $y >= 2018; $y--)
                                        <option value="{{ $y }}" {{ request('anio', date('Y')) == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Columna derecha: campos de fecha desde/hasta (visibles solo en modo "fechas") --}}
                <div id="bloque_fechas" style="{{ request('consultar_por','fechas') != 'fechas' ? 'opacity:.3;pointer-events:none;' : '' }}">
                    <label>Rango de fechas</label>
                    <div class="dates-block">
                        <div class="form-group">
                            <label for="fecha_inicio" style="font-size:11px;margin-bottom:4px;">Desde</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="input"
                                   value="{{ request('fecha_inicio') }}"
                                   {{ request('consultar_por','fechas') != 'fechas' ? 'disabled' : '' }}>
                        </div>
                        <div class="form-group">
                            <label for="fecha_fin" style="font-size:11px;margin-bottom:4px;">Hasta</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="input"
                                   value="{{ request('fecha_fin') }}"
                                   {{ request('consultar_por','fechas') != 'fechas' ? 'disabled' : '' }}>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Acciones ── --}}
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Buscar
                </button>
                <span style="font-size:12px;color:var(--text-muted);">
                    Selecciona los filtros y presiona Buscar para consultar el inventario.
                </span>
            </div>

        </form>
    </div>

    <!-- ═══════════════ SUMMARY PILLS ═══════════════ -->
    @if(isset($kardex) && $kardex->count() > 0)
    <div class="summary-strip">
        <div class="summary-pill">
            <span class="pill-label">Total registros</span>
            <span class="pill-value blue">{{ $kardex->total() ?? $kardex->count() }}</span>
        </div>
        <div class="summary-pill">
            <span class="pill-label">Tiempo de respuesta</span>
            <span class="pill-value green">{{ $tiempoRespuesta ?? '—' }}</span>
        </div>
        <div class="summary-pill">
            <span class="pill-label">Fecha de consulta</span>
            <span class="pill-value yellow">{{ $fechaConsulta ?? now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="summary-pill">
            <span class="pill-label">Estrategia utilizada</span>
            <span class="pill-value purple">{{ $estrategiaUsada ?? ucfirst(request('estrategia','Tradicional')) }}</span>
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

        @if(isset($kardex) && $kardex->count() > 0)
        <div style="overflow-x:auto;">
            <table id="kardex-table">
                <thead>
                    <tr>
                        <th onclick="sortTable(0)">Fecha y Hora <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(1)">Documento <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(2)">Producto <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(3)">Almacén <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(4)">Tipo de Movimiento <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(5)" style="text-align:right;">Costo Unitario <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(6)" style="text-align:right;">Cantidad <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(7)" style="text-align:right;">Valor Total <span class="sort-icon">⇅</span></th>
                        <th onclick="sortTable(8)" style="text-align:right;">Stock Calculado <span class="sort-icon">⇅</span></th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kardex as $movimiento)
                    <tr>
                        <td class="muted">{{ \Carbon\Carbon::parse($movimiento->fecha)->format('d/m/Y') }}</td>
                        <td class="muted">{{ $movimiento->comprobante ?? '—' }}</td>
                        <td>{{ $movimiento->producto->nombre ?? '—' }}</td>
                        <td class="muted">{{ $movimiento->almacen->nombre ?? '—' }}</td>
                        <td>
                            @if($movimiento->tipo == 'entrada')
                                <span class="badge badge-entrada">Entrada</span>
                            @elseif($movimiento->tipo == 'salida')
                                <span class="badge badge-salida">Salida</span>
                            @else
                                <span class="badge badge-ajuste">Ajuste</span>
                            @endif
                        </td>
                        <td style="text-align:right;" class="muted">
                            S/ {{ number_format($movimiento->costo_unitario ?? 0, 2) }}
                        </td>
                        <td style="text-align:right;">
                            @if($movimiento->tipo == 'entrada')
                                <span class="num pos">+{{ number_format($movimiento->cantidad, 2) }}</span>
                            @elseif($movimiento->tipo == 'salida')
                                <span class="num neg">-{{ number_format($movimiento->cantidad, 2) }}</span>
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
                        <td class="muted">{{ $movimiento->observacion ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <span>
                Mostrando {{ $kardex->firstItem() ?? 1 }}–{{ $kardex->lastItem() ?? $kardex->count() }}
                de {{ $kardex->total() ?? $kardex->count() }} registros
            </span>
            @if(method_exists($kardex, 'links'))
                <div class="page-btns">
                    {!! $kardex->appends(request()->query())->links('pagination.kardex') !!}
                </div>
            @endif
        </div>

        @else
        <div class="empty-state">
            <div class="icon">🗂️</div>
            <h3>Sin resultados</h3>
            <p>Aplica filtros y presiona <strong>Buscar</strong> para consultar el kardex.</p>
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
    });

    /* ══════════════════════════════════════════════
       LIMPIAR FILTROS
    ══════════════════════════════════════════════ */
    function limpiarFiltros() {
        // Selects y dates
        document.querySelectorAll('#filtros-form select').forEach(el => {
            el.selectedIndex = 0;
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
</script>
@endpush