<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GymBro - @yield('title', 'Home')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        :root {
            --primary-gray: #2c3e50;
            --secondary-gray: #34495e;
            --accent-color: #3498db;
            --text-light: #ecf0f1;
            --text-dark: #2c3e50;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: var(--primary-gray);
            color: var(--text-light);
            line-height: 1.6;
        }

        header {
            background: var(--secondary-gray);
            padding: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        nav a, button.nav-link {
            background: transparent;
            color: var(--text-light);
            text-decoration: none;
            padding: 0.5rem 1rem;
            margin: 0 0.5rem;
            border: 2px solid var(--accent-color);
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        nav a:hover, button.nav-link:hover {
            background: var(--accent-color);
            color: var(--text-light);
            transform: translateY(-2px);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            background: var(--secondary-gray);
            border-radius: 8px;
            padding: 2rem;
            margin: 1rem 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            background: var(--secondary-gray);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--primary-gray);
        }

        th {
            background: var(--accent-color);
            color: var(--text-light);
        }

        button {
            background: var(--accent-color);
            color: var(--text-light);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
        }

        .alert-success {
            background: #2ecc71;
            color: white;
        }

        .alert-danger {
            background: #e74c3c;
            color: white;
        }

        /* Add new styles for profile section */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 2rem;
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.5rem 1rem;
            background: var(--primary-gray);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .profile-info {
            text-align: left;
        }

        .profile-name {
            font-weight: bold;
            color: var(--accent-color);
            margin: 0;
        }

        .profile-streak {
            margin: 0;
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .main-nav {
            flex: 1;
            text-align: right;
        }

        .settings-button {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border: 2px solid var(--accent-color);
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .settings-button:hover {
            background: var(--accent-color);
            color: var(--text-light);
            transform: translateY(-2px);
        }

        /* Search Results Styles */
        .search-results {
            position: absolute;
            background: var(--secondary-gray);
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
        }

        .search-result-item {
            padding: 0.8rem;
            border-bottom: 1px solid var(--primary-gray);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .search-result-item:hover {
            background: var(--primary-gray);
            cursor: pointer;
        }

        .add-friend-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
    <script src="{{ asset('js/search.js') }}" defer></script>
</head>
<body>
    <header>
        <div class="header-container">
            @auth
                <div class="profile-section">
                    <div class="profile-info">
                        <p class="profile-name">{{ Auth::user()->name }}</p>
                        <p class="profile-streak">Streak: {{ Auth::user()->streak ?? 0 }} days</p>
                    </div>
                    <a href="{{ route('settings') }}" class="settings-button">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                </div>
            @endauth
            
            <nav class="main-nav">
                @auth
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('adminspage') }}">Admin Dashboard</a>
                    @else
                        <a href="{{ route('weekplan') }}">Week Plan</a>
                        <a href="{{ route('friends') }}">Friends</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="display: inline">
                        @csrf
                        <button type="submit" class="nav-link">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ route('register') }}">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
