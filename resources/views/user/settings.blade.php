@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="settings-card card">
        <h2>User Settings</h2>

        <div class="user-info">
            <h3>Account Information</h3>
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Member Since:</strong> {{ $user->created_at->format('M d, Y') }}</p>
        </div>

        <div class="password-change">
            <h3>Change Password</h3>
            
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('settings.password') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" name="current_password" id="current_password" required>
                    @error('current_password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" required>
                    @error('new_password')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required>
                </div>

                <button type="submit">Update Password</button>
            </form>
        </div>
    </div>
</div>

<style>
.auth-container {
    max-width: 500px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.settings-card {
    background: var(--surface);
    padding: 2rem;
    border-radius: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.settings-card h2 {
    color: var(--text);
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
    font-weight: 600;
    text-align: center;
}

.user-info, .password-change {
    margin: 2rem 0;
}

.user-info h3, .password-change h3 {
    color: var(--text);
    margin-bottom: 1rem;
    font-size: 1.25rem;
    font-weight: 600;
}

.user-info p {
    color: var(--text-light);
    margin: 0.5rem 0;
}

.user-info strong {
    color: var(--primary);
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

.error {
    color: var(--error);
    font-size: 0.875rem;
    margin-top: 0.5rem;
    padding: 0 1rem;
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
