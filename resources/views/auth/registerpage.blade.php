@extends('layouts.app')

@section('content')
<div class="auth-container">
    <div class="register-layout">
        <div class="auth-card">
            <h2>Register for GymBro</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password:</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>

                <button type="submit" id="submitBtn" disabled title="Please meet all password requirements">Register</button>
            </form>
        </div>

        <div class="requirements-box">
            <h3>Password Requirements</h3>
            <ul class="req-list">
                <li id="req-length"><i class="fas fa-circle"></i> At least 8 characters</li>
                <li id="req-uppercase"><i class="fas fa-circle"></i> One uppercase letter</li>
                <li id="req-lowercase"><i class="fas fa-circle"></i> One lowercase letter</li>
                <li id="req-number"><i class="fas fa-circle"></i> One number</li>
            </ul>
        </div>
    </div>
</div>

<script>
const passwordInput = document.getElementById('password');
const submitBtn = document.getElementById('submitBtn');
let requirements = {
    length: false,
    uppercase: false,
    lowercase: false,
    number: false
};

passwordInput.addEventListener('input', function() {
    const password = this.value;
    
    requirements.length = password.length >= 8;
    requirements.uppercase = /[A-Z]/.test(password);
    requirements.lowercase = /[a-z]/.test(password);
    requirements.number = /[0-9]/.test(password);
    
    Object.entries(requirements).forEach(([req, valid]) => {
        const element = document.getElementById(`req-${req}`);
        element.classList.toggle('valid', valid);
        element.classList.toggle('invalid', !valid);
    });
    
    submitBtn.disabled = !Object.values(requirements).every(Boolean);
});
</script>

<style>
.auth-container {
    max-width: 1200px;
    margin: 4rem auto;
    padding: 0 2rem;
}

.register-layout {
    display: flex;
    gap: 2rem;
    justify-content: center;
    align-items: flex-start;
}

.auth-card {
    background: var(--surface);
    padding: 2rem;
    border-radius: 1rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    min-width: 400px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.requirements-box {
    background: var(--surface);
    padding: 1.5rem;
    border-radius: 1rem;
    min-width: 300px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.auth-card h2, .requirements-box h3 {
    color: var(--text);
    margin-bottom: 1.5rem;
    font-weight: 600;
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

button[type="submit"]:hover:not(:disabled) {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
}

button[type="submit"]:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.req-list {
    list-style: none;
    padding: 0;
    margin: 1rem 0;
}

.req-list li {
    color: var(--text-light);
    margin: 0.75rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: color 0.2s;
}

.req-list li i {
    font-size: 0.75rem;
    color: var(--error);
    transition: color 0.2s;
}

.req-list li.valid {
    color: var(--success);
}

.req-list li.valid i {
    color: var(--success);
}
</style>
@endsection
