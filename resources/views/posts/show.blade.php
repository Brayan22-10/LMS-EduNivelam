@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <h1>{{ $post->title }}</h1>
            <p class="text-muted small">Escrito por <strong>{{ $post->user->name }}</strong> el {{ $post->created_at->format('d/m/Y H:i') }}</p>
            <hr>
            <p style="white-space: pre-line;">{{ $post->content }}</p>

            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">Volver</a>

                @can('update', $post)
                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-dark">Editar</a>
                @endcan

                @can('delete', $post)
                    <form method="POST" action="{{ route('posts.destroy', $post) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta publicación?')">Eliminar</button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection