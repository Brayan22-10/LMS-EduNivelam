@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Crear Publicación</h2>
    <form method="POST" action="{{ route('posts.store') }}" class="mt-3">
        @csrf
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Contenido</label>
            <textarea name="content" rows="5" class="form-control @error('content') is-invalid @enderror" required>{{ old('content') }}</textarea>
            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn-dark">Guardar Publicación</button>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection