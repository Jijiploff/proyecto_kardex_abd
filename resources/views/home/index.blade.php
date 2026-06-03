@extends('layouts.app')

@section('title', 'Home — KardexPro')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Home')

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 18px;
        margin-bottom: 28px;
    }
    .stat-card {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 22px 24px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        transition: border-color var(--transition), transform var(--transition);
    }
    .stat-card:hover {
        border-color: rgba(79,142,247,.4);
        transform: translateY(-2px);
    }
    .stat-header {
        display: flex; align-items: center; justify-content: space-between;
    }
    .stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .8px;
        color: var(--text-muted);
    }
    .stat-icon {
        width: 34px; height: 34px;
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
    }
    .stat-value {
        font-family: 'Syne', sans-serif;
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
        line-height: 1;
    }
    .stat-delta {
        font-size: 12px;
        color: var(--accent2);
        font-weight: 500;
    }

    .section-title {
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 14px;
    }

    .quick-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
    }
    .quick-card {
        background: var(--bg2);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 22px;
        text-decoration: none;
        display: flex; align-items: flex-start; gap: 16px;
        transition: all var(--transition);
    }
    .quick-card:hover {
        border-color: rgba(79,142,247,.45);
        background: rgba(79,142,247,.05);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,.3);
    }
    .quick-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .quick-body h3 {
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }
    .quick-body p {
        font-size: 12.5px;
        color: var(--text-muted);
        line-height: 1.5;
    }
</style>
@endpush

@section('content')

    <!-- Stats row -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Productos activos</span>
                <div class="stat-icon" style="background:rgba(79,142,247,.15);">📦</div>
            </div>
            <div class="stat-value">{{ $totalProductos ?? '—' }}</div>
            <div class="stat-delta">↑ Inventario vigente</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Almacenes</span>
                <div class="stat-icon" style="background:rgba(56,217,169,.15);">🏭</div>
            </div>
            <div class="stat-value">{{ $totalAlmacenes ?? '—' }}</div>
            <div class="stat-delta">Almacenes registrados</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Movimientos hoy</span>
                <div class="stat-icon" style="background:rgba(247,201,79,.15);">📋</div>
            </div>
            <div class="stat-value">{{ $movimientosHoy ?? '—' }}</div>
            <div class="stat-delta">Entradas y salidas</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Pruebas ejecutadas</span>
                <div class="stat-icon" style="background:rgba(167,92,247,.15);">⚡</div>
            </div>
            <div class="stat-value">{{ $totalPruebas ?? '—' }}</div>
            <div class="stat-delta">Benchmarks realizados</div>
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
        <a href="{{ route('pruebas.index') }}" class="quick-card">
            <div class="quick-icon" style="background:rgba(56,217,169,.12);">📊</div>
            <div class="quick-body">
                <h3>Pruebas de Rendimiento</h3>
                <p>Ejecuta y compara estrategias de consulta para medir tiempos de respuesta.</p>
            </div>
        </a>
    </div>

@endsection