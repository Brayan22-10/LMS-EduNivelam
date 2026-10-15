<form action="{{ route('logout') }}" method="POST" style="display: inline;">
    @csrf
    <button type="submit" class="btn-logout" style="background: none; border: none; cursor: pointer;">
        Cerrar Sesión
    </button>
</form>