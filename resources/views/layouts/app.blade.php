<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GymBro') }}</title>    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css'])
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #1e293b;
            --accent: #f43f5e;
            --background: #0f172a;
            --surface: #1e293b;
            --surface-light: #334155;
            --text: #f8fafc;
            --text-light: #cbd5e1;
            --text-dark: #1e293b;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--background) 0%, var(--secondary) 100%);
            color: var(--text);
            min-height: 100vh;
        }

        * {
            transition: background-color 0.2s, border-color 0.2s, color 0.2s, transform 0.2s, box-shadow 0.2s;
        }

        .app-header {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            height: 4rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .nav-link {
            position: relative;
            padding: 0.75rem 1rem;
            color: var(--text-light);
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .nav-link.active {
            color: var(--primary);
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary);
            border-radius: 2px;
        }

        .nav-link-form {
            margin: 0;
            padding: 0;
        }

        .nav-link-button {
            color: var(--text-light);
            font-weight: 500;
            padding: 0.75rem 1rem;
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .nav-link-button:hover {
            color: var(--primary);
        }

        .brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            outline: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .card {
            background: var(--surface);
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
        }

        input, select, textarea {
            background: var(--surface-light);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.2);
            color: var(--error);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.2);
            color: var(--warning);
        }

        .alert-info {
            background: rgba(99, 102, 241, 0.1);
            border-color: rgba(99, 102, 241, 0.2);
            color: var(--primary);
        }

        .page-transition {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeIn 0.3s ease forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hover-lift {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
        }

        .ripple {
            position: relative;
            overflow: hidden;
        }

        .ripple::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: width 0.6s ease-out, height 0.6s ease-out, opacity 0.6s ease-out;
        }

        .ripple:active::after {
            width: 200%;
            height: 200%;
            opacity: 1;
            transition: 0s;
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
        }
    </style>
</head>
<body>
    <header class="app-header">
        <div class="nav-container">        <a href="{{ route('homepage') }}" class="brand">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M6.5 6.5h11v11h-11z"></path>
                <path d="M3 3l3.5 3.5M21 3l-3.5 3.5M3 21l3.5-3.5M21 21l-3.5-3.5"></path>
            </svg>
            GymBro
        </a>            <nav class="nav-menu">
                @auth
                    @can('admin')
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Admin Dashboard</a>
                    @else
                        <a href="{{ route('weekplan') }}" class="nav-link {{ request()->routeIs('weekplan') ? 'active' : '' }}">Week Plan</a>
                        <a href="{{ route('friends') }}" class="nav-link {{ request()->routeIs('friends') ? 'active' : '' }}">Friends</a>
                        <a href="{{ route('settings') }}" class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}">Settings</a>
                    @endcan
                    <form method="POST" action="{{ route('logout') }}" class="nav-link-form">
                        @csrf
                        <button type="submit" class="nav-link-button">Log Out</button>
                    </form>
                @else
                    <div class="nav-buttons">
                        <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-outline">Register</a>
                    </div>
                @endauth
            </nav>
        </div>
    </header>

    <main class="container">
        @yield('content')
    </main>
</body>
</html>
