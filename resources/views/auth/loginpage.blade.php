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
    background: var(--surface);
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.auth-card h2 {
    margin-bottom: 2rem;
    color: var(--text);
    font-weight: 600;
    text-align: center;
}

.form-group {
    margin-bottom: 1.5rem;
    padding: 0 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-light);
}

.form-group input {
    width: calc(100% - 2rem);
    padding: 0.75rem 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 0.5rem;
    background: var(--surface-light);
    color: var(--text);
    transition: all 0.2s;
    margin: 0 1rem;
}

.form-group input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

button[type="submit"] {
    width: calc(100% - 2rem);
    margin: 1rem;
    padding: 0.75rem 1.5rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 0.5rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s;
}

button[type="submit"]:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
}
</style>
@endsection
