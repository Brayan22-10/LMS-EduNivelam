@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <h1 class="mb-3">Dashboard</h1>
            <p class="lead">Welcome, <strong>{{ Auth::user()->name ?? 'Administrator' }}</strong>.</p>
            <p>This panel is protected by server-side authentication middleware.</p>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-danger">Log Out</button>
            </form>
        </div>
    </div>
</div>
@endsection