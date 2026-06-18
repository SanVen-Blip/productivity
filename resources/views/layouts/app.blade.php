<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'SanvenDocs') — SanvenDocs</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 text-gray-900 antialiased">

    {{-- Navbar --}}
    <nav class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shadow-sm">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-blue-600 font-semibold text-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            SanvenDocs
        </a>

        <div class="flex items-center gap-4">
            @auth
                <span class="text-sm text-gray-500">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-sm text-gray-600 hover:text-red-500 transition-colors">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </nav>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Main content --}}
    <main class="@yield('main-class', 'max-w-6xl mx-auto px-4 py-8')">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
