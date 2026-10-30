@php
    $user = Auth::user();
    $isGuest = ! $user;
    $isVerified = $user && $user->hasVerifiedEmail();

    $isPanelRoute = request()->is('admin*') || request()->is('dashboard*') || request()->is('teacher*') || request()->is('student*') || request()->is('settings*');
    $isInAnyPanel = $isPanelRoute || request()->routeIs('dashboard*') || request()->routeIs('admin.*') || request()->routeIs('settings.*') || request()->routeIs('teacher.*') || request()->routeIs('student.*');

    $showLinks = $isGuest || $isVerified || $isInAnyPanel;

    $links = [
        ['label' => 'Home',        'icon' => 'bi-house-door',       'url' => route('home'),        'active' => request()->routeIs('home')],
        ['label' => 'About',       'icon' => 'bi-info-circle',      'url' => route('about'),       'active' => request()->routeIs('about')],
        ['label' => 'News',        'icon' => 'bi-newspaper',        'url' => route('news.index'),  'active' => request()->routeIs('news.*')],
        ['label' => 'Courses',     'icon' => 'bi-journal-bookmark', 'url' => route('courses'),     'active' => request()->routeIs('courses*')],
        ['label' => 'Instructors', 'icon' => 'bi-person-workspace', 'url' => route('instructors'), 'active' => request()->routeIs('instructors*')],
        ['label' => 'Contact',     'icon' => 'bi-envelope',         'url' => route('contact'),     'active' => request()->routeIs('contact*')],
    ];

    $campus = null;
    if ($user && $showLinks) {
        if ($user->role === 'admin') {
            $campus = [
                'label'  => 'Dashboard',
                'icon'   => 'bi-speedometer2',
                'url'    => route('dashboard'),
                'active' => request()->routeIs('dashboard*') || request()->routeIs('admin.*') || request()->is('admin*'),
            ];
        } elseif ($user->role === 'teacher') {
            $campus = [
                'label'  => 'Mi Campus',
                'icon'   => 'bi-mortarboard',
                'url'    => route('teacher.dashboard'),
                'active' => request()->routeIs('teacher.*') || request()->is('teacher*'),
            ];
        } else {
            $campus = [
                'label'  => 'Mi Campus',
                'icon'   => 'bi-mortarboard',
                'url'    => route('student.dashboard'),
                'active' => request()->routeIs('student.*') || request()->is('student*'),
            ];
        }
    }
@endphp

<div class="{{ $isInAnyPanel ? 'panel-nav-container px-0 pt-2 mb-3' : 'container py-3' }}">
    <nav class="pill-nav floating-navbar" aria-label="Navegación principal">

        <a class="pill-brand" href="{{ route('home') }}">EduNivelam</a>

        <div class="pill-scroll">
            <ul class="pill-list">

                @if($showLinks)
                    @foreach($links as $link)
                        <li>
                            <a class="pill-item {{ $link['active'] ? 'is-active' : '' }}"
                               href="{{ $link['url'] }}"
                               @if($link['active']) aria-current="page" @endif>
                                <span class="pill-icon"><i class="bi {{ $link['icon'] }}"></i></span>
                                <span class="pill-label">{{ $link['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                @endif

                @if($isGuest)
                    <li class="pill-sep" aria-hidden="true"></li>
                    <li>
                        <a class="pill-item {{ request()->routeIs('login*') ? 'is-active' : '' }}" href="{{ route('login') }}">
                            <span class="pill-icon"><i class="bi bi-box-arrow-in-right"></i></span>
                            <span class="pill-label">Login</span>
                        </a>
                    </li>
                    <li>
                        <a class="pill-item {{ request()->routeIs('register*') ? 'is-active' : '' }}" href="{{ route('register') }}">
                            <span class="pill-icon"><i class="bi bi-person-plus"></i></span>
                            <span class="pill-label">Sign Up</span>
                        </a>
                    </li>
                @else
                    <li class="pill-sep" aria-hidden="true"></li>

                    @if($campus)
                        <li>
                            <a class="pill-item {{ $campus['active'] ? 'is-active' : '' }}"
                               href="{{ $campus['url'] }}"
                               @if($campus['active']) aria-current="page" @endif>
                                <span class="pill-icon"><i class="bi {{ $campus['icon'] }}"></i></span>
                                <span class="pill-label">{{ $campus['label'] }}</span>
                            </a>
                        </li>
                    @endif

                    @if(! $showLinks)
                        <li>
                            <span class="pill-item pill-status" title="Verificación de correo pendiente">
                                <span class="pill-icon"><i class="bi bi-envelope-exclamation"></i></span>
                                <span class="pill-label">Verifica tu correo</span>
                            </span>
                        </li>
                    @endif

                    <li class="pill-hello-wrap">
                        <span class="pill-hello">Hola, {{ $user->name }}</span>
                    </li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="pill-item pill-danger">
                                <span class="pill-icon"><i class="bi bi-box-arrow-right"></i></span>
                                <span class="pill-label">Salir</span>
                            </button>
                        </form>
                    </li>
                @endif

            </ul>
        </div>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var scroller = document.querySelector('.pill-scroll');
        var active = scroller ? scroller.querySelector('.is-active') : null;
        if (scroller && active) {
            scroller.scrollLeft = active.offsetLeft - (scroller.clientWidth - active.clientWidth) / 2;
        }
    });
</script>