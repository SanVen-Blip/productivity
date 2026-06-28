@extends('layouts.app')

@section('title', 'Dashboard')

@section('main-class', 'flex h-[calc(100vh-57px)] overflow-hidden')

@section('content')

{{-- ── Sidebar ───────────────────────────────────────────────── --}}
<aside class="w-56 flex-shrink-0 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 flex flex-col overflow-y-auto hidden md:flex">
    <div class="p-4">
        {{-- New document with template dropdown --}}
        <div class="relative">
            <div class="flex rounded-lg overflow-hidden border border-blue-600 shadow-sm">
                <form method="POST" action="{{ route('documents.store', request()->only('folder')) }}" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-2 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        New Doc
                    </button>
                </form>
                <button onclick="document.getElementById('template-picker').classList.toggle('hidden')"
                    class="bg-blue-700 hover:bg-blue-800 text-white px-2 py-2 transition-colors border-l border-blue-500" title="Choose template">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>

            {{-- Template picker dropdown --}}
            <div id="template-picker" class="hidden absolute left-0 top-full mt-1 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg py-1 z-20">
                <p class="px-3 py-1.5 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Templates</p>
                @foreach([
                    ['blank',   '📄', 'Blank Document'],
                    ['meeting', '📋', 'Meeting Notes'],
                    ['todo',    '✅', 'To-Do List'],
                    ['project', '🚀', 'Project Brief'],
                    ['notes',   '⚡', 'Quick Notes'],
                ] as [$key, $icon, $label])
                <form method="POST" action="{{ route('documents.from-template') }}">
                    @csrf
                    <input type="hidden" name="template" value="{{ $key }}">
                    @if(request('folder'))<input type="hidden" name="folder" value="{{ request('folder') }}">@endif
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <span>{{ $icon }}</span>
                        {{ $label }}
                    </button>
                </form>
                @endforeach
            </div>
        </div>
    </div>

    <nav class="px-3 pb-4 space-y-0.5 text-sm flex-1">
        {{-- All docs --}}
        <a href="{{ route('dashboard') }}"
            class="sidebar-link {{ !request('folder') && !request('starred') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            All Documents
            <span class="ml-auto text-xs text-gray-400 dark:text-gray-500">{{ $stats['total'] }}</span>
        </a>

        {{-- Starred --}}
        <a href="{{ route('dashboard', ['starred' => 1]) }}"
            class="sidebar-link {{ request('starred') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
            Starred
            <span class="ml-auto text-xs text-gray-400 dark:text-gray-500">{{ $stats['starred'] }}</span>
        </a>

        {{-- Teams --}}
        <a href="{{ route('teams.index') }}" class="sidebar-link">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Teams
        </a>

        {{-- Folders --}}
        @if($folders->isNotEmpty())
            <div class="pt-3 pb-1 px-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Folders</div>
            @foreach($folders as $folder)
                <a href="{{ route('dashboard', ['folder' => $folder]) }}"
                    class="sidebar-link {{ request('folder') === $folder ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                    <span class="truncate">{{ $folder }}</span>
                </a>
            @endforeach
        @endif
    </nav>

    {{-- Stats panel --}}
    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Overview</p>
        <div class="space-y-1 text-xs text-gray-500 dark:text-gray-400">
            <div class="flex justify-between"><span>Total docs</span><span class="font-medium text-gray-700 dark:text-gray-300">{{ $stats['total'] }}</span></div>
            <div class="flex justify-between"><span>Starred</span><span class="font-medium text-gray-700 dark:text-gray-300">{{ $stats['starred'] }}</span></div>
            <div class="flex justify-between"><span>Folders</span><span class="font-medium text-gray-700 dark:text-gray-300">{{ $stats['folders'] }}</span></div>
        </div>
    </div>
</aside>

{{-- ── Main content ──────────────────────────────────────────── --}}
<div class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-950">
    <div class="max-w-6xl mx-auto px-4 py-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                    @if(request('starred')) ⭐ Starred
                    @elseif(request('folder')) 📁 {{ request('folder') }}
                    @else All Documents
                    @endif
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $documents->count() }} document{{ $documents->count() !== 1 ? 's' : '' }}</p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                {{-- Search --}}
                <form method="GET" action="{{ route('dashboard') }}" class="relative">
                    @if(request('folder'))<input type="hidden" name="folder" value="{{ request('folder') }}">@endif
                    @if(request('starred'))<input type="hidden" name="starred" value="1">@endif
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Search..."
                        class="pl-8 pr-8 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-44 bg-white dark:bg-gray-800 dark:text-white dark:placeholder-gray-500"/>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    @if(request('q'))
                        <a href="{{ route('dashboard', array_filter(['folder'=>request('folder'),'starred'=>request('starred')])) }}" class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </a>
                    @endif
                </form>

                {{-- Sort --}}
                <form method="GET" action="{{ route('dashboard') }}" id="sort-form">
                    @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                    @if(request('folder'))<input type="hidden" name="folder" value="{{ request('folder') }}">@endif
                    @if(request('starred'))<input type="hidden" name="starred" value="1">@endif
                    <select name="sort" onchange="document.getElementById('sort-form').submit()"
                        class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-800 dark:text-white cursor-pointer">
                        <option value="updated_at" {{ request('sort','updated_at')==='updated_at'?'selected':'' }}>Last modified</option>
                        <option value="created_at" {{ request('sort')==='created_at'?'selected':'' }}>Date created</option>
                        <option value="title"      {{ request('sort')==='title'?'selected':'' }}>Title A–Z</option>
                    </select>
                </form>

                {{-- Mobile new doc --}}
                <form method="POST" action="{{ route('documents.store') }}" class="md:hidden">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-1.5 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        New
                    </button>
                </form>
            </div>
        </div>

        {{-- Empty state --}}
        @if($documents->isEmpty())
        <div class="text-center py-20 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
            @if(request('q'))
                <p class="text-gray-500 dark:text-gray-400 font-medium mb-1">No results for "{{ request('q') }}"</p>
                <a href="{{ route('dashboard', array_filter(['folder'=>request('folder'),'starred'=>request('starred')])) }}" class="text-blue-600 text-sm hover:underline">Clear search</a>
            @elseif(request('starred'))
                <p class="text-gray-500 dark:text-gray-400 font-medium mb-1">No starred documents</p>
                <p class="text-gray-400 text-sm">Star a document from the dashboard to find it here quickly</p>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400 font-medium mb-4">No documents yet</p>
                <form method="POST" action="{{ route('documents.store') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Create First Document
                    </button>
                </form>
            @endif
        </div>

        {{-- Document grid --}}
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($documents as $doc)
            <div class="group bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-md transition-all duration-200 flex flex-col"
                 id="doc-card-{{ $doc->slug }}">

                {{-- Preview --}}
                <a href="{{ route('documents.edit', $doc->slug) }}" class="block p-4">
                    <div class="rounded-lg h-24 flex items-center justify-center mb-3 overflow-hidden relative
                        {{ $doc->starred ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-blue-50 dark:bg-blue-900/20' }}">
                        @if($doc->content)
                            <div class="absolute inset-0 p-2 text-[7px] leading-tight overflow-hidden pointer-events-none select-none text-gray-500 dark:text-gray-400">
                                {!! strip_tags(substr($doc->content, 0, 300)) !!}
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-{{ $doc->starred ? 'yellow' : 'blue' }}-50 dark:to-{{ $doc->starred ? 'yellow' : 'blue' }}-900/20"></div>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9 {{ $doc->starred ? 'text-yellow-300' : 'text-blue-300 dark:text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        @endif
                    </div>
                </a>

                {{-- Title + star --}}
                <div class="px-4 pb-1 flex items-start gap-1">
                    <span class="doc-title flex-1 font-medium text-gray-900 dark:text-white text-sm cursor-text rounded px-1 py-0.5 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors truncate"
                        data-slug="{{ $doc->slug }}"
                        title="Click to rename"
                        onclick="startRename(this)">{{ $doc->title }}</span>
                    <button onclick="toggleStar('{{ $doc->slug }}', this)" title="{{ $doc->starred ? 'Unstar' : 'Star' }}"
                        class="flex-shrink-0 mt-0.5 p-0.5 rounded transition-colors hover:scale-110"
                        data-starred="{{ $doc->starred ? 'true' : 'false' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $doc->starred ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </button>
                </div>
                <div class="px-5 pb-1">
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $doc->updated_at->diffForHumans() }}</p>
                    @if($doc->folder)
                        <p class="text-xs text-blue-500 dark:text-blue-400 mt-0.5">📁 {{ $doc->folder }}</p>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="border-t border-gray-100 dark:border-gray-700 mt-2 px-4 py-2 flex items-center justify-between">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                        {{ $doc->status === 'published' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                        {{ ucfirst($doc->status) }}
                    </span>

                    <div class="relative" id="menu-{{ $doc->slug }}">
                        <button onclick="toggleMenu('{{ $doc->slug }}')"
                            class="text-gray-300 dark:text-gray-600 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800 opacity-0 group-hover:opacity-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                        </button>

                        <div class="doc-menu hidden absolute right-0 bottom-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg py-1 w-48 z-20">
                            <a href="{{ route('documents.edit', $doc->slug) }}" class="menu-item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                Open
                            </a>
                            <button onclick="startRename(document.querySelector('[data-slug=\'{{ $doc->slug }}\']')); toggleMenu('{{ $doc->slug }}')" class="menu-item w-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                Rename
                            </button>
                            <button onclick="openMoveFolder('{{ $doc->slug }}', '{{ addslashes($doc->folder ?? '') }}')" class="menu-item w-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                                Move to Folder
                            </button>
                            <form method="POST" action="{{ route('documents.duplicate', $doc->slug) }}">
                                @csrf
                                <button type="submit" class="menu-item w-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                    Duplicate
                                </button>
                            </form>
                            <a href="{{ route('documents.export', $doc->slug) }}" target="_blank" class="menu-item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                Export / Print
                            </a>
                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                            <form method="POST" action="{{ route('documents.destroy', $doc->slug) }}"
                                onsubmit="return confirm('Delete \'{{ addslashes($doc->title) }}\'?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="menu-item w-full text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</div>

{{-- ── Move to Folder modal ──────────────────────────────────── --}}
<div id="folder-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-6 w-full max-w-sm">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Move to Folder</h3>
        <input type="text" id="folder-input"
            placeholder="Folder name (leave empty to remove)"
            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white mb-3"/>
        @if($folders->isNotEmpty())
            <div class="flex flex-wrap gap-1 mb-3">
                @foreach($folders as $f)
                    <button onclick="document.getElementById('folder-input').value='{{ $f }}'"
                        class="px-2 py-1 text-xs rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 hover:bg-blue-100 border border-blue-200 dark:border-blue-700 transition-colors">
                        {{ $f }}
                    </button>
                @endforeach
            </div>
        @endif
        <div class="flex gap-2 justify-end">
            <button onclick="closeMoveFolder()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">Cancel</button>
            <button onclick="submitMoveFolder()" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">Move</button>
        </div>
    </div>
</div>

{{-- ── Keyboard shortcut modal ───────────────────────────────── --}}
<div id="shortcut-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-gray-900 dark:text-white">Keyboard Shortcuts</h3>
            <button onclick="document.getElementById('shortcut-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="space-y-2 text-sm">
            @foreach([
                ['Ctrl + S',   'Save document'],
                ['Ctrl + B',   'Bold'],
                ['Ctrl + I',   'Italic'],
                ['Ctrl + U',   'Underline'],
                ['Ctrl + Z',   'Undo'],
                ['Ctrl + Y',   'Redo'],
                ['?',          'Show shortcuts (on dashboard)'],
            ] as [$key, $desc])
            <div class="flex items-center justify-between py-1.5 border-b border-gray-100 dark:border-gray-800">
                <span class="text-gray-600 dark:text-gray-400">{{ $desc }}</span>
                <kbd class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-xs font-mono">{{ $key }}</kbd>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let moveFolderSlug = null;

// ── Toast ──────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const colors = {
        success: 'bg-green-600',
        error:   'bg-red-600',
        info:    'bg-blue-600',
    };
    const toast = document.createElement('div');
    toast.className = `fixed bottom-5 right-5 z-50 flex items-center gap-2 px-4 py-3 rounded-xl text-white text-sm shadow-lg ${colors[type]} transition-all duration-300 translate-y-2 opacity-0`;
    toast.innerHTML = msg;
    document.body.appendChild(toast);
    requestAnimationFrame(() => { toast.classList.remove('translate-y-2', 'opacity-0'); });
    setTimeout(() => {
        toast.classList.add('translate-y-2', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Auto-show flash
@if(session('success'))
    showToast('{{ addslashes(session('success')) }}');
@endif

// ── Dropdown menu ──────────────────────────────────────────────────
function toggleMenu(slug) {
    const menu = document.querySelector(`#menu-${slug} .doc-menu`);
    document.querySelectorAll('.doc-menu').forEach(m => { if (m !== menu) m.classList.add('hidden'); });
    menu.classList.toggle('hidden');
}
document.addEventListener('click', (e) => {
    if (!e.target.closest('[id^="menu-"]')) {
        document.querySelectorAll('.doc-menu').forEach(m => m.classList.add('hidden'));
    }
    if (!e.target.closest('#template-picker') && !e.target.closest('button[onclick*="template-picker"]')) {
        document.getElementById('template-picker')?.classList.add('hidden');
    }
});

// ── Star toggle ────────────────────────────────────────────────────
async function toggleStar(slug, btn) {
    try {
        const res  = await fetch(`/documents/${slug}/star`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();
        const svg  = btn.querySelector('svg');
        if (data.starred) {
            svg.classList.add('text-yellow-400', 'fill-yellow-400');
            svg.classList.remove('text-gray-300');
            btn.dataset.starred = 'true';
            showToast('⭐ Document starred');
        } else {
            svg.classList.remove('text-yellow-400', 'fill-yellow-400');
            svg.classList.add('text-gray-300');
            btn.dataset.starred = 'false';
            showToast('Removed from starred', 'info');
        }
    } catch {
        showToast('Failed to update star', 'error');
    }
}

// ── Inline rename ──────────────────────────────────────────────────
function startRename(el) {
    if (el.querySelector('input')) return;
    const slug     = el.dataset.slug;
    const oldTitle = el.textContent.trim();
    const input    = document.createElement('input');
    input.type      = 'text';
    input.value     = oldTitle;
    input.className = 'w-full text-sm font-medium text-gray-900 dark:text-white border border-blue-400 rounded px-1 py-0.5 focus:outline-none bg-white dark:bg-gray-800';
    el.textContent  = '';
    el.appendChild(input);
    input.focus(); input.select();

    async function commit() {
        const newTitle = input.value.trim() || oldTitle;
        el.textContent = newTitle;
        el.dataset.slug = slug;
        if (newTitle === oldTitle) return;
        try {
            const res  = await fetch(`/documents/${slug}/rename`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify({ title: newTitle }),
            });
            const data = await res.json();
            el.textContent = data.title;
            showToast('Document renamed');
        } catch { el.textContent = oldTitle; }
    }
    input.addEventListener('blur', commit);
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter')  { e.preventDefault(); input.blur(); }
        if (e.key === 'Escape') { el.textContent = oldTitle; }
    });
}

// ── Move to folder ─────────────────────────────────────────────────
function openMoveFolder(slug, currentFolder) {
    moveFolderSlug = slug;
    document.getElementById('folder-input').value = currentFolder || '';
    document.getElementById('folder-modal').classList.remove('hidden');
    document.querySelectorAll('.doc-menu').forEach(m => m.classList.add('hidden'));
    setTimeout(() => document.getElementById('folder-input').focus(), 50);
}
function closeMoveFolder() {
    document.getElementById('folder-modal').classList.add('hidden');
    moveFolderSlug = null;
}
async function submitMoveFolder() {
    const folder = document.getElementById('folder-input').value.trim();
    try {
        await fetch(`/documents/${moveFolderSlug}/folder`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ folder: folder || null }),
        });
        showToast(folder ? `Moved to "${folder}"` : 'Removed from folder');
        closeMoveFolder();
        setTimeout(() => location.reload(), 800);
    } catch {
        showToast('Failed to move document', 'error');
    }
}
document.getElementById('folder-input')?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') submitMoveFolder();
    if (e.key === 'Escape') closeMoveFolder();
});
document.getElementById('folder-modal')?.addEventListener('click', (e) => {
    if (e.target === document.getElementById('folder-modal')) closeMoveFolder();
});

// ── Keyboard shortcuts ─────────────────────────────────────────────
document.addEventListener('keydown', (e) => {
    if (e.key === '?' && !e.target.matches('input, textarea, [contenteditable]')) {
        document.getElementById('shortcut-modal').classList.toggle('hidden');
    }
    if (e.key === 'Escape') {
        document.getElementById('shortcut-modal').classList.add('hidden');
        closeMoveFolder();
    }
});
document.getElementById('shortcut-modal')?.addEventListener('click', (e) => {
    if (e.target === document.getElementById('shortcut-modal'))
        document.getElementById('shortcut-modal').classList.add('hidden');
});
</script>
@endpush
