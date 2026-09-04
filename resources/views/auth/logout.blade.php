<form method="POST" action="{{ route('logout') }}" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-outline-danger btn-sm">
        Cerrar sesión
    </button>
</form>