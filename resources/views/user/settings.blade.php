@extends('layouts.app')

@section('content')
<div class="container">
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
.settings-card {
    max-width: 600px;
    margin: 2rem auto;
}

.user-info, .password-change {
    margin: 2rem 0;
}

.settings-button {
    padding: 0.5rem 1rem;
    background: var(--accent-color);
    border-radius: 4px;
    margin-left: 1rem;
}
</style>
@endsection
