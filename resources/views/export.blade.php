<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $document->title }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .doc-paper {
                box-shadow: none !important;
                margin: 0 !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- Print toolbar --}}
    <div class="no-print bg-white border-b border-gray-200 px-6 py-3 flex items-center gap-4 shadow-sm">
        <a href="{{ route('documents.edit', $document->slug) }}"
            class="text-gray-400 hover:text-gray-700 transition-colors"
            title="Back to editor">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <span class="text-sm font-medium text-gray-700 flex-1">{{ $document->title }}</span>
        <button
            onclick="window.print()"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print / Save as PDF
        </button>
    </div>

    {{-- Document paper --}}
    <div class="max-w-4xl mx-auto my-8 px-4">
        <div class="doc-paper bg-white shadow-md rounded-lg px-16 py-14 min-h-[842px]">

            {{-- Doc header --}}
            <div class="mb-10 pb-6 border-b border-gray-100">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $document->title }}</h1>
                <p class="text-sm text-gray-400">
                    Last saved: {{ $document->last_saved_at ? $document->last_saved_at->format('F j, Y \a\t g:i A') : 'Never' }}
                </p>
            </div>

            {{-- Content --}}
            <div class="prose prose-lg max-w-none text-gray-800">
                {!! $document->content !!}
            </div>
        </div>
    </div>

</body>
</html>
