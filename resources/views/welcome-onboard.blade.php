@extends('layouts.app')

@section('title', 'Welcome to SanvenDocs')

@section('content')
<div class="max-w-2xl mx-auto text-center py-12 px-4">

    {{-- Hero --}}
    <div class="mb-10">
        <div class="inline-flex items-center justify-center h-20 w-20 rounded-3xl bg-blue-600 shadow-lg mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">
            Welcome to SanvenDocs, {{ Auth::user()->name }}! 👋
        </h1>
        <p class="text-gray-500 dark:text-gray-400 text-base leading-relaxed">
            Your personal productivity workspace is ready. Here's everything you can do:
        </p>
    </div>

    {{-- Features grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10 text-left">
        @foreach([
            ['📝', 'Rich Text Editor',     'Bold, italic, headings, lists, tables, and images — everything you need to write.'],
            ['📁', 'Folders & Stars',      'Organize documents into folders and star your favorites for quick access.'],
            ['🕐', 'Version History',      'Every save creates a version snapshot. Restore any previous version anytime.'],
            ['🔗', 'Share Publicly',       'Toggle a public link to share any document with anyone — no login required.'],
            ['🌙', 'Dark Mode',            'Click the moon icon in the navbar to switch to dark mode.'],
            ['📋', 'Templates',            'Start fast with Meeting Notes, To-Do List, Project Brief, and more.'],
        ] as [$icon, $title, $desc])
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex gap-3">
            <span class="text-2xl flex-shrink-0 mt-0.5">{{ $icon }}</span>
            <div>
                <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $title }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-relaxed">{{ $desc }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- CTA --}}
    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
        <form method="POST" action="{{ route('documents.from-template') }}">
            @csrf
            <input type="hidden" name="template" value="blank">
            <button type="submit"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-xl transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Create First Document
            </button>
        </form>
        <a href="{{ route('dashboard') }}"
            class="inline-flex items-center gap-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium px-6 py-3 rounded-xl transition-colors">
            Go to Dashboard
        </a>
    </div>

    {{-- Skip --}}
    <p class="mt-6 text-xs text-gray-400 dark:text-gray-600">
        You can always access this info from
        <a href="{{ route('profile.edit') }}" class="text-blue-500 hover:underline">Profile Settings</a>
    </p>

</div>
@endsection
