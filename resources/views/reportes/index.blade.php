@extends('layouts.app')

@section('title', 'Reportes')
@section('page-title', 'Reportes de Inventario')
@section('breadcrumb', 'Reportes')

@push('styles')
<style>
  .reportes-container {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 24px;
  }

  .tabs-container {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    border-bottom: 1px solid var(--border);
    padding-bottom: 16px;
  }

  .tab-btn {
    padding: 12px 24px;
    background: transparent;
    border: 1px solid var(--border);
    border-radius: 12px;
    color: var(--text-muted);
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all var(--transition);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .tab-btn:hover {
    background: rgba(79, 163, 255, 0.08);
    color: var(--text);
    border-color: rgba(79, 163, 255, 0.2);
  }

  .tab-btn.active {
    background: rgba(79, 163, 255, 0.15);
    color: var(--accent);
    border-color: rgba(79, 163, 255, 0.4);
    box-shadow: 0 0 0 1px rgba(79, 163, 255, 0.2);
  }

  .tab-content {
    display: none;
  }

  .tab-content.active {
    display: block;
    animation: fadeIn 0.3s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .filter-row {
    display: flex;
    gap: 16px;
    align-items: flex-end;
    margin-bottom: 24px;
  }

  .filter-item {
    flex: 1;
  }

  .time-indicator {
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .time-indicator span {
    color: var(--accent);
    font-weight: 700;
  }

  .loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid var(--border);
    border-top-color: var(--accent);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }

  .empty-state {
    text-align: center;
    padding: 60px 24px;
    color: var(--text-muted);
  }

  .empty-state svg {
    width: 64px;
    height: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
  }

  .table-container {
    overflow-x: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead th {
    padding: 16px 20px;
    text-align: left;
    font-family: 'Rajdhani', sans-serif;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-muted);
    background: rgba(255, 255, 255, 0.02);
    border-bottom: 1px solid var(--border);
  }

  tbody td {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
  }

  tbody tr:hover {
    background: rgba(79, 163, 255, 0.05);
  }

  .negative-value {
    color: #ff5a5a;
    font-weight: 700;
  }

  .positive-value {
    color: var(--accent2);
    font-weight: 700;
  }
</style>
@endpush

@section('content')
<div class="reportes-container">

  <!-- TABS -->
  <div class="card" style="padding: 32px;">
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
      <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(79, 163, 255, 0.1); border: 1px solid rgba(79, 163, 255, 0.2); display: flex; align-items: center; justify-content: center; color: var(--accent);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
      </div>
      <div>
        <h2 style="font-family: 'Rajdhani', sans-serif; font-size: 22px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text); margin: 0;">Reportes de Inventario</h2>
        <p style="font-size: 12px; color: var(--text-muted); margin: 4px 0 0 0;">Selecciona un tipo de reporte para visualizar la información.</p>
      </div>
    </div>

    <div class="tabs-container">
      <button class="tab-btn active" data-tab="stock-critico">Stock Crítico</button>
      <button class="tab-btn" data-tab="resumen-mensual">Resumen Mensual</button>
      <button class="tab-btn" data-tab="valor-inventario">Valor Inventario</button>
    </div>

    <!-- TAB: STOCK CRÍTICO -->
    <div id="tab-stock-critico" class="tab-content active">
      <div id="stock-critico-loading" style="display: none; text-align: center; padding: 40px;">
        <div class="loading-spinner" style="width: 40px; height: 40px; margin: 0 auto 16px;"></div>
        <div style="color: var(--text-muted);">Cargando datos...</div>
      </div>
      <div id="stock-critico-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <div class="time-indicator">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Tiempo de respuesta: <span id="stock-critico-time">—</span> s
          </div>
          <div style="display: flex; gap: 12px;">
            <a href="{{ route('reportes.pdf.stock-critico') }}" class="btn" style="background: #ef4444; color: white; border-color: #dc2626; padding: 10px 18px; display: flex; align-items: center; gap: 8px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
              PDF
            </a>
            <a href="{{ route('reportes.excel.stock-critico') }}" class="btn" style="background: #22c55e; color: white; border-color: #16a34a; padding: 10px 18px; display: flex; align-items: center; gap: 8px;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
              Excel
            </a>
          </div>
        </div>
        <div class="table-container">
          <table id="stock-critico-table">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Descripción</th>
                <th style="text-align: right;">Stock Mín</th>
                <th style="text-align: right;">Stock Real</th>
                <th style="text-align: right;">Diferencia</th>
              </tr>
            </thead>
            <tbody id="stock-critico-body">
            </tbody>
          </table>
        </div>
        <div style="margin-top: 30px;">
          <canvas id="stock-critico-chart" style="max-height: 400px;"></canvas>
        </div>
      </div>
    </div>

    <!-- TAB: RESUMEN MENSUAL -->
    <div id="tab-resumen-mensual" class="tab-content">
      <div class="filter-row">
        <div class="filter-item">
          <label for="rm-producto">Producto</label>
          <select id="rm-producto" name="producto">
            <option value="">Seleccionar producto...</option>
          </select>
        </div>
        <div class="filter-item">
          <label for="rm-anio">Año</label>
          <select id="rm-anio" name="anio" class="ts-select">
            @for($y = date('Y'); $y >= 2000; $y--)
              <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
          </select>
        </div>
        <div class="filter-item" style="flex: 0 0 auto;">
          <button id="rm-consultar" class="btn btn-primary" style="padding: 12px 24px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            Consultar
          </button>
        </div>
      </div>
      <div id="resumen-mensual-loading" style="display: none; text-align: center; padding: 40px;">
        <div class="loading-spinner" style="width: 40px; height: 40px; margin: 0 auto 16px;"></div>
        <div style="color: var(--text-muted);">Cargando datos...</div>
      </div>
      <div id="resumen-mensual-content" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <div class="time-indicator">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Tiempo de respuesta: <span id="resumen-mensual-time">—</span> s
          </div>
          <div style="display: flex; gap: 12px;">
            <button id="rm-export-pdf" class="btn" style="background: #ef4444; color: white; border-color: #dc2626; padding: 10px 18px; display: flex; align-items: center; gap: 8px; cursor: pointer;" disabled>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
              PDF
            </button>
            <button id="rm-export-excel" class="btn" style="background: #22c55e; color: white; border-color: #16a34a; padding: 10px 18px; display: flex; align-items: center; gap: 8px; cursor: pointer;" disabled>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
              Excel
            </button>
          </div>
        </div>
        <div class="table-container">
          <table id="resumen-mensual-table">
            <thead>
              <tr>
                <th>Mes</th>
                <th style="text-align: right;">Entradas</th>
                <th style="text-align: right;">Salidas</th>
                <th style="text-align: right;">Valor Entradas</th>
                <th style="text-align: right;">Valor Salidas</th>
                <th style="text-align: right;">Stock Final</th>
              </tr>
            </thead>
            <tbody id="resumen-mensual-body">
            </tbody>
          </table>
        </div>
        <div style="margin-top: 30px; height: 600px;">
          <canvas id="resumen-mensual-chart"></canvas>
        </div>
      </div>
      <div id="resumen-mensual-empty" class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <p>Selecciona un producto y año para generar el reporte.</p>
      </div>
    </div>

    <!-- TAB: VALOR INVENTARIO -->
    <div id="tab-valor-inventario" class="tab-content">
      <div class="filter-row">
        <div class="filter-item">
          <label for="vi-producto">Producto (opcional)</label>
          <select id="vi-producto" name="producto">
            <option value="">Todos los productos</option>
          </select>
        </div>
        <div class="filter-item" style="flex: 0 0 auto;">
          <button id="vi-consultar" class="btn btn-primary" style="padding: 12px 24px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            Consultar
          </button>
        </div>
      </div>
      <div id="valor-inventario-loading" style="display: none; text-align: center; padding: 40px;">
        <div class="loading-spinner" style="width: 40px; height: 40px; margin: 0 auto 16px;"></div>
        <div style="color: var(--text-muted);">Cargando datos...</div>
      </div>
      <div id="valor-inventario-content" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <div class="time-indicator">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Tiempo de respuesta: <span id="valor-inventario-time">—</span> s
          </div>
          <div style="display: flex; gap: 12px;">
            <button id="vi-export-pdf" class="btn" style="background: #ef4444; color: white; border-color: #dc2626; padding: 10px 18px; display: flex; align-items: center; gap: 8px; cursor: pointer;" disabled>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
              PDF
            </button>
            <button id="vi-export-excel" class="btn" style="background: #22c55e; color: white; border-color: #16a34a; padding: 10px 18px; display: flex; align-items: center; gap: 8px; cursor: pointer;" disabled>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
              Excel
            </button>
          </div>
        </div>
        <div class="table-container">
          <table id="valor-inventario-table">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Descripción</th>
                <th>Unidad Medida</th>
                <th style="text-align: right;">Stock Real</th>
                <th style="text-align: right;">Costo Promedio</th>
                <th style="text-align: right;">Valor Inventario</th>
                <th style="text-align: right;">Precio Venta</th>
                <th style="text-align: right;">Valor Venta</th>
              </tr>
            </thead>
            <tbody id="valor-inventario-body">
            </tbody>
          </table>
        </div>
      </div>
      <div id="valor-inventario-empty" class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <p>Haz clic en "Consultar" para generar el reporte.</p>
      </div>
    </div>

  </div>

</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const mesesNombres = {
      1: 'Enero', 2: 'Febrero', 3: 'Marzo', 4: 'Abril',
      5: 'Mayo', 6: 'Junio', 7: 'Julio', 8: 'Agosto',
      9: 'Septiembre', 10: 'Octubre', 11: 'Noviembre', 12: 'Diciembre'
    };

    // Inicializar TomSelect para productos
    let tsRmProducto, tsViProducto;
    
    tsRmProducto = new TomSelect('#rm-producto', {
      valueField: 'Producto',
      labelField: 'Descripcion',
      searchField: ['Producto', 'Descripcion'],
      placeholder: 'Buscar código o descripción...',
      load: function(query, callback) {
        if (!query.length) return callback();
        fetch('/api/productos/buscar?q=' + encodeURIComponent(query))
          .then(response => response.json())
          .then(json => {
            callback(json);
          }).catch(() => {
            callback();
          });
      },
      render: {
        option: function(item, escape) {
          return `<div><span style="font-weight: 700;">${escape(item.Producto)}</span> &mdash; ${escape(item.Descripcion)}</div>`;
        },
        item: function(item, escape) {
          return `<div>${escape(item.Producto)} &mdash; ${escape(item.Descripcion)}</div>`;
        }
      },
      dropdownParent: 'body'
    });

    tsViProducto = new TomSelect('#vi-producto', {
      valueField: 'Producto',
      labelField: 'Descripcion',
      searchField: ['Producto', 'Descripcion'],
      placeholder: 'Buscar código o descripción...',
      load: function(query, callback) {
        if (!query.length) return callback();
        fetch('/api/productos/buscar?q=' + encodeURIComponent(query))
          .then(response => response.json())
          .then(json => {
            callback(json);
          }).catch(() => {
            callback();
          });
      },
      render: {
        option: function(item, escape) {
          return `<div><span style="font-weight: 700;">${escape(item.Producto)}</span> &mdash; ${escape(item.Descripcion)}</div>`;
        },
        item: function(item, escape) {
          return `<div>${escape(item.Producto)} &mdash; ${escape(item.Descripcion)}</div>`;
        }
      },
      dropdownParent: 'body'
    });

    // Inicializar select de año con TomSelect
    new TomSelect('#rm-anio', { maxOptions: null, dropdownParent: 'body' });

    // Tabs functionality
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        const tabId = this.getAttribute('data-tab');
        
        tabBtns.forEach(b => b.classList.remove('active'));
        tabContents.forEach(c => c.classList.remove('active'));
        
        this.classList.add('active');
        document.getElementById('tab-' + tabId).classList.add('active');

        // Cargar automáticamente stock crítico al entrar
        if (tabId === 'stock-critico') {
          loadStockCritico();
        }
      });
    });

    // Cargar stock crítico automáticamente al iniciar
    loadStockCritico();

    // Stock Crítico
    function loadStockCritico() {
      const loading = document.getElementById('stock-critico-loading');
      const content = document.getElementById('stock-critico-content');
      const tbody = document.getElementById('stock-critico-body');
      const timeSpan = document.getElementById('stock-critico-time');
      
      loading.style.display = 'block';
      content.style.display = 'none';
      tbody.innerHTML = '';
      
      const startTime = performance.now();
      
      fetch('{{ route("reportes.stock-critico") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(response => response.json())
      .then(data => {
        const endTime = performance.now();
        timeSpan.textContent = ((endTime - startTime) / 1000).toFixed(4);
        
        if (data.ok && data.data.length > 0) {
          data.data.forEach(row => {
            const diff = parseFloat(row.Diferencia || 0);
            const diffClass = diff < 0 ? 'negative-value' : (diff > 0 ? 'positive-value' : '');
            
            tbody.innerHTML += `
              <tr>
                <td style="font-weight: 600;">${row.Producto || '—'}</td>
                <td>${row.Descripcion || '—'}</td>
                <td style="text-align: right;">${number_format(row.StockMin, 2)}</td>
                <td style="text-align: right;">${number_format(row.StockReal, 2)}</td>
                <td style="text-align: right;" class="${diffClass}">${number_format(diff, 2)}</td>
              </tr>
            `;
          });
          
          // Crear gráfico de barras horizontales
          createStockCriticoChart(data.data);
        } else {
          tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--text-muted);">No hay datos de stock crítico</td></tr>';
          // Destruir gráfico si existe
          destroyChart('stock-critico-chart');
        }
        
        loading.style.display = 'none';
        content.style.display = 'block';
      })
      .catch(error => {
        console.error('Error loading stock crítico:', error);
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #ff5a5a;">Error al cargar datos</td></tr>';
        loading.style.display = 'none';
        content.style.display = 'block';
      });
    }

    // Resumen Mensual
    document.getElementById('rm-consultar').addEventListener('click', function() {
      const producto = tsRmProducto.getValue();
      const anio = document.getElementById('rm-anio').value;
      
      if (!producto) {
        alert('Por favor, selecciona un producto');
        return;
      }
      
      const loading = document.getElementById('resumen-mensual-loading');
      const content = document.getElementById('resumen-mensual-content');
      const empty = document.getElementById('resumen-mensual-empty');
      const tbody = document.getElementById('resumen-mensual-body');
      const timeSpan = document.getElementById('resumen-mensual-time');
      
      loading.style.display = 'block';
      content.style.display = 'none';
      empty.style.display = 'none';
      tbody.innerHTML = '';
      
      const startTime = performance.now();
      
      const url = `{{ route("reportes.resumen-mensual") }}?idProducto=${encodeURIComponent(producto)}&anio=${anio}`;
      
      fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(response => response.json())
      .then(data => {
        const endTime = performance.now();
        timeSpan.textContent = ((endTime - startTime) / 1000).toFixed(4);
        
        if (data.ok && data.data.length > 0) {
          data.data.forEach(row => {
            const mesNombre = row.NombreMes || mesesNombres[row.Mes] || row.Mes;
            
            tbody.innerHTML += `
              <tr>
                <td style="font-weight: 600;">${mesNombre}</td>
                <td style="text-align: right; color: var(--accent2);">+${number_format(row.TotalEntradas, 2)}</td>
                <td style="text-align: right; color: #ff5a5a;">${number_format(row.TotalSalidas, 2)}</td>
                <td style="text-align: right;">S/ ${number_format(row.ValorEntradas, 2)}</td>
                <td style="text-align: right;">S/ ${number_format(row.ValorSalidas, 2)}</td>
                <td style="text-align: right; font-weight: 700;">${number_format(row.StockFinal, 2)}</td>
              </tr>
            `;
          });
          
          loading.style.display = 'none';
          content.style.display = 'block';
          
          // Crear gráfico de líneas (con manejo de errores)
          try {
            createResumenMensualChart(data.data);
          } catch (error) {
            console.error('Error creating chart:', error);
          }
          
          // Habilitar botones de exportación
          document.getElementById('rm-export-pdf').disabled = false;
          document.getElementById('rm-export-excel').disabled = false;
        } else {
          loading.style.display = 'none';
          empty.style.display = 'block';
          empty.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><p>No hay datos para el producto y año seleccionados.</p>';
          
          // Destruir gráfico si existe
          destroyChart('resumen-mensual-chart');
          
          // Deshabilitar botones de exportación
          document.getElementById('rm-export-pdf').disabled = true;
          document.getElementById('rm-export-excel').disabled = true;
        }
      })
      .catch(error => {
        console.error('Error loading resumen mensual:', error);
        loading.style.display = 'none';
        empty.style.display = 'block';
        empty.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg><p>Error al cargar datos. Por favor, intenta nuevamente.</p>';
        
        // Deshabilitar botones de exportación
        document.getElementById('rm-export-pdf').disabled = true;
        document.getElementById('rm-export-excel').disabled = true;
      });
    });

    // Export buttons for Resumen Mensual
    document.getElementById('rm-export-pdf').addEventListener('click', function() {
      const producto = tsRmProducto.getValue();
      const anio = document.getElementById('rm-anio').value;
      if (producto && anio) {
        window.location.href = `{{ route('reportes.pdf.resumen-mensual') }}?idProducto=${encodeURIComponent(producto)}&anio=${anio}`;
      }
    });

    document.getElementById('rm-export-excel').addEventListener('click', function() {
      const producto = tsRmProducto.getValue();
      const anio = document.getElementById('rm-anio').value;
      if (producto && anio) {
        window.location.href = `{{ route('reportes.excel.resumen-mensual') }}?idProducto=${encodeURIComponent(producto)}&anio=${anio}`;
      }
    });

    // Valor Inventario
    document.getElementById('vi-consultar').addEventListener('click', function() {
      const producto = tsViProducto.getValue();
      
      const loading = document.getElementById('valor-inventario-loading');
      const content = document.getElementById('valor-inventario-content');
      const empty = document.getElementById('valor-inventario-empty');
      const tbody = document.getElementById('valor-inventario-body');
      const timeSpan = document.getElementById('valor-inventario-time');
      
      loading.style.display = 'block';
      content.style.display = 'none';
      empty.style.display = 'none';
      tbody.innerHTML = '';
      
      const startTime = performance.now();
      
      let url = '{{ route("reportes.valor-inventario") }}';
      if (producto) {
        url += `?idProducto=${encodeURIComponent(producto)}`;
      }
      
      fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(response => response.json())
      .then(data => {
        const endTime = performance.now();
        timeSpan.textContent = ((endTime - startTime) / 1000).toFixed(4);
        
        if (data.ok && data.data.length > 0) {
          data.data.forEach(row => {
            tbody.innerHTML += `
              <tr>
                <td style="font-weight: 600;">${row.Producto || '—'}</td>
                <td>${row.Descripcion || '—'}</td>
                <td>${row.UniMed || '—'}</td>
                <td style="text-align: right;">${number_format(row.StockReal, 2)}</td>
                <td style="text-align: right;">S/ ${number_format(row.CostoPromedio, 2)}</td>
                <td style="text-align: right; font-weight: 700;">S/ ${number_format(row.ValorInventario, 2)}</td>
                <td style="text-align: right;">S/ ${number_format(row.PrecVenta, 2)}</td>
                <td style="text-align: right; font-weight: 700;">S/ ${number_format(row.ValorVenta, 2)}</td>
              </tr>
            `;
          });
          
          loading.style.display = 'none';
          content.style.display = 'block';
          
          // Habilitar botones de exportación
          document.getElementById('vi-export-pdf').disabled = false;
          document.getElementById('vi-export-excel').disabled = false;
        } else {
          loading.style.display = 'none';
          empty.style.display = 'block';
          empty.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><p>No hay datos de inventario disponibles.</p>';
          
          // Deshabilitar botones de exportación
          document.getElementById('vi-export-pdf').disabled = true;
          document.getElementById('vi-export-excel').disabled = true;
        }
      })
      .catch(error => {
        console.error('Error loading valor inventario:', error);
        loading.style.display = 'none';
        empty.style.display = 'block';
        empty.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg><p>Error al cargar datos. Por favor, intenta nuevamente.</p>';
        
        // Deshabilitar botones de exportación
        document.getElementById('vi-export-pdf').disabled = true;
        document.getElementById('vi-export-excel').disabled = true;
      });
    });

    // Export buttons for Valor Inventario
    document.getElementById('vi-export-pdf').addEventListener('click', function() {
      const producto = tsViProducto.getValue();
      let url = '{{ route('reportes.pdf.valor-inventario') }}';
      if (producto) {
        url += `?idProducto=${encodeURIComponent(producto)}`;
      }
      window.location.href = url;
    });

    document.getElementById('vi-export-excel').addEventListener('click', function() {
      const producto = tsViProducto.getValue();
      let url = '{{ route('reportes.excel.valor-inventario') }}';
      if (producto) {
        url += `?idProducto=${encodeURIComponent(producto)}`;
      }
      window.location.href = url;
    });

    // Helper function for number formatting
    function number_format(num, decimals) {
      if (num === null || num === undefined || isNaN(num)) return '0.00';
      return parseFloat(num).toFixed(decimals);
    }

    // Chart instances storage
    const chartInstances = {};

    // Destroy chart if exists
    function destroyChart(canvasId) {
      if (chartInstances[canvasId]) {
        chartInstances[canvasId].destroy();
        delete chartInstances[canvasId];
      }
    }

    // Create Stock Crítico Chart (Horizontal Bar Chart)
    function createStockCriticoChart(data) {
      destroyChart('stock-critico-chart');
      
      const ctx = document.getElementById('stock-critico-chart').getContext('2d');
      
      // Detect theme
      const isLight = document.documentElement.classList.contains('light');
      const textColor = isLight ? '#1f2937' : '#fff';
      const gridColor = isLight ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.1)';
      const ticksColor = isLight ? '#4b5563' : '#9ca3af';
      
      // Limit to top 10 products for better readability
      const topData = data.slice(0, 10);
      
      chartInstances['stock-critico-chart'] = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: topData.map(row => row.Producto || '—'),
          datasets: [
            {
              label: 'Stock Mínimo',
              data: topData.map(row => row.StockMin || 0),
              backgroundColor: 'rgba(59, 130, 246, 0.7)',
              borderColor: 'rgba(59, 130, 246, 1)',
              borderWidth: 1
            },
            {
              label: 'Stock Real',
              data: topData.map(row => row.StockReal || 0),
              backgroundColor: 'rgba(16, 185, 129, 0.7)',
              borderColor: 'rgba(16, 185, 129, 1)',
              borderWidth: 1
            }
          ]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
              labels: {
                color: textColor,
                font: { size: 12 }
              }
            },
            title: {
              display: true,
              text: 'Stock Crítico - Top 10 Productos',
              color: textColor,
              font: { size: 16, weight: 'bold' }
            }
          },
          scales: {
            x: {
              ticks: { color: ticksColor },
              grid: { color: gridColor }
            },
            y: {
              ticks: { color: ticksColor },
              grid: { color: gridColor }
            }
          }
        }
      });
    }

    // Create Resumen Mensual Chart (Line Chart)
    function createResumenMensualChart(data) {
      destroyChart('resumen-mensual-chart');
      
      const ctx = document.getElementById('resumen-mensual-chart').getContext('2d');
      
      // Detect theme
      const isLight = document.documentElement.classList.contains('light');
      const textColor = isLight ? '#1f2937' : '#fff';
      const gridColor = isLight ? 'rgba(0, 0, 0, 0.1)' : 'rgba(255, 255, 255, 0.1)';
      const ticksColor = isLight ? '#4b5563' : '#9ca3af';
      
      chartInstances['resumen-mensual-chart'] = new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.map(row => row.NombreMes || mesesNombres[row.Mes] || row.Mes),
          datasets: [
            {
              label: 'Entradas',
              data: data.map(row => row.TotalEntradas || 0),
              borderColor: 'rgba(16, 185, 129, 1)',
              backgroundColor: 'rgba(16, 185, 129, 0.2)',
              fill: true,
              tension: 0.4
            },
            {
              label: 'Salidas',
              data: data.map(row => row.TotalSalidas || 0),
              borderColor: 'rgba(239, 68, 68, 1)',
              backgroundColor: 'rgba(239, 68, 68, 0.2)',
              fill: true,
              tension: 0.4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
              labels: {
                color: textColor,
                font: { size: 12 }
              }
            },
            title: {
              display: true,
              text: 'Resumen Mensual - Entradas vs Salidas',
              color: textColor,
              font: { size: 16, weight: 'bold' }
            }
          },
          scales: {
            x: {
              ticks: { color: ticksColor },
              grid: { color: gridColor }
            },
            y: {
              ticks: { color: ticksColor },
              grid: { color: gridColor }
            }
          }
        }
      });
    }
  });
</script>
@endpush
