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

        <div class="flex items-center gap-2">
            @auth
                {{-- Profile dropdown --}}
                <div class="relative" id="nav-profile-menu">
                    <button onclick="document.getElementById('nav-profile-dropdown').classList.toggle('hidden')"
                        class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="h-7 w-7 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold select-none">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden sm:block font-medium">{{ Auth::user()->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="nav-profile-dropdown"
                        class="hidden absolute right-0 top-10 bg-white border border-gray-200 rounded-xl shadow-lg py-1 w-44 z-30">
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profile Settings
                        </a>
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            My Documents
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>

        <script>
        // Close nav dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const menu = document.getElementById('nav-profile-menu');
            if (menu && !menu.contains(e.target)) {
                document.getElementById('nav-profile-dropdown')?.classList.add('hidden');
            }
        });
        </script>
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
