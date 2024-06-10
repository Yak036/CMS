<nav class=" sm:fixed sm:top-0 sm:right-0 p-6 text-end z-10 navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">Inicio</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            @auth
            <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="{{ url('/dashboard') }}" wire:navigate>Mi inicio</a>
            </li>
            @else
            <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}" wire:navigate>Iniciar Sesion</a>
            </li>
            <li class="nav-item">
            <a class="nav-link" href="{{ route('register') }}" wire:navigate>Registrarse</a>
            </li>
            @endauth
        </ul>
        </div>
    </div>
</nav>