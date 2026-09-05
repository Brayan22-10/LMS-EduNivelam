@extends('layouts.app')

@section('title', 'Contacto - Sitio Seguro')

@section('styles')
    @vite(['resources/css/contact.css'])
@endsection

@section('content')
<section class="py-5">  
    <div class="container">
        <h2>Formulario de Contacto</h2>
        <hr class="mb-4">

        <form method="POST" action="{{ route('contact.store') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" required>
            </div>

            <div class="mb-3">
                <label for="subject" class="form-label">Asunto</label>
                <input type="text" name="subject" class="form-control" id="subject" placeholder="Asunto" required>
            </div>

            <div class="mb-3">
                <label for="message" class="form-label">Mensaje</label>
                <textarea name="message" class="form-control" id="message" rows="3" placeholder="Escribe tu mensaje aquí..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>
</section>
@endsection

@section('scripts')
    @vite(['resources/js/contact.js'])
@endsection