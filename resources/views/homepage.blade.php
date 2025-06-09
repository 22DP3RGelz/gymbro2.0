@extends('layouts.app')

@section('content')
<div class="hero-section">
    <h1>Welcome to GymBro</h1>
    <p class="hero-text">Plan your week, track your streaks, and connect with friends!</p>
    <p class="hero-subtext">Join GymBro today and achieve your fitness goals!</p>
</div>

<style>
.hero-section {
    padding: 4rem 2rem;
    text-align: center;
    background: var(--secondary-gray);
    margin: 2rem auto;
    border-radius: 8px;
    max-width: 800px;
}

.hero-text {
    font-size: 1.8rem;
    margin: 2rem 0;
    color: var(--text-light);
}

.hero-subtext {
    font-size: 1.4rem;
    color: var(--accent-color);
}
</style>
@endsection