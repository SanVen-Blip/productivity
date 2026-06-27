<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Error') — SanvenDocs</title>
    @vite(['resources/css/app.css'])
    <script>
        if (localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-950 flex items-center justify-center antialiased">
    <div class="text-center px-4 max-w-md">
        <div class="text-8xl font-black text-blue-100 dark:text-blue-900/40 mb-2 select-none leading-none">
            @yield('code', '?')
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">@yield('heading')</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-8 text-sm">@yield('message')</p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ url('/dashboard') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Go to Dashboard
            </a>
            <button onclick="history.back()"
               class="inline-flex items-center gap-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium px-5 py-2.5 rounded-xl transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Go Back
            </button>
        </div>
        <p class="mt-10 text-xs text-gray-400 dark:text-gray-600">
            <a href="{{ url('/') }}" class="hover:text-blue-500 transition-colors">SanvenDocs</a>
        </p>
    </div>
</body>
</html>
