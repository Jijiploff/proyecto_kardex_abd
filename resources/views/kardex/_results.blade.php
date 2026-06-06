@if(isset($kardex) && $totalRegistros > 0)
    <!-- METRICS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 32px;">
      
      <!-- Entrada -->
      <div class="stat-card" style="padding: 24px; display: flex; flex-direction: column; position: relative; overflow: hidden; background: var(--bg2); border-radius: 12px;">
        <div style="position: absolute; right: -15px; top: -15px; opacity: 0.1; color: var(--accent2);">
          <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
        </div>
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); margin-bottom: 12px; z-index: 1;">TOTAL ENTRADAS</div>
        <div style="font-size: 32px; font-weight: 800; color: var(--accent2); font-family: 'Rajdhani', sans-serif; line-height: 1; z-index: 1;">
          +{{ number_format($totalEntradas, 2) }}
        </div>
      </div>

      <!-- Salida -->
      <div class="stat-card" style="padding: 24px; display: flex; flex-direction: column; position: relative; overflow: hidden; background: var(--bg2); border-radius: 12px;">
        <div style="position: absolute; right: -15px; top: -15px; opacity: 0.08; color: #ff5a5a;">
          <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"></polyline><polyline points="17 18 23 18 23 12"></polyline></svg>
        </div>
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); margin-bottom: 12px; z-index: 1;">TOTAL SALIDAS</div>
        <div style="font-size: 32px; font-weight: 800; color: #ff5a5a; font-family: 'Rajdhani', sans-serif; line-height: 1; z-index: 1;">
          -{{ number_format($totalSalidas, 2) }}
        </div>
      </div>

      <!-- Stock -->
      <div class="stat-card" style="padding: 24px; display: flex; flex-direction: column; position: relative; overflow: hidden; background: var(--bg2); border-radius: 12px;">
        <div style="position: absolute; right: -15px; top: -15px; opacity: 0.08; color: var(--accent);">
          <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
        </div>
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); margin-bottom: 12px; z-index: 1;">STOCK ACTUAL</div>
        <div style="font-size: 32px; font-weight: 800; color: var(--text); font-family: 'Rajdhani', sans-serif; line-height: 1; z-index: 1;">
          {{ number_format($stockActual, 2) }}
        </div>
      </div>
    </div>
    
    <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
      <div class="summary-pill" style="padding: 10px 18px; display: inline-flex; flex-direction: column; justify-content: center; min-width: 150px;">
        <span style="font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Registros Encontrados</span>
        <span style="font-size: 16px; font-weight: 700; color: var(--text); font-family: 'Rajdhani', sans-serif;">{{ number_format($totalRegistros) }}</span>
      </div>
      <div class="summary-pill" style="padding: 10px 18px; display: inline-flex; flex-direction: column; justify-content: center; min-width: 150px;">
        <span style="font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Tiempo Total (Controlador)</span>
        <span style="font-size: 16px; font-weight: 700; color: var(--text); font-family: 'Rajdhani', sans-serif;">{{ $tiempoRespuesta }} s</span>
      </div>
      <div class="summary-pill" style="padding: 10px 18px; display: inline-flex; flex-direction: column; justify-content: center; min-width: 150px;">
        <span style="font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Tiempo de Ejecución del SP</span>
        <span style="font-size: 16px; font-weight: 700; color: var(--accent); font-family: 'Rajdhani', sans-serif;">⏱️ {{ isset($tiempoSp) ? $tiempoSp : '0.0000' }} s</span>
      </div>
      <div class="summary-pill" style="padding: 10px 18px; display: inline-flex; flex-direction: column; justify-content: center; min-width: 150px;">
        <span style="font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Estrategia</span>
        <span style="font-size: 16px; font-weight: 700; color: var(--accent); font-family: 'Rajdhani', sans-serif;">{{ $nombreEstrategiaDisplay ?? 'Tradicional' }}</span>
      </div>
    </div>

    <!-- RESULT TABLE -->
    <div class="results-table-wrapper" style="position: relative;">
      
      <div style="padding: 22px 28px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; font-family: 'Rajdhani', sans-serif; font-size: 20px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">
        <div style="width: 32px; height: 32px; background: rgba(79, 163, 255, 0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--accent);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        </div>
        Detalle de Movimientos
      </div>
      
      <div style="overflow-x: auto;">
        <table>
          <thead>
            <tr>
              <th style="padding: 16px 28px; text-align: left;">Fecha</th>
              <th style="padding: 16px; text-align: left;">Documento</th>
              <th style="padding: 16px; text-align: left;">Producto</th>
              <th style="padding: 16px; text-align: left;">Tipo</th>
              <th style="padding: 16px; text-align: right;">Costo Unit.</th>
              <th style="padding: 16px; text-align: right;">Cantidad</th>
              <th style="padding: 16px; text-align: right;">Total</th>
              <th style="padding: 16px 28px; text-align: right;">Stock</th>
            </tr>
          </thead>
          <tbody>
            @foreach($kardex as $mov)
            <tr>
              <td style="padding: 18px 28px; font-weight: 600; white-space: nowrap;">{{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y') }}</td>
              <td style="padding: 18px 16px;">{{ $mov->comprobante ?? '—' }}</td>
              <td style="padding: 18px 16px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $productoModel->Descripcion ?? '—' }}">{{ $productoModel->Descripcion ?? '—' }}</td>
              <td style="padding: 18px 16px;">
                @if($mov->tipo == 'entrada')
                  <span class="badge" style="background: rgba(53, 224, 184, 0.15); color: var(--accent2); border: 1px solid rgba(53, 224, 184, 0.25);">{{ $mov->tipo_nombre ?? 'Entrada' }}</span>
                @elseif($mov->tipo == 'salida')
                  <span class="badge" style="background: rgba(255, 90, 90, 0.15); color: #ff5a5a; border: 1px solid rgba(255, 90, 90, 0.25);">{{ $mov->tipo_nombre ?? 'Salida' }}</span>
                @else
                  <span class="badge" style="background: rgba(245, 200, 79, 0.15); color: var(--accent3); border: 1px solid rgba(245, 200, 79, 0.25);">{{ $mov->tipo_nombre ?? 'Ajuste' }}</span>
                @endif
              </td>
              <td style="padding: 18px 16px; text-align: right; color: var(--text-muted); font-weight: 500;">
                @if($mov->comprobante === 'ACUMULADO')
                  —
                @else
                  S/ {{ number_format($mov->costo_unitario, 2) }}
                @endif
              </td>
              <td style="padding: 18px 16px; text-align: right; font-weight: 700; font-size: 15px;">
                @if($mov->comprobante === 'ACUMULADO')
                  —
                @elseif($mov->tipo == 'entrada')
                  <span style="color: var(--accent2);">+{{ number_format($mov->cantidad, 2) }}</span>
                @elseif($mov->tipo == 'salida')
                  <span style="color: #ff5a5a;">{{ number_format($mov->cantidad, 2) }}</span>
                @else
                  {{ number_format($mov->cantidad, 2) }}
                @endif
              </td>
              <td style="padding: 18px 16px; text-align: right; color: var(--text-muted); font-weight: 500;">S/ {{ number_format($mov->costo_total, 2) }}</td>
              <td style="padding: 18px 28px; text-align: right; font-weight: 800; color: var(--accent); font-size: 15px;">{{ number_format($mov->saldo, 2) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      @if(method_exists($kardex,'links'))
      <div style="padding: 24px 28px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <span style="font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Mostrando {{ $kardex->firstItem() }} al {{ $kardex->lastItem() }} de {{ $kardex->total() }} registros</span>
        <div class="page-btns custom-pagination">
          {!! $kardex->appends(request()->query())->links('pagination.kardex') !!}
        </div>
      </div>
      @endif
    </div>
  @elseif(isset($sinDatosParaPeriodo) && $sinDatosParaPeriodo)
    <div class="initial-state" style="text-align: center; padding: 70px 24px;">
      <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--bg2); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto; color: var(--text-muted);">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
      </div>
      <h3 style="font-family: 'Rajdhani', sans-serif; font-size: 26px; font-weight: 700; color: var(--text); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">Sin Datos Registrados</h3>
      <p style="color: var(--text-muted); font-size: 15px; max-width: 450px; margin: 0 auto; line-height: 1.6;">No existen movimientos para los filtros seleccionados en este periodo de tiempo.</p>
    </div>
  @elseif(isset($kardex) && $kardex->total() === 0)
    <div class="initial-state" style="text-align: center; padding: 70px 24px;">
      <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--bg2); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto; color: var(--text-muted);">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      </div>
      <h3 style="font-family: 'Rajdhani', sans-serif; font-size: 26px; font-weight: 700; color: var(--text); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">Sin Coincidencias</h3>
      <p style="color: var(--text-muted); font-size: 15px; max-width: 450px; margin: 0 auto; line-height: 1.6;">No se encontraron movimientos con los parámetros actuales. Prueba ajustando los filtros de búsqueda.</p>
    </div>
  @else
    <div class="initial-state" style="text-align: center; padding: 80px 24px;">
      <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--bg2); display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto; color: var(--text-muted);">
        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
      </div>
      <h3 style="font-family: 'Rajdhani', sans-serif; font-size: 28px; font-weight: 700; color: var(--text); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">Sistema Kardex</h3>
      <p style="color: var(--text-muted); font-size: 15px; max-width: 450px; margin: 0 auto; line-height: 1.6;">Selecciona un producto y aplica filtros en el panel superior para visualizar el historial de movimientos.</p>
    </div>
  @endif
