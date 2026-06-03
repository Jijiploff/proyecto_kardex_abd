@extends('layouts.app')

@section('title', 'Pruebas de Rendimiento — KardexPro')
@section('page-title', 'Pruebas de Rendimiento')
@section('breadcrumb', 'Pruebas')

@push('styles')
<style>
    /* ── Config card ── */
    .config-card {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 26px 28px;
        margin-bottom: 26px;
    }
    .config-card-title {
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 20px;
        display: flex; align-items: center; gap: 9px;
    }
    .config-card-title svg { color: var(--accent3); }

    .config-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 18px;
        align-items: end;
        margin-bottom: 20px;
    }
    .form-group { display: flex; flex-direction: column; }

    .run-row {
        display: flex; align-items: center; gap: 14px;
        padding-top: 6px;
        border-top: 1px solid var(--border);
    }
    .run-row .hint {
        font-size: 12px;
        color: var(--text-muted);
    }

    .static-val {
        display: flex;
        align-items: center;
        gap: 9px;
        background: var(--bg3);
        border: 1px solid var(--border);
        border-radius: 7px;
        padding: 9px 13px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
        min-height: 38px;
        cursor: default;
        user-select: none;
    }
    .static-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
        background: var(--accent2);
    }
    .static-val.accent-blue  .static-dot { background: var(--accent); }
    .static-val.accent-yellow .static-dot { background: var(--accent3); }
    .static-val.accent-purple .static-dot { background: #c4b5fd; }

    /* ── Progress indicator ── */
    .progress-bar-wrap {
        display: none;
        margin-top: 16px;
        gap: 10px;
        align-items: center;
    }
    .progress-bar-wrap.visible { display: flex; }
    .progress-bar {
        flex: 1;
        height: 6px;
        background: var(--bg3);
        border-radius: 99px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--accent), var(--accent2));
        border-radius: 99px;
        width: 0;
        transition: width .4s ease;
        animation: shimmer 1.4s infinite;
    }
    @keyframes shimmer {
        0%   { opacity:1; }
        50%  { opacity:.7; }
        100% { opacity:1; }
    }
    .progress-label {
        font-size: 12px;
        color: var(--text-muted);
        white-space: nowrap;
    }

    /* ── Results section ── */
    .results-section { }
    .results-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 16px;
    }
    .results-title {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        display: flex; align-items: center; gap: 9px;
    }
    .results-meta {
        font-size: 12px;
        color: var(--text-muted);
        display: flex; align-items: center; gap: 14px;
    }
    .results-meta span {
        display: flex; align-items: center; gap: 5px;
    }

    /* ── Results table ── */
    .results-table-wrapper {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    table.results-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .results-table thead th {
        background: var(--bg3);
        padding: 12px 18px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }
    .results-table thead th:not(:first-child) { text-align: center; }

    .results-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background var(--transition);
    }
    .results-table tbody tr:last-child { border-bottom: none; }
    .results-table tbody tr:hover { background: rgba(255,255,255,.025); }

    .results-table tbody td {
        padding: 14px 18px;
        color: var(--text);
        vertical-align: middle;
    }
    .results-table tbody td:not(:first-child):not(:nth-child(2)) {
        text-align: center;
    }

    /* strategy name */
    .strategy-name {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: 13.5px;
        color: var(--text);
        margin-bottom: 3px;
    }
    .strategy-tag {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .tag-traditional  { background: rgba(107,122,153,.15); color: var(--text-muted); border: 1px solid rgba(107,122,153,.25); }
    .tag-indexed      { background: rgba(79,142,247,.13);  color: var(--accent);      border: 1px solid rgba(79,142,247,.25); }
    .tag-cache        { background: rgba(56,217,169,.13);  color: var(--accent2);     border: 1px solid rgba(56,217,169,.25); }
    .tag-materialized { background: rgba(247,201,79,.13);  color: var(--accent3);     border: 1px solid rgba(247,201,79,.25); }
    .tag-partitioned  { background: rgba(167,92,247,.13);  color: #c4b5fd;            border: 1px solid rgba(167,92,247,.25); }

    /* time cell */
    .time-cell {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
    }
    .time-cell.best  { color: var(--accent2); }
    .time-cell.good  { color: var(--accent); }
    .time-cell.avg   { color: var(--accent3); }
    .time-cell.slow  { color: #f87171; }

    /* records cell */
    .records-cell {
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
    }
    .records-match {
        font-size: 10px;
        color: var(--accent2);
        display: flex; align-items: center; justify-content: center; gap: 3px;
        margin-top: 2px;
    }
    .records-mismatch {
        font-size: 10px;
        color: #f87171;
        display: flex; align-items: center; justify-content: center; gap: 3px;
        margin-top: 2px;
    }

    /* mejora cell */
    .mejora-cell {
        display: flex; flex-direction: column; align-items: center; gap: 5px;
    }
    .mejora-pct {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
    }
    .mejora-pct.positive { color: var(--accent2); }
    .mejora-pct.negative { color: #f87171; }
    .mejora-pct.base     { color: var(--text-muted); font-size: 12px; font-weight: 500; }
    .mejora-bar-bg {
        width: 80px; height: 5px;
        background: var(--bg3);
        border-radius: 99px;
        overflow: hidden;
    }
    .mejora-bar-fill {
        height: 100%;
        border-radius: 99px;
        background: linear-gradient(90deg, var(--accent2), var(--accent));
        transition: width .8s ease;
    }
    .mejora-bar-fill.negative {
        background: linear-gradient(90deg, #f87171, #ef4444);
    }

    /* ── Winner badge ── */
    .winner-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        background: rgba(56,217,169,.15);
        color: var(--accent2);
        border: 1px solid rgba(56,217,169,.3);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .4px;
    }

    /* ── Empty / initial state ── */
    .initial-state {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 60px 20px;
        text-align: center;
        color: var(--text-muted);
    }
    .initial-state .icon { font-size: 44px; margin-bottom: 12px; }
    .initial-state h3 { font-size: 16px; color: var(--text); margin-bottom: 8px; }
    .initial-state p  { font-size: 13px; max-width: 360px; margin: 0 auto; }

    /* ── Description col ── */
    .desc-text {
        font-size: 12.5px;
        color: var(--text-muted);
        max-width: 260px;
        line-height: 1.5;
    }

    /* Row highlight for best */
    .results-table tbody tr.row-best {
        background: rgba(56,217,169,.04);
    }
    .results-table tbody tr.row-best td:first-child {
        border-left: 3px solid var(--accent2);
    }
</style>
@endpush

@section('content')

    <!-- Configuracion -->
    <div class="config-card">
        <div class="config-card-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M16.24 16.24l1.41 1.41M4.93 4.93l1.41 1.41M7.76 16.24l-1.41 1.41M21 12h-2M5 12H3M12 3V1M12 23v-2"/></svg>
            Configuración de la Prueba
        </div>

        <form method="POST" action="{{ route('pruebas.ejecutar') }}" id="form-prueba">
            @csrf
            {{-- Valores predeterminados enviados como hidden inputs --}}
            <input type="hidden" name="producto_id"    value="{{ $defaultProductoId ?? 1 }}">
            <input type="hidden" name="almacen_id"     value="{{ $defaultAlmacenId ?? 1 }}">
            <input type="hidden" name="fecha_inicio"   value="{{ $defaultFechaInicio ?? '2024-01-01' }}">
            <input type="hidden" name="fecha_fin"      value="{{ $defaultFechaFin ?? '2024-12-31' }}">
            <input type="hidden" name="cant_registros" value="{{ $defaultCantRegistros ?? 1000 }}">

            <div class="config-grid">

                <div class="form-group">
                    <label>Producto</label>
                    <div class="static-val accent-blue">
                        <span class="static-dot"></span>
                        {{ $defaultProductoCodigo ?? 'PRD-001' }} — {{ $defaultProductoNombre ?? 'Arroz Extra' }}
                    </div>
                </div>

                <div class="form-group">
                    <label>Almacén</label>
                    <div class="static-val">
                        <span class="static-dot"></span>
                        {{ $defaultAlmacenNombre ?? 'Almacén Central' }}
                    </div>
                </div>

                <div class="form-group">
                    <label>Fecha inicio</label>
                    <div class="static-val accent-yellow">
                        <span class="static-dot"></span>
                        {{ isset($defaultFechaInicio) ? \Carbon\Carbon::parse($defaultFechaInicio)->format('d/m/Y') : '01/01/2024' }}
                    </div>
                </div>

                <div class="form-group">
                    <label>Fecha fin</label>
                    <div class="static-val accent-yellow">
                        <span class="static-dot"></span>
                        {{ isset($defaultFechaFin) ? \Carbon\Carbon::parse($defaultFechaFin)->format('d/m/Y') : '31/12/2024' }}
                    </div>
                </div>

                <div class="form-group">
                    <label>Cant. registros esperados</label>
                    <div class="static-val accent-purple">
                        <span class="static-dot"></span>
                        {{ number_format($defaultCantRegistros ?? 1000) }} registros
                    </div>
                </div>

            </div>

            <div class="run-row">
                <button type="submit" class="btn btn-primary" id="btn-ejecutar">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    Ejecutar Prueba
                </button>
                <span class="hint">Se comparará el rendimiento entre múltiples estrategias de consulta.</span>
            </div>

            <!-- Progress bar -->
            <div class="progress-bar-wrap" id="progress-wrap">
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
                <span class="progress-label" id="progress-label">Ejecutando pruebas…</span>
            </div>
        </form>
    </div>
    

    <!-- Resultados -->
    <div class="results-section">
        <div class="results-header">
            <div class="results-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Tabla de Resultados
                @if(isset($resultados) && count($resultados) > 0)
                    <span class="winner-badge">
                        ⚡ Mejor: {{ $mejorEstrategia ?? 'Indexación' }}
                    </span>
                @endif
            </div>
            @if(isset($resultados) && count($resultados) > 0)
            <div class="results-meta">
                <span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Ejecutado: {{ $fechaEjecucion ?? now()->format('d/m/Y H:i') }}
                </span>
                <span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    {{ count($resultados ?? []) }} estrategias
                </span>
            </div>
            @endif
        </div>

        @if(isset($resultados) && count($resultados) > 0)

        <div class="results-table-wrapper">
            <table class="results-table">
                <thead>
                    <tr>
                        <th style="min-width:200px;">Estrategia de Consulta</th>
                        <th style="min-width:240px;">Descripción</th>
                        <th style="min-width:140px;">Tiempo de Ejecución</th>
                        <th style="min-width:130px;">Registros Obtenidos</th>
                        <th style="min-width:160px;">% Mejora vs Tradicional</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resultados as $resultado)
                    <tr class="{{ $resultado['es_mejor'] ?? false ? 'row-best' : '' }}">

                        {{-- Estrategia --}}
                        <td>
                            <div class="strategy-name">{{ $resultado['estrategia'] }}</div>
                            <span class="strategy-tag tag-{{ strtolower(str_replace(' ', '_', $resultado['tipo'] ?? 'traditional')) }}">
                                {{ $resultado['tipo'] ?? 'Tradicional' }}
                            </span>
                        </td>

                        {{-- Descripción --}}
                        <td>
                            <div class="desc-text">{{ $resultado['descripcion'] }}</div>
                        </td>

                        {{-- Tiempo --}}
                        <td>
                            <span class="time-cell {{ $resultado['clase_tiempo'] ?? 'avg' }}">
                                {{ number_format($resultado['tiempo_ms'], 2) }} ms
                            </span>
                            @if(($resultado['tiempo_ms'] ?? 0) < 50)
                                <div style="font-size:10px;color:var(--accent2);margin-top:2px;">Excelente</div>
                            @elseif(($resultado['tiempo_ms'] ?? 0) < 200)
                                <div style="font-size:10px;color:var(--accent);margin-top:2px;">Bueno</div>
                            @elseif(($resultado['tiempo_ms'] ?? 0) < 500)
                                <div style="font-size:10px;color:var(--accent3);margin-top:2px;">Aceptable</div>
                            @else
                                <div style="font-size:10px;color:#f87171;margin-top:2px;">Lento</div>
                            @endif
                        </td>

                        {{-- Registros --}}
                        <td>
                            <div class="records-cell">{{ number_format($resultado['registros_obtenidos']) }}</div>
                            @if(isset($params['cant_registros']) && $params['cant_registros'])
                                @if($resultado['registros_obtenidos'] == $params['cant_registros'])
                                    <div class="records-match">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        Coincide
                                    </div>
                                @else
                                    <div class="records-mismatch">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        Esperado: {{ number_format($params['cant_registros']) }}
                                    </div>
                                @endif
                            @endif
                        </td>

                        {{-- % Mejora --}}
                        <td>
                            @php
                                $mejora = $resultado['mejora_pct'] ?? null;
                                $esBase = ($resultado['tipo'] ?? '') === 'Tradicional' || $mejora === null;
                            @endphp
                            <div class="mejora-cell">
                                @if($esBase)
                                    <span class="mejora-pct base">Referencia</span>
                                @elseif($mejora >= 0)
                                    <span class="mejora-pct positive">+{{ number_format($mejora, 1) }}%</span>
                                    <div class="mejora-bar-bg">
                                        <div class="mejora-bar-fill" style="width:{{ min($mejora, 100) }}%;"></div>
                                    </div>
                                @else
                                    <span class="mejora-pct negative">{{ number_format($mejora, 1) }}%</span>
                                    <div class="mejora-bar-bg">
                                        <div class="mejora-bar-fill negative" style="width:{{ min(abs($mejora), 100) }}%;"></div>
                                    </div>
                                @endif
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @else

        {{-- Estado inicial --}}
        <div class="initial-state">
            <div class="icon">⚡</div>
            <h3>Sin resultados aún</h3>
            <p>Configura los parámetros de la prueba arriba y presiona <strong>Ejecutar Prueba</strong> para comparar las estrategias de consulta.</p>
        </div>

        @endif
    </div>

    <!-- Resumen -->
    @if(isset($resultados) && count($resultados) > 0)
    <div style="margin-top: 32px;">

        {{-- Header --}}
        <div class="results-header" style="margin-bottom: 16px;">
            <div class="results-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M3 9h18M9 21V9"/></svg>
                Resumen de Rendimiento
            </div>
        </div>

        <div class="results-table-wrapper" style="padding: 24px 26px;">

            <p style="font-size: 12px; color: var(--text-muted); margin: 0 0 20px;">
                Tiempo de ejecución en milisegundos por estrategia de consulta
            </p>

            {{-- Leyenda --}}
            <div style="display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;">
                @foreach($resultados as $r)
                <span style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-muted);">
                    <span style="width:10px;height:10px;border-radius:2px;background:{{ $r['color_hex'] ?? '#888' }};"></span>
                    {{ $r['estrategia'] }}
                </span>
                @endforeach
            </div>

            {{-- Canvas Chart.js --}}
            <div style="position: relative; width: 100%; height: 280px;">
                <canvas id="chartRendimiento"
                    role="img"
                    aria-label="Gráfico de barras con los tiempos de ejecución de cada estrategia de consulta"
                ></canvas>
            </div>

            {{-- Mejor estrategia --}}
            @php
                $mejor = collect($resultados)->sortBy('tiempo_ms')->first();
                $base  = collect($resultados)->firstWhere('tipo', 'Tradicional');
                $mejoraPct = $base && $base['tiempo_ms'] > 0
                    ? round((($base['tiempo_ms'] - $mejor['tiempo_ms']) / $base['tiempo_ms']) * 100, 1)
                    : null;
            @endphp
            <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border); display: flex; align-items: flex-start; gap: 14px;">
                <div style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:rgba(56,217,169,.15);flex-shrink:0;">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="var(--accent2)" stroke-width="2.5"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                </div>
                <div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 3px;">Mejor estrategia</div>
                    <div style="font-size: 15px; font-weight: 700; color: var(--text); font-family: 'Syne', sans-serif;">
                        {{ $mejor['estrategia'] }} —
                        <span style="color: var(--accent2);">{{ number_format($mejor['tiempo_ms'], 2) }} ms</span>
                    </div>
                    @if($mejoraPct !== null)
                    <div style="font-size: 12px; color: var(--text-muted); margin-top: 3px;">
                        {{ $mejoraPct }}% más rápida que la estrategia tradicional ({{ number_format($base['tiempo_ms'], 2) }} ms)
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    @endif

@endsection

@push('scripts')
<script>
    /* ── Animación del progress bar al enviar el form ── */
    const form    = document.getElementById('form-prueba');
    const btnRun  = document.getElementById('btn-ejecutar');
    const pWrap   = document.getElementById('progress-wrap');
    const pFill   = document.getElementById('progress-fill');
    const pLabel  = document.getElementById('progress-label');

    const steps = [
        { pct: 15,  label: 'Iniciando estrategia tradicional…' },
        { pct: 35,  label: 'Ejecutando consulta con índices…' },
        { pct: 55,  label: 'Probando vistas materializadas…' },
        { pct: 72,  label: 'Aplicando estrategia de caché…' },
        { pct: 88,  label: 'Probando particionamiento…' },
        { pct: 96,  label: 'Compilando resultados…' },
    ];

    form?.addEventListener('submit', function () {
        btnRun.disabled = true;
        btnRun.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Ejecutando…';
        pWrap.classList.add('visible');

        let i = 0;
        const interval = setInterval(() => {
            if (i < steps.length) {
                pFill.style.width  = steps[i].pct + '%';
                pLabel.textContent = steps[i].label;
                i++;
            } else {
                clearInterval(interval);
            }
        }, 600);
    });

    /* ── Animate mejora bars on load ── */
    window.addEventListener('load', () => {
        document.querySelectorAll('.mejora-bar-fill').forEach(bar => {
            const w = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => { bar.style.width = w; }, 200);
        });
    });

    /* ── Chart.js – Resumen de Rendimiento ── */
    @if(isset($resultados) && count($resultados) > 0)
    (function () {
        const labels = @json(collect($resultados)->pluck('estrategia'));
        const data   = @json(collect($resultados)->pluck('tiempo_ms'));
        const colors = @json(collect($resultados)->pluck('color_hex'));

        const isDark    = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const gridColor = isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)';
        const tickColor = isDark ? '#b4b2a9' : '#6b7a99';

        new Chart(document.getElementById('chartRendimiento'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Tiempo (ms)',
                    data,
                    backgroundColor: colors.map(c => c + 'cc'),
                    borderColor: colors,
                    borderWidth: 1.5,
                    borderRadius: 5,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: ctx => ' ' + ctx.parsed.y.toFixed(2) + ' ms' }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: tickColor, font: { size: 12 }, autoSkip: false, maxRotation: 0 },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: tickColor, font: { size: 11 }, callback: v => v + ' ms' },
                        grid: { color: gridColor }
                    }
                }
            }
        });
    })();
    @endif
</script>
@endpush