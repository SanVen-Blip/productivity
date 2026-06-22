@extends('layouts.app')

@section('title', $document->title)

@section('main-class', 'h-[calc(100vh-57px)] flex flex-col')

@section('content')
<div class="flex flex-col h-full" id="editor-app" data-slug="{{ $document->slug }}">

    {{-- ── Toolbar ─────────────────────────────────────────────── --}}
    <div class="bg-white border-b border-gray-200 px-3 py-2 flex flex-wrap items-center gap-1 flex-shrink-0">

        {{-- Back --}}
        <a href="{{ route('dashboard') }}"
            class="text-gray-400 hover:text-gray-700 transition-colors p-1 rounded hover:bg-gray-100 mr-1"
            title="Back to Dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>

        {{-- Title --}}
        <input
            type="text"
            id="doc-title"
            value="{{ $document->title }}"
            class="w-44 text-sm font-medium text-gray-800 border border-transparent rounded px-2 py-1 focus:outline-none focus:border-blue-300 focus:bg-blue-50 transition-colors"
            placeholder="Document title..."
        />

        <div class="h-5 w-px bg-gray-200 mx-1"></div>

        {{-- Font size --}}
        <select id="font-size-select" onchange="changeFontSize(this.value)"
            class="toolbar-select text-xs">
            <option value="">Size</option>
            <option value="1">Small</option>
            <option value="3">Normal</option>
            <option value="4">Large</option>
            <option value="5">X-Large</option>
            <option value="6">2X-Large</option>
        </select>

        <div class="h-5 w-px bg-gray-200 mx-1"></div>

        {{-- Text format --}}
        <button onclick="fmt('bold')"      title="Bold (Ctrl+B)"      class="toolbar-btn font-bold" id="btn-bold">B</button>
        <button onclick="fmt('italic')"    title="Italic (Ctrl+I)"    class="toolbar-btn italic"    id="btn-italic">I</button>
        <button onclick="fmt('underline')" title="Underline (Ctrl+U)" class="toolbar-btn underline" id="btn-underline">U</button>
        <button onclick="fmt('strikeThrough')" title="Strikethrough"  class="toolbar-btn line-through text-xs">S</button>

        <div class="h-5 w-px bg-gray-200 mx-1"></div>

        {{-- Headings --}}
        <button onclick="fmtBlock('h1')" title="Heading 1" class="toolbar-btn text-xs font-semibold">H1</button>
        <button onclick="fmtBlock('h2')" title="Heading 2" class="toolbar-btn text-xs font-semibold">H2</button>
        <button onclick="fmtBlock('h3')" title="Heading 3" class="toolbar-btn text-xs font-semibold">H3</button>
        <button onclick="fmtBlock('p')"  title="Paragraph" class="toolbar-btn text-xs">¶</button>

        <div class="h-5 w-px bg-gray-200 mx-1"></div>

        {{-- Alignment --}}
        <button onclick="fmt('justifyLeft')"   title="Align Left"    class="toolbar-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h14"/></svg>
        </button>
        <button onclick="fmt('justifyCenter')" title="Align Center"  class="toolbar-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M7 12h10M5 18h14"/></svg>
        </button>
        <button onclick="fmt('justifyRight')"  title="Align Right"   class="toolbar-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M10 12h10M6 18h14"/></svg>
        </button>
        <button onclick="fmt('justifyFull')"   title="Justify"       class="toolbar-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        <div class="h-5 w-px bg-gray-200 mx-1"></div>

        {{-- Lists --}}
        <button onclick="fmt('insertUnorderedList')" title="Bullet List"   class="toolbar-btn text-xs">• List</button>
        <button onclick="fmt('insertOrderedList')"   title="Numbered List" class="toolbar-btn text-xs">1. List</button>

        <div class="h-5 w-px bg-gray-200 mx-1"></div>

        {{-- Link --}}
        <button onclick="insertLink()" title="Insert Link" class="toolbar-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
        </button>

        {{-- Undo / Redo --}}
        <div class="h-5 w-px bg-gray-200 mx-1"></div>
        <button onclick="document.execCommand('undo')" title="Undo (Ctrl+Z)" class="toolbar-btn text-xs">↩</button>
        <button onclick="document.execCommand('redo')" title="Redo (Ctrl+Y)" class="toolbar-btn text-xs">↪</button>

        {{-- Export --}}
        <div class="h-5 w-px bg-gray-200 mx-1"></div>
        <a href="{{ route('documents.export', $document->slug) }}" target="_blank"
            class="toolbar-btn text-xs flex items-center gap-1" title="Export / Print">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Export
        </a>

        {{-- Save status --}}
        <div class="ml-auto flex items-center gap-3">
            <div id="word-count" class="text-xs text-gray-400 hidden sm:block">0 words</div>

            <span id="save-status" class="text-xs text-gray-400 flex items-center gap-1">
                @if($document->last_saved_at)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Saved {{ $document->last_saved_at->diffForHumans() }}
                @else
                    Not saved yet
                @endif
            </span>

            <button id="save-btn" onclick="saveDocument()"
                class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap">
                Save
            </button>
        </div>
    </div>

    {{-- ── Editor body ─────────────────────────────────────────── --}}
    <div class="flex-1 overflow-y-auto bg-gray-100">
        <div class="max-w-4xl mx-auto my-8 bg-white shadow-sm rounded-xl min-h-[700px]">
            <div
                id="editor-content"
                contenteditable="true"
                spellcheck="true"
                class="min-h-[700px] p-12 focus:outline-none"
                data-placeholder="Start typing your document..."
            >{!! $document->content !!}</div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const slug        = document.getElementById('editor-app').dataset.slug;
const editor      = document.getElementById('editor-content');
const titleInput  = document.getElementById('doc-title');
const saveStatus  = document.getElementById('save-status');
const wordCountEl = document.getElementById('word-count');

let saveTimer = null;
let isDirty   = false;

// ── Format commands ────────────────────────────────────────────────
function fmt(command) {
    document.execCommand(command, false, null);
    editor.focus();
    markDirty();
    updateToolbarState();
}

function fmtBlock(tag) {
    document.execCommand('formatBlock', false, tag);
    editor.focus();
    markDirty();
}

function changeFontSize(size) {
    if (!size) return;
    document.execCommand('fontSize', false, size);
    editor.focus();
    markDirty();
}

function insertLink() {
    const url = prompt('Enter URL:', 'https://');
    if (url) {
        document.execCommand('createLink', false, url);
        editor.focus();
        markDirty();
    }
}

// ── Toolbar active state ───────────────────────────────────────────
function updateToolbarState() {
    ['bold', 'italic', 'underline'].forEach(cmd => {
        const btn = document.getElementById('btn-' + cmd);
        if (btn) {
            btn.classList.toggle('bg-blue-100', document.queryCommandState(cmd));
            btn.classList.toggle('text-blue-700', document.queryCommandState(cmd));
        }
    });
}

document.addEventListener('selectionchange', updateToolbarState);

// ── Word count ─────────────────────────────────────────────────────
function updateWordCount() {
    const text  = editor.innerText.trim();
    const words = text ? text.split(/\s+/).filter(Boolean).length : 0;
    const chars = text.length;
    wordCountEl.textContent = `${words} word${words !== 1 ? 's' : ''} · ${chars} char${chars !== 1 ? 's' : ''}`;
}

// ── Dirty tracking ─────────────────────────────────────────────────
function markDirty() {
    isDirty = true;
    saveStatus.innerHTML = '<span class="text-yellow-500">Unsaved changes</span>';
    clearTimeout(saveTimer);
    saveTimer = setTimeout(saveDocument, 3000);
    updateWordCount();
}

editor.addEventListener('input', markDirty);
titleInput.addEventListener('input', markDirty);

// ── Save ───────────────────────────────────────────────────────────
async function saveDocument() {
    if (!isDirty) return;
    clearTimeout(saveTimer);

    const saveBtn = document.getElementById('save-btn');
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving…';
    saveStatus.innerHTML = '<span class="text-gray-400">Saving…</span>';

    try {
        const res = await fetch(`/documents/${slug}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                title:   titleInput.value.trim() || 'Untitled Document',
                content: editor.innerHTML,
            }),
        });

        const data = await res.json();

        if (data.saved) {
            isDirty = false;
            saveStatus.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Saved ${data.last_saved_at}`;
        }
    } catch (err) {
        saveStatus.innerHTML = '<span class="text-red-500">Save failed — retrying…</span>';
        saveTimer = setTimeout(saveDocument, 5000);
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Save';
    }
}

// ── Keyboard shortcuts ─────────────────────────────────────────────
document.addEventListener('keydown', (e) => {
    const mod = e.ctrlKey || e.metaKey;
    if (mod && e.key === 's') { e.preventDefault(); saveDocument(); }
    if (mod && e.key === 'b') { e.preventDefault(); fmt('bold'); }
    if (mod && e.key === 'i') { e.preventDefault(); fmt('italic'); }
    if (mod && e.key === 'u') { e.preventDefault(); fmt('underline'); }
});

// ── Warn on unsaved changes ────────────────────────────────────────
window.addEventListener('beforeunload', (e) => {
    if (isDirty) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// ── Init word count ────────────────────────────────────────────────
updateWordCount();
</script>
@endpush
