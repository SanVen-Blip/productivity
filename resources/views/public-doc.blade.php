<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ $document->title }} — SanvenDocs</title>
    <meta name="description" content="Shared document: {{ $document->title }}"/>
    @vite(['resources/css/app.css'])
    <script>
        if (localStorage.getItem('theme') === 'dark' ||
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-950 min-h-full antialiased">

    {{-- Top bar --}}
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-6 py-3 flex items-center justify-between shadow-sm">
        <a href="{{ route('login') }}" class="flex items-center gap-2 text-blue-600 font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            SanvenDocs
        </a>
        <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500 dark:text-gray-400 hidden sm:block">
                Shared by {{ $document->user->name }}
            </span>
            <button onclick="window.print()"
                class="inline-flex items-center gap-1.5 text-xs bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <a href="{{ route('register') }}"
                class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition-colors">
                Create free account
            </a>
        </div>
    </div>

    {{-- Document --}}
    <div class="max-w-4xl mx-auto my-10 px-4">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm px-12 py-14 min-h-[600px]">

            {{-- Header --}}
            <div class="mb-8 pb-6 border-b border-gray-100 dark:border-gray-800">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $document->title }}</h1>
                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-400 dark:text-gray-500">
                    <span>By {{ $document->user->name }}</span>
                    <span>·</span>
                    <span>{{ $document->last_saved_at ? $document->last_saved_at->format('F j, Y') : $document->created_at->format('F j, Y') }}</span>
                    @if($document->tags)
                        @foreach($document->tags as $tag)
                            <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full text-xs">{{ $tag }}</span>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Content --}}
            <div id="editor-content" class="prose-view dark:text-gray-100">
                {!! $document->content !!}
            </div>
        </div>

        {{-- Footer CTA --}}
        <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Made with <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">SanvenDocs</a>
            &nbsp;·&nbsp;
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Create your own workspace →</a>
        </div>
    </div>

</body>
</html>
