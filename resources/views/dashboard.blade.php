@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">My Documents</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $documents->count() }} document{{ $documents->count() !== 1 ? 's' : '' }}</p>
    </div>

    <div class="flex items-center gap-3">
        {{-- Search --}}
        <form method="GET" action="{{ route('dashboard') }}" class="relative">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search documents..."
                class="pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-56 bg-white"
            />
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            @if(request('q'))
                <a href="{{ route('dashboard') }}" class="absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            @endif
        </form>

        {{-- New doc button --}}
        <form method="POST" action="{{ route('documents.store') }}">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                New Document
            </button>
        </form>
    </div>
</div>

@if($documents->isEmpty())
    {{-- Empty state --}}
    <div class="text-center py-24 bg-white rounded-2xl border border-dashed border-gray-300">
        @if(request('q'))
            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="text-gray-600 font-medium mb-1">No results for "{{ request('q') }}"</h3>
            <p class="text-gray-400 text-sm mb-4">Try a different search term</p>
            <a href="{{ route('dashboard') }}" class="text-blue-600 text-sm hover:underline">Clear search</a>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-gray-600 font-medium mb-1">No documents yet</h3>
            <p class="text-gray-400 text-sm mb-6">Create your first document to get started</p>
            <form method="POST" action="{{ route('documents.store') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Document
                </button>
            </form>
        @endif
    </div>
@else
    {{-- Document grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($documents as $doc)
        <div class="group bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col"
             id="doc-card-{{ $doc->slug }}">

            {{-- Clickable preview area --}}
            <a href="{{ route('documents.edit', $doc->slug) }}" class="flex-1 p-5 block">
                <div class="bg-blue-50 rounded-lg h-28 flex items-center justify-center mb-4 overflow-hidden relative">
                    @if($doc->content)
                        <div class="absolute inset-0 p-3 text-[8px] text-gray-400 leading-tight overflow-hidden pointer-events-none select-none">
                            {!! strip_tags($doc->content) !!}
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-blue-50"></div>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    @endif
                </div>
            </a>

            {{-- Title (inline rename) --}}
            <div class="px-5 pb-1">
                <div class="flex items-center gap-1 group/title">
                    <span
                        class="doc-title font-medium text-gray-900 text-sm truncate flex-1 cursor-text rounded px-1 py-0.5 hover:bg-gray-100 transition-colors"
                        data-slug="{{ $doc->slug }}"
                        title="Click to rename"
                        onclick="startRename(this)"
                    >{{ $doc->title }}</span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5 px-1">{{ $doc->updated_at->diffForHumans() }}</p>
            </div>

            {{-- Footer actions --}}
            <div class="border-t border-gray-100 mt-2 px-4 py-2 flex items-center justify-between">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                    {{ $doc->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ ucfirst($doc->status) }}
                </span>

                {{-- Action menu --}}
                <div class="relative" id="menu-{{ $doc->slug }}">
                    <button onclick="toggleMenu('{{ $doc->slug }}')"
                        class="text-gray-300 hover:text-gray-600 transition-colors p-1 rounded hover:bg-gray-100 opacity-0 group-hover:opacity-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                    </button>

                    {{-- Dropdown --}}
                    <div class="doc-menu hidden absolute right-0 bottom-8 bg-white border border-gray-200 rounded-xl shadow-lg py-1 w-44 z-20">
                        <a href="{{ route('documents.edit', $doc->slug) }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Open
                        </a>
                        <button onclick="startRename(document.querySelector('[data-slug=\'{{ $doc->slug }}\']')); toggleMenu('{{ $doc->slug }}')"
                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Rename
                        </button>
                        <form method="POST" action="{{ route('documents.duplicate', $doc->slug) }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Duplicate
                            </button>
                        </form>
                        <a href="{{ route('documents.export', $doc->slug) }}" target="_blank"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Export / Print
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('documents.destroy', $doc->slug) }}"
                            onsubmit="return confirm('Delete \'{{ addslashes($doc->title) }}\'? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
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

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── Dropdown menu ──────────────────────────────────────────────────
function toggleMenu(slug) {
    const menu = document.querySelector(`#menu-${slug} .doc-menu`);
    // Close all others first
    document.querySelectorAll('.doc-menu').forEach(m => {
        if (m !== menu) m.classList.add('hidden');
    });
    menu.classList.toggle('hidden');
}

// Close menus when clicking outside
document.addEventListener('click', (e) => {
    if (!e.target.closest('[id^="menu-"]')) {
        document.querySelectorAll('.doc-menu').forEach(m => m.classList.add('hidden'));
    }
});

// ── Inline rename ──────────────────────────────────────────────────
function startRename(el) {
    if (el.querySelector('input')) return; // already editing

    const slug     = el.dataset.slug;
    const oldTitle = el.textContent.trim();

    const input = document.createElement('input');
    input.type  = 'text';
    input.value = oldTitle;
    input.className = 'w-full text-sm font-medium text-gray-900 border border-blue-400 rounded px-1 py-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white';

    el.textContent = '';
    el.appendChild(input);
    input.focus();
    input.select();

    async function commitRename() {
        const newTitle = input.value.trim() || oldTitle;
        el.textContent = newTitle;
        el.dataset.slug = slug;

        if (newTitle === oldTitle) return;

        try {
            const res = await fetch(`/documents/${slug}/rename`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ title: newTitle }),
            });
            const data = await res.json();
            el.textContent = data.title;
        } catch (err) {
            el.textContent = oldTitle;
        }
    }

    input.addEventListener('blur', commitRename);
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') { e.preventDefault(); input.blur(); }
        if (e.key === 'Escape') { el.textContent = oldTitle; }
    });
}
</script>
@endpush
