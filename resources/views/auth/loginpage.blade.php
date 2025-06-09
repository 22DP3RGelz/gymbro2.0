@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <h2>Login to GymBro</h2>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit">Login</button>
        </form>
    </div>
</div>

<style>
.auth-container {
    max-width: 400px;
    margin: 4rem auto;
    padding: 0 1rem;
}

.auth-card {
    background: var(--secondary-gray);
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.auth-card h2 {
    margin-bottom: 2rem;
    color: var(--accent-color);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
}

.form-group input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid var(--accent-color);
    border-radius: 4px;
    background: var(--primary-gray);
    color: var(--text-light);
}

button[type="submit"] {
    width: 100%;
    margin-top: 2rem;
}
</style>
@endsection
