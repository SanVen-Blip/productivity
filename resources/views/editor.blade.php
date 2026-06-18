@extends('layouts.app')

@section('title', $document->title)

@section('main-class', 'h-[calc(100vh-57px)] flex flex-col')

@section('content')
<div class="flex flex-col h-full" id="editor-app" data-slug="{{ $document->slug }}">

    {{-- Editor toolbar --}}
    <div class="bg-white border-b border-gray-200 px-4 py-2 flex items-center gap-2 flex-shrink-0">
        {{-- Back --}}
        <a href="{{ route('dashboard') }}"
            class="text-gray-400 hover:text-gray-700 transition-colors mr-2"
            title="Back to Dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>

        {{-- Title input --}}
        <input
            type="text"
            id="doc-title"
            value="{{ $document->title }}"
            class="flex-1 max-w-sm text-sm font-medium text-gray-800 border border-transparent rounded px-2 py-1 focus:outline-none focus:border-blue-300 focus:bg-blue-50 transition-colors"
            placeholder="Document title..."
        />

        {{-- Divider --}}
        <div class="h-5 w-px bg-gray-200 mx-1"></div>

        {{-- Format buttons --}}
        <div class="flex items-center gap-1">
            <button onclick="formatText('bold')" title="Bold (Ctrl+B)"
                class="toolbar-btn font-bold">B</button>
            <button onclick="formatText('italic')" title="Italic (Ctrl+I)"
                class="toolbar-btn italic">I</button>
            <button onclick="formatText('underline')" title="Underline (Ctrl+U)"
                class="toolbar-btn underline">U</button>
            <div class="h-5 w-px bg-gray-200 mx-1"></div>
            <button onclick="formatBlock('h1')" title="Heading 1"
                class="toolbar-btn text-xs">H1</button>
            <button onclick="formatBlock('h2')" title="Heading 2"
                class="toolbar-btn text-xs">H2</button>
            <button onclick="formatBlock('p')" title="Paragraph"
                class="toolbar-btn text-xs">¶</button>
            <div class="h-5 w-px bg-gray-200 mx-1"></div>
            <button onclick="formatText('insertUnorderedList')" title="Bullet List"
                class="toolbar-btn text-xs">• List</button>
            <button onclick="formatText('insertOrderedList')" title="Numbered List"
                class="toolbar-btn text-xs">1. List</button>
        </div>

        {{-- Save status --}}
        <div class="ml-auto flex items-center gap-3">
            <span id="save-status" class="text-xs text-gray-400 flex items-center gap-1">
                @if($document->last_saved_at)
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Saved {{ $document->last_saved_at->diffForHumans() }}
                @else
                    Not saved yet
                @endif
            </span>
            <button id="save-btn"
                onclick="saveDocument()"
                class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                Save
            </button>
        </div>
    </div>

    {{-- Editor body --}}
    <div class="flex-1 overflow-y-auto bg-gray-100">
        <div class="max-w-4xl mx-auto my-8 bg-white shadow-sm rounded-lg min-h-[700px]">
            <div
                id="editor-content"
                contenteditable="true"
                spellcheck="true"
                class="min-h-[700px] p-12 prose prose-lg max-w-none focus:outline-none"
                data-placeholder="Start typing..."
            >{!! $document->content !!}</div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const slug   = document.getElementById('editor-app').dataset.slug;
const editor = document.getElementById('editor-content');
const titleInput = document.getElementById('doc-title');
const saveStatus = document.getElementById('save-status');

let saveTimer = null;
let isDirty   = false;

// ── Format helpers ─────────────────────────────────────────────────
function formatText(command) {
    document.execCommand(command, false, null);
    editor.focus();
    markDirty();
}

function formatBlock(tag) {
    document.execCommand('formatBlock', false, tag);
    editor.focus();
    markDirty();
}

// ── Dirty tracking ─────────────────────────────────────────────────
function markDirty() {
    isDirty = true;
    saveStatus.innerHTML = '<span class="text-yellow-500">Unsaved changes</span>';
    clearTimeout(saveTimer);
    saveTimer = setTimeout(saveDocument, 3000); // auto-save after 3s idle
}

editor.addEventListener('input', markDirty);
titleInput.addEventListener('input', markDirty);

// ── Save ───────────────────────────────────────────────────────────
async function saveDocument() {
    if (!isDirty) return;

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
                title:   titleInput.value || 'Untitled Document',
                content: editor.innerHTML,
            }),
        });

        const data = await res.json();

        if (data.saved) {
            isDirty = false;
            saveStatus.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Saved ${data.last_saved_at}`;
        }
    } catch (err) {
        saveStatus.innerHTML = '<span class="text-red-500">Save failed</span>';
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Save';
    }
}

// ── Keyboard shortcuts ─────────────────────────────────────────────
document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        saveDocument();
    }
});

// ── Placeholder ────────────────────────────────────────────────────
editor.addEventListener('focus', function () {
    if (this.innerHTML.trim() === '') this.classList.add('is-empty');
});
editor.addEventListener('blur', function () {
    this.classList.remove('is-empty');
});
</script>
@endpush
