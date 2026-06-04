@extends('layouts.app')

@section('title', 'Consulta Kardex')
@section('page-title', 'Consulta de Inventario')
@section('breadcrumb', 'Kardex')

@push('styles')
<style>
  /* Base structural adjustments */
  .kardex-container {
    max-width: 1400px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 24px;
    position: relative;
  }
  
  .filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
  }

  .radio-group {
    display: flex;
    gap: 20px;
    align-items: center;
    padding: 8px 0;
  }
  
  .radio-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 13px;
    color: var(--text);
    cursor: pointer;
    text-transform: none;
    letter-spacing: normal;
    margin: 0;
  }

  .radio-group input[type="radio"] {
    accent-color: var(--accent);
    width: 16px;
    height: 16px;
    cursor: pointer;
  }

  /* FIX: Override global inputs to respect dark/light mode */
  .input,
  .select,
  .ts-control,
  .ts-dropdown,
  .ts-dropdown .option {
    background: var(--surface) !important;
    color: var(--text) !important;
    border-color: var(--border) !important;
  }

  /* Make sure text inside the control and inputs is also legible */
  .ts-control input, 
  .ts-control .item,
  .input,
  .select {
    color: var(--text) !important;
  }

  .ts-dropdown .active {
    background: rgba(79, 163, 255, 0.15) !important;
    color: var(--text) !important;
  }
  
  .light .ts-dropdown .active {
    background: rgba(36, 107, 253, 0.15) !important;
  }

  .ts-dropdown {
    padding-bottom: 8px !important;
  }
  
  .ts-dropdown .ts-dropdown-content {
    max-height: 250px;
    overflow-y: auto;
    padding-bottom: 8px;
  }
  
  /* Fix scrollbar space if it overlaps */
  .ts-dropdown .option {
    margin-bottom: 4px;
    padding: 10px 14px !important;
  }

  /* Custom Loading Overlay */
  #loading-overlay {
    position: absolute;
    inset: 0;
    background: var(--surface-strong);
    backdrop-filter: blur(8px);
    z-index: 50;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
  }
  
  #loading-overlay.active {
    opacity: 1;
    pointer-events: auto;
  }

  .spinner {
    width: 48px;
    height: 48px;
    border: 4px solid var(--border);
    border-top-color: var(--accent);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 16px;
    box-shadow: 0 0 15px rgba(79, 163, 255, 0.2);
  }

  @keyframes spin { 
    to { transform: rotate(360deg); } 
  }

  .filter-section {
    transition: opacity 0.3s ease;
  }
</style>
@endpush

@section('content')
<div class="kardex-container">

  <!-- FILTER PANEL -->
  <div class="card" style="padding: 32px;">
    
    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
      <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(79, 163, 255, 0.1); border: 1px solid rgba(79, 163, 255, 0.2); display: flex; align-items: center; justify-content: center; color: var(--accent);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
      </div>
      <div>
        <h2 style="font-family: 'Rajdhani', sans-serif; font-size: 22px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text); margin: 0;">Filtros de Búsqueda</h2>
        <p style="font-size: 12px; color: var(--text-muted); margin: 4px 0 0 0;">Configura los parámetros para consultar el inventario.</p>
      </div>
    </div>

    <form id="filter-form" action="{{ route('kardex.index') }}" method="GET">
      <div class="filter-grid">
        
        <div class="filter-section">
          <label for="producto">Producto</label>
          <select name="producto" id="producto">
            @if(isset($productoModel))
              <option value="{{ $productoModel->Producto }}" selected>{{ $productoModel->Producto }} &mdash; {{ $productoModel->Descripcion }}</option>
            @endif
          </select>
          <div id="product-info" style="margin-top:10px; font-size:0.85rem; color: var(--text-muted); font-weight: 500;">
            @if(isset($productoModel))
              Descripción: {{ $productoModel->Descripcion }}
            @endif
          </div>
        </div>

        <div class="filter-section">
          <label for="estrategia">Estrategia de Búsqueda</label>
          <select name="estrategia" id="estrategia" class="ts-select">
            <option value="tradicional" {{ request('estrategia') == 'tradicional' ? 'selected' : '' }}>Tradicional (Lento)</option>
            <option value="optimizada" {{ request('estrategia') == 'optimizada' ? 'selected' : '' }}>Optimizada (Rápido)</option>
            <option value="optimizada_indices" {{ request('estrategia') == 'optimizada_indices' ? 'selected' : '' }}>Optimizada + Índices (Ultra Rápido)</option>
          </select>
        </div>

        <div class="filter-section" id="index-controls" style="display: {{ request('estrategia') == 'optimizada_indices' ? 'flex' : 'none' }}; flex-direction: column; gap: 10px;">
          <label>Administrar índices</label>
          <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <div id="index-status" style="font-size: 0.95rem; font-weight: 600; color: var(--text);">Estado de índices: consultando...</div>
            <button type="button" id="activar-indices" class="btn btn-secondary" style="padding: 10px 18px;">Activar índices</button>
            <button type="button" id="desactivar-indices" class="btn btn-secondary" style="padding: 10px 18px;">Desactivar índices</button>
          </div>
          <div id="index-message" style="font-size: 0.85rem; color: var(--text-muted);">Activa o desactiva los índices cuando uses la estrategia Optimizada + Índices.</div>
        </div>

        <div class="filter-section">
          <label>Consultar Por</label>
          <div class="radio-group" style="background: var(--bg2); padding: 10px 16px; border: 1px solid var(--border); border-radius: 12px;">
            <label><input type="radio" name="consultar_por" value="fechas" {{ request('consultar_por','fechas') == 'fechas' ? 'checked' : '' }} onchange="toggleSections('fechas')"> Fechas</label>
            <label><input type="radio" name="consultar_por" value="mes" {{ request('consultar_por') == 'mes' ? 'checked' : '' }} onchange="toggleSections('mes')"> Mes</label>
            <label><input type="radio" name="consultar_por" value="anio" {{ request('consultar_por') == 'anio' ? 'checked' : '' }} onchange="toggleSections('anio')"> Año</label>
          </div>
        </div>
        
        <!-- Contenedores Dinámicos -->
        <div class="filter-section" id="date-range" style="display:flex; gap:16px; {{ request('consultar_por','fechas') != 'fechas' ? 'opacity:0.3; pointer-events:none;' : '' }}">
          <div style="flex:1;">
            <label for="fecha_inicio">Fecha Inicio</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="input" value="{{ request('fecha_inicio') }}">
          </div>
          <div style="flex:1;">
            <label for="fecha_fin">Fecha Fin</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="input" value="{{ request('fecha_fin') }}">
          </div>
        </div>
        
        @php
            $mesesNombres = [
                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
            ];
        @endphp

        <div class="filter-section" id="month-range" style="display:flex; gap:16px; {{ request('consultar_por') != 'mes' ? 'display:none;' : '' }}">
          <div style="flex:1;">
            <label for="sel_anio_mes">Año</label>
            <select name="anio_mes" id="sel_anio_mes" class="ts-select">
              @foreach($aniosDisponibles as $y)
                <option value="{{ $y }}" {{ request('anio_mes', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
              @endforeach
            </select>
          </div>
          <div style="flex:1;">
            <label for="sel_mes">Mes</label>
            <select name="mes" id="sel_mes" class="ts-select">
              @foreach($mesesDisponibles as $m)
                <option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}" {{ request('mes') == str_pad($m,2,'0',STR_PAD_LEFT) ? 'selected' : '' }}>{{ $mesesNombres[(int)$m] ?? $m }}</option>
              @endforeach
            </select>
          </div>
        </div>
        
        <div class="filter-section" id="year-range" style="display:flex; gap:16px; {{ request('consultar_por') != 'anio' ? 'display:none;' : '' }}">
          <div style="flex:1;">
            <label for="sel_anio">Año</label>
            <select name="anio" id="sel_anio" class="ts-select">
              @foreach($aniosDisponibles as $y)
                <option value="{{ $y }}" {{ request('anio', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
              @endforeach
            </select>
          </div>
        </div>

      </div>

      <div style="display: flex; justify-content: flex-end; gap: 16px; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border);">
        <button type="button" class="btn btn-secondary" onclick="resetForm()">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px;"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
          Restablecer
        </button>
        <button type="submit" class="btn btn-primary" style="padding: 11px 28px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          Consultar
        </button>
      </div>
    </form>
  </div>

  <!-- Contenedor de Resultados AJAX -->
  <div class="card" style="padding: 0; min-height: 400px; position: relative;">
    <div id="loading-overlay">
      <div class="spinner"></div>
      <div style="font-family: 'Rajdhani', sans-serif; font-size: 20px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: var(--accent);">Procesando Datos</div>
      <div style="font-size: 13px; color: var(--text-muted); margin-top: 8px;">Conectando con la base de datos...</div>
    </div>
    
    <div id="kardex-results-container" style="padding: 32px;">
      @include('kardex._results')
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  const periodosPorAnio = @json($periodosPorAnio ?? []);
  const mesesNombres = {
      1: 'Enero', 2: 'Febrero', 3: 'Marzo', 4: 'Abril',
      5: 'Mayo', 6: 'Junio', 7: 'Julio', 8: 'Agosto',
      9: 'Septiembre', 10: 'Octubre', 11: 'Noviembre', 12: 'Diciembre'
  };

  let tomSelectProduct;
  let otherSelects = [];
  let tsMes, tsAnioMes, tsAnio, tsEstrategia;
    const indicesEstadoUrl = '{{ route('kardex.indices.estado') }}';
    const indicesToggleUrl = '{{ route('kardex.indices.toggle') }}';
    // Inicializar TomSelect para Productos con AJAX Dinámico
    tomSelectProduct = new TomSelect('#producto', {
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
          }).catch(()=>{
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
      onChange: function(value) {
        const infoDiv = document.getElementById('product-info');
        if (value) {
            const item = this.options[value];
            infoDiv.textContent = item ? `Descripción: ${item.Descripcion}` : '';
        } else {
            infoDiv.textContent = '';
        }
      },
      dropdownParent: 'body'
    });

    // Inicializar los otros selectores básicos con TomSelect para estilización consistente y scroll
    tsEstrategia = new TomSelect('#estrategia', {
        controlInput: null,
        maxOptions: null,
        dropdownParent: 'body',
        onChange: function() {
            actualizarIndexControls();
        }
    });
    tsAnio = new TomSelect('#sel_anio', { controlInput: null, maxOptions: null, dropdownParent: 'body' });
    
    tsAnioMes = new TomSelect('#sel_anio_mes', { 
        controlInput: null, 
        maxOptions: null,
        dropdownParent: 'body',
        onChange: function(value) {
            actualizarMeses(value);
        }
    });

    tsMes = new TomSelect('#sel_mes', { controlInput: null, maxOptions: null, dropdownParent: 'body' });

    otherSelects = [tsEstrategia, tsAnio, tsAnioMes, tsMes];

    const indexControls = document.getElementById('index-controls');
    const indexStatus = document.getElementById('index-status');
    const indexMessage = document.getElementById('index-message');
    const activarIndicesBtn = document.getElementById('activar-indices');
    const desactivarIndicesBtn = document.getElementById('desactivar-indices');

    function actualizarIndexControls() {
      if (!indexControls) return;
      const selectedStrategy = (tsEstrategia && typeof tsEstrategia.getValue === 'function')
        ? tsEstrategia.getValue()
        : document.getElementById('estrategia').value;
      const mostrar = selectedStrategy === 'optimizada_indices';
      indexControls.style.display = mostrar ? 'flex' : 'none';
      if (mostrar) {
        cargarEstadoIndices();
      }
    }

    function cargarEstadoIndices() {
      if (!indexStatus) return;
      indexStatus.textContent = 'Estado de índices: consultando...';
      fetch(indicesEstadoUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(data => {
          const activo = data.todos_activos === true;
          indexStatus.textContent = activo ? 'Estado de índices: activos' : 'Estado de índices: inactivos';
          activarIndicesBtn.disabled = activo;
          desactivarIndicesBtn.disabled = !activo;
        })
        .catch(() => {
          indexStatus.textContent = 'No se pudo consultar el estado de índices.';
          activarIndicesBtn.disabled = false;
          desactivarIndicesBtn.disabled = false;
        });
    }

    function cambiarEstadoIndices(accion) {
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      activarIndicesBtn.disabled = true;
      desactivarIndicesBtn.disabled = true;
      indexStatus.textContent = 'Estado de índices: actualizando...';

      fetch(indicesToggleUrl, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ accion })
      })
        .then(response => response.json())
        .then(data => {
          if (data.ok) {
            const activo = data.indices_activos.todos_activos === true;
            indexStatus.textContent = activo ? 'Estado de índices: activos' : 'Estado de índices: inactivos';
            activarIndicesBtn.disabled = activo;
            desactivarIndicesBtn.disabled = !activo;
            indexMessage.textContent = data.mensaje || 'Estado de índices actualizado.';
          } else {
            throw new Error(data.error || 'Error al cambiar índices');
          }
        })
        .catch(() => {
          indexStatus.textContent = 'No se pudo actualizar el estado de índices.';
          indexMessage.textContent = 'Verifique la conexión y que su base de datos sea SQL Server.';
          activarIndicesBtn.disabled = false;
          desactivarIndicesBtn.disabled = false;
        });
    }

    if (activarIndicesBtn) {
      activarIndicesBtn.addEventListener('click', function() {
        cambiarEstadoIndices('crear');
      });
    }
    if (desactivarIndicesBtn) {
      desactivarIndicesBtn.addEventListener('click', function() {
        cambiarEstadoIndices('eliminar');
      });
    }

    if (document.getElementById('estrategia')) {
      document.getElementById('estrategia').addEventListener('change', actualizarIndexControls);
    }
    if (tsEstrategia && typeof tsEstrategia.on === 'function') {
      tsEstrategia.on('change', actualizarIndexControls);
    }

    actualizarIndexControls();

    function actualizarMeses(anioStr) {
        if (!tsMes) return;
        const currentMes = tsMes.getValue();
        tsMes.clearOptions();
        
        const anio = parseInt(anioStr);
        if (periodosPorAnio[anio]) {
            periodosPorAnio[anio].forEach(m => {
                const mesStr = m.toString().padStart(2, '0');
                tsMes.addOption({ value: mesStr, text: mesesNombres[m] || mesStr });
            });
            // Mantener mes seleccionado si existe en el nuevo año
            const options = tsMes.options;
            if (currentMes && options[currentMes]) {
                tsMes.setValue(currentMes, true);
            } else if (periodosPorAnio[anio].length > 0) {
                const primerMes = periodosPorAnio[anio][0].toString().padStart(2, '0');
                tsMes.setValue(primerMes, true);
            }
        }
    }

    // Inicializar los meses al cargar la página si hay un año seleccionado
    if (tsAnioMes.getValue()) {
        actualizarMeses(tsAnioMes.getValue());
    }

    // Toggle de visibilidad de secciones
    window.toggleSections = function(modo) {
      const dates = document.getElementById('date-range');
      const months = document.getElementById('month-range');
      const years = document.getElementById('year-range');

      dates.style.display = 'flex';
      months.style.display = 'flex';
      years.style.display = 'flex';
      
      dates.style.opacity = (modo === 'fechas') ? '1' : '0.3';
      dates.style.pointerEvents = (modo === 'fechas') ? 'auto' : 'none';
      if(modo !== 'fechas') dates.style.display = 'none';

      months.style.opacity = (modo === 'mes') ? '1' : '0.3';
      months.style.pointerEvents = (modo === 'mes') ? 'auto' : 'none';
      if(modo !== 'mes') months.style.display = 'none';

      years.style.opacity = (modo === 'anio') ? '1' : '0.3';
      years.style.pointerEvents = (modo === 'anio') ? 'auto' : 'none';
      if(modo !== 'anio') years.style.display = 'none';
    };

    window.resetForm = function() {
      document.getElementById('filter-form').reset();
      if (tomSelectProduct) {
          tomSelectProduct.clear();
          document.getElementById('product-info').textContent = '';
      }
      if (tsEstrategia && typeof tsEstrategia.setValue === 'function') {
          tsEstrategia.setValue('tradicional');
      }
      if (tsAnioMes && typeof tsAnioMes.setValue === 'function') {
          const selAnioMes = document.getElementById('sel_anio_mes');
          tsAnioMes.setValue(selAnioMes ? selAnioMes.value : '');
      }
      if (tsMes && typeof tsMes.setValue === 'function') {
          const selMes = document.getElementById('sel_mes');
          tsMes.setValue(selMes ? selMes.value : '');
      }
      if (tsAnio && typeof tsAnio.setValue === 'function') {
          const selAnio = document.getElementById('sel_anio');
          tsAnio.setValue(selAnio ? selAnio.value : '');
      }
      toggleSections('fechas');
      actualizarIndexControls();
      
      // Restablecer los resultados recargando la ruta base (sin parámetros)
      fetchKardexData(form.action, false);
    };

    const container = document.getElementById('kardex-results-container');
    const overlay = document.getElementById('loading-overlay');
    const form = document.getElementById('filter-form');
    const kardexCard = overlay.parentElement;

    // Función principal para realizar la petición AJAX
    function fetchKardexData(url, shouldScroll = true) {
      overlay.classList.add('active');
      
      if (shouldScroll) {
          // Usar scrollIntoView que es más nativo y confiable
          setTimeout(() => {
              container.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }, 50);
      }
      
      fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'text/html'
        }
      })
      .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.text();
      })
      .then(html => {
        container.innerHTML = html;
        window.history.pushState({}, '', url);
        
        if (shouldScroll) {
            // Desplazar a ver los resultados
            setTimeout(() => {
                kardexCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
      })
      .catch(error => {
        console.error('Error fetching kardex data:', error);
        container.innerHTML = '<div class="alert alert-error" style="margin: 32px;">Ocurrió un error al cargar los datos. Por favor, intente nuevamente.</div>';
      })
      .finally(() => {
        setTimeout(() => overlay.classList.remove('active'), 250);
      });
    }

    // Interceptar envío del formulario
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(form);
      const searchParams = new URLSearchParams(formData);
      const url = `${form.action}?${searchParams.toString()}`;
      
      fetchKardexData(url);
    });

    // Interceptar clics en la paginación (DELEGATION)
    container.addEventListener('click', function(e) {
      const pageLink = e.target.closest('.custom-pagination a, .page-btns a');
      if (pageLink) {
        e.preventDefault();
        fetchKardexData(pageLink.href);
      }
    });

    // Navegación hacia atrás/adelante del navegador
    window.addEventListener('popstate', function() {
      fetchKardexData(window.location.href);
    });
  });
</script>
@endpush