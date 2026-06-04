@extends('layouts.app')

@section('title', '404 — Página no encontrada')
@section('page-title', 'Error 404')
@section('breadcrumb', 'No encontrado')

@push('styles')
<style>
    .error-404-shell {
        min-height: 60vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 10px;
        animation: cardRise 520ms ease both;
    }
    .error-code {
        font-family: 'Rajdhani', sans-serif;
        font-size: clamp(92px, 12vw, 154px);
        font-weight: 700;
        line-height: 1;
        letter-spacing: 0.08em;
        color: transparent;
        background: linear-gradient(135deg, var(--accent), var(--accent4), var(--accent2));
        -webkit-background-clip: text;
        background-clip: text;
        text-shadow: 0 0 30px rgba(79, 163, 255, 0.18);
    }
    .error-title {
        font-family: 'Rajdhani', sans-serif;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--text);
    }
    .error-copy {
        color: var(--text-muted);
        max-width: 460px;
        line-height: 1.7;
        font-size: 14px;
    }
    .error-action {
        padding: 12px 22px;
    }
</style>
@endpush

@section('content')
<div class="error-404-shell">
    <div class="error-code">404</div>
    <h1 class="error-title">Página no encontrada</h1>
    <p class="error-copy">
        Lo sentimos, la página que estás buscando no existe o ha sido movida. Verifica la URL o regresa al inicio.
    </p>
    <a href="{{ route('home') }}" class="btn btn-primary error-action">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 8px;"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Volver al Inicio
    </a>
</div>
@endsection
