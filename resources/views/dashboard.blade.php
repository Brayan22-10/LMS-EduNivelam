@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <h1 class="mb-3">Dashboard</h1>
            <p class="lead">Welcome, <strong>{{ Auth::user()->name ?? 'Administrator' }}</strong>.</p>
            <p>This panel is protected by server-side authentication middleware.</p>
            
            <div class="mt-4">
                @include('auth.logout')
            </div>
        </div>
    </div>
</div>
@endsection