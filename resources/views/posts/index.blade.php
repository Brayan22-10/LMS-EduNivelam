@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
        <h1 class="h2 mb-0">Publicaciones</h1>
        @auth
            <a href="{{ route('posts.create') }}" class="btn btn-dark w-100 w-sm-auto">Crear Nueva Publicación</a>
        @endauth
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @foreach($posts as $post)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h3><a href="{{ route('posts.show', $post) }}" class="text-decoration-none text-dark">{{ $post->title }}</a></h3>
                <p class="text-muted small">Escrito por <strong>{{ $post->user->name }}</strong> el {{ $post->created_at->format('d/m/Y H:i') }}</p>
                <p>{{ Str::limit($post->content, 150) }}</p>
                <a href="{{ route('posts.show', $post) }}" class="btn btn-sm btn-outline-dark">Leer más</a>
            </div>
        </div>
    @endforeach

    {{ $posts->links() }}
</div>
@endsection