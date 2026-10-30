@auth
@vite(['resources/css/logout-animation.css', 'resources/js/logout-animation.js'])

<div id="logoutScene" data-stage="idle" role="status" aria-live="polite" aria-hidden="true">
    <svg class="ls-svg" viewBox="0 0 400 230" aria-hidden="true">
        <defs>
            <linearGradient id="lsGlow" x1="0" y1="0" x2="1" y2="0">
                <stop offset="0" stop-color="#fde68a"/>
                <stop offset="1" stop-color="#f59e0b"/>
            </linearGradient>
        </defs>

        <line x1="20" y1="200" x2="380" y2="200" stroke="#475569" stroke-width="3" stroke-linecap="round"/>

        <rect x="268" y="58" width="88" height="142" rx="4" fill="#1e293b" stroke="#64748b" stroke-width="4"/>
        <rect class="ls-light" x="274" y="64" width="76" height="136" fill="url(#lsGlow)"/>

        <g class="ls-door">
            <rect x="274" y="64" width="76" height="136" fill="#8b5a2b"/>
            <rect x="282" y="74" width="60" height="50" rx="2" fill="none" stroke="#6b4423" stroke-width="3"/>
            <rect x="282" y="134" width="60" height="56" rx="2" fill="none" stroke="#6b4423" stroke-width="3"/>
            <circle cx="286" cy="129" r="4" fill="#fbbf24"/>
        </g>

        <g class="ls-fig">
            <ellipse cx="0" cy="2" rx="16" ry="3" fill="rgba(0,0,0,.35)"/>
            <rect class="ls-limb ls-leg" x="-7" y="-34" width="7" height="34" rx="3.5" fill="#64748b"/>
            <rect class="ls-limb ls-leg rev" x="0" y="-34" width="7" height="34" rx="3.5" fill="#64748b"/>
            <rect x="-9" y="-74" width="18" height="44" rx="8" fill="#38bdf8"/>
            <rect class="ls-limb ls-arm rev" x="-3.5" y="-70" width="7" height="30" rx="3.5" fill="#fcd9b6"/>
            <rect class="ls-limb ls-arm ls-arm-r" x="-3.5" y="-70" width="7" height="30" rx="3.5" fill="#fcd9b6"/>
            <circle cx="0" cy="-88" r="13" fill="#fcd9b6"/>
            <circle cx="4.5" cy="-90" r="1.8" fill="#0f172a"/>
        </g>
    </svg>

    <p class="ls-text">
        <span class="ls-t1">Cerrando sesión…</span>
        <span class="ls-t2">¡Hasta pronto, {{ \Illuminate\Support\Str::of(Auth::user()->name ?? '')->before(' ') }}!</span>
    </p>
    <small class="ls-skip">Haz clic para salir ahora</small>
</div>
@endauth