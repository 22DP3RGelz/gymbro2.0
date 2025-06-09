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
    background: var(--secondary-gray);
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    min-width: 400px;
}

.requirements-box {
    background: var(--secondary-gray);
    padding: 1.5rem;
    border-radius: 8px;
    min-width: 300px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-light);
}

.form-group input {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid var(--accent-color);
    border-radius: 4px;
    background: var(--primary-gray);
    color: var(--text-light);
    margin-top: 0.5rem;
}

button[type="submit"] {
    width: 100%;
    margin-top: 2rem;
    padding: 1rem;
}

.req-list {
    list-style: none;
    padding: 0;
    margin: 1rem 0;
}

.req-list li {
    color: var(--text-light);
    margin: 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.req-list li.valid {
    color: #2ecc71;
}

.req-list li.invalid {
    color: #e74c3c;
}

button[type="submit"]:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
@endsection
