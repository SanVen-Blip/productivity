@extends('layouts.app')

@section('title', $document->title)

@section('main-class', 'h-[calc(100vh-57px)] flex flex-col')

@section('content')
<div class="flex h-full overflow-hidden" id="editor-app"
     data-slug="{{ $document->slug }}"
     data-created="{{ $document->created_at->format('M j, Y') }}"
     data-updated="{{ $document->updated_at->format('M j, Y g:i A') }}">

  {{-- ── Left: editor column ──────────────────────────────── --}}
  <div class="flex flex-col flex-1 min-w-0">

    {{-- ── Toolbar ────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-3 py-1.5 flex flex-wrap items-center gap-0.5 flex-shrink-0">

      {{-- Back --}}
      <a href="{{ route('dashboard') }}" class="toolbar-btn mr-1" title="Back">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
      </a>

      {{-- Title --}}
      <input type="text" id="doc-title" value="{{ $document->title }}"
        class="w-40 text-sm font-medium text-gray-800 dark:text-gray-200 dark:bg-transparent border border-transparent rounded px-2 py-1 focus:outline-none focus:border-blue-300 focus:bg-blue-50 dark:focus:bg-blue-900/20 transition-colors"
        placeholder="Document title..."/>

      <div class="h-5 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

      {{-- Font size --}}
      <select id="font-size-select" onchange="changeFontSize(this.value)" class="toolbar-select text-xs">
        <option value="">Size</option>
        <option value="1">Small</option>
        <option value="3">Normal</option>
        <option value="4">Large</option>
        <option value="5">X-Large</option>
        <option value="6">2X-Large</option>
      </select>

      <div class="h-5 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

      {{-- Text format --}}
      <button onclick="fmt('bold')"         title="Bold (Ctrl+B)"      class="toolbar-btn font-bold" id="btn-bold">B</button>
      <button onclick="fmt('italic')"       title="Italic (Ctrl+I)"    class="toolbar-btn italic"    id="btn-italic">I</button>
      <button onclick="fmt('underline')"    title="Underline (Ctrl+U)" class="toolbar-btn underline" id="btn-underline">U</button>
      <button onclick="fmt('strikeThrough')" title="Strikethrough"     class="toolbar-btn line-through text-xs">S</button>

      <div class="h-5 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

      {{-- Headings --}}
      <button onclick="fmtBlock('h1')" class="toolbar-btn text-xs font-semibold">H1</button>
      <button onclick="fmtBlock('h2')" class="toolbar-btn text-xs font-semibold">H2</button>
      <button onclick="fmtBlock('h3')" class="toolbar-btn text-xs font-semibold">H3</button>
      <button onclick="fmtBlock('p')"  class="toolbar-btn text-xs">¶</button>

      <div class="h-5 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

      {{-- Alignment --}}
      <button onclick="fmt('justifyLeft')"   title="Align Left"   class="toolbar-btn"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h14"/></svg></button>
      <button onclick="fmt('justifyCenter')" title="Align Center" class="toolbar-btn"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M7 12h10M5 18h14"/></svg></button>
      <button onclick="fmt('justifyRight')"  title="Align Right"  class="toolbar-btn"><svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M10 12h10M6 18h14"/></svg></button>

      <div class="h-5 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

      {{-- Lists --}}
      <button onclick="fmt('insertUnorderedList')" title="Bullet List"   class="toolbar-btn text-xs">• List</button>
      <button onclick="fmt('insertOrderedList')"   title="Numbered List" class="toolbar-btn text-xs">1. List</button>

      <div class="h-5 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

      {{-- Insert table --}}
      <button onclick="insertTable()" title="Insert Table" class="toolbar-btn text-xs flex items-center gap-0.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M10 3v18M14 3v18M3 6a3 3 0 013-3h12a3 3 0 013 3v12a3 3 0 01-3 3H6a3 3 0 01-3-3V6z"/>
        </svg>
        Table
      </button>

      {{-- Insert image --}}
      <button onclick="document.getElementById('img-upload').click()" title="Insert Image" class="toolbar-btn text-xs flex items-center gap-0.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Image
      </button>
      <input type="file" id="img-upload" accept="image/*" class="hidden" onchange="handleImageUpload(this)"/>

      {{-- Link --}}
      <button onclick="insertLink()" title="Insert Link" class="toolbar-btn">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
      </button>

      <div class="h-5 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

      {{-- Undo/Redo --}}
      <button onclick="document.execCommand('undo')" title="Undo (Ctrl+Z)" class="toolbar-btn">↩</button>
      <button onclick="document.execCommand('redo')" title="Redo (Ctrl+Y)" class="toolbar-btn">↪</button>

      <div class="h-5 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

      {{-- Find & Replace --}}
      <button onclick="toggleFindReplace()" title="Find & Replace (Ctrl+H)" class="toolbar-btn text-xs flex items-center gap-0.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        Find
      </button>

      {{-- Export --}}
      <div class="h-5 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>
      <a href="{{ route('documents.export', $document->slug) }}" target="_blank" class="toolbar-btn text-xs flex items-center gap-0.5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Export
      </a>

      {{-- Right side: word count + info + history + save --}}
      <div class="ml-auto flex items-center gap-2">
        <span id="word-count" class="text-xs text-gray-400 dark:text-gray-500 hidden lg:block">0 words</span>

        {{-- Doc info panel toggle --}}
        <button onclick="toggleInfoPanel()" title="Document Info" id="btn-info"
          class="toolbar-btn text-xs">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </button>

        {{-- Version history toggle --}}
        <button onclick="toggleHistoryPanel()" title="Version History" id="btn-history"
          class="toolbar-btn text-xs flex items-center gap-0.5">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          History
        </button>

        <span id="save-status" class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1 hidden sm:flex">
          @if($document->last_saved_at)
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
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

    {{-- ── Find & Replace bar ───────────────────────────────── --}}
    <div id="find-replace-bar" class="hidden bg-yellow-50 dark:bg-yellow-900/20 border-b border-yellow-200 dark:border-yellow-800 px-4 py-2 flex items-center gap-2 flex-shrink-0">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
      <input id="find-input" type="text" placeholder="Find..." class="text-sm border border-yellow-300 dark:border-yellow-700 rounded px-2 py-1 w-36 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:bg-gray-800 dark:text-white"/>
      <input id="replace-input" type="text" placeholder="Replace with..." class="text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 w-40 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-800 dark:text-white"/>
      <button onclick="findNext()"    class="text-xs px-2 py-1 bg-yellow-200 dark:bg-yellow-800 hover:bg-yellow-300 rounded transition-colors">Next</button>
      <button onclick="replaceCurrent()" class="text-xs px-2 py-1 bg-blue-100 dark:bg-blue-900 hover:bg-blue-200 rounded transition-colors">Replace</button>
      <button onclick="replaceAll()"  class="text-xs px-2 py-1 bg-blue-600 text-white hover:bg-blue-700 rounded transition-colors">Replace All</button>
      <span id="find-count" class="text-xs text-gray-500 dark:text-gray-400"></span>
      <button onclick="toggleFindReplace()" class="ml-auto text-gray-400 hover:text-gray-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    {{-- ── Editor area ──────────────────────────────────────── --}}
    <div class="flex-1 overflow-y-auto bg-gray-100 dark:bg-gray-950">
      <div class="max-w-4xl mx-auto my-8 bg-white dark:bg-gray-900 shadow-sm rounded-xl min-h-[700px]">
        <div id="editor-content" contenteditable="true" spellcheck="true"
          class="min-h-[700px] p-12 focus:outline-none dark:text-gray-100"
          data-placeholder="Start typing your document..."
        >{!! $document->content !!}</div>
      </div>
    </div>
  </div>

  {{-- ── Right: Info / History panel ──────────────────────── --}}
  <div id="side-panel" class="hidden w-72 flex-shrink-0 bg-white dark:bg-gray-900 border-l border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden">

    {{-- Panel tabs --}}
    <div class="flex border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
      <button id="tab-info" onclick="switchTab('info')"
        class="flex-1 px-3 py-2.5 text-xs font-medium border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 transition-colors">
        Info
      </button>
      <button id="tab-history" onclick="switchTab('history')"
        class="flex-1 px-3 py-2.5 text-xs font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
        History
      </button>
      <button onclick="closeSidePanel()" class="px-3 text-gray-400 hover:text-gray-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    {{-- Info tab --}}
    <div id="panel-info" class="flex-1 overflow-y-auto p-4 space-y-4">
      <div>
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Document</p>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400">Created</span>
            <span class="text-gray-800 dark:text-gray-200 text-xs">{{ $document->created_at->format('M j, Y') }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400">Modified</span>
            <span class="text-gray-800 dark:text-gray-200 text-xs" id="info-modified">{{ $document->updated_at->format('M j, Y g:i A') }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400">Status</span>
            <span class="text-gray-800 dark:text-gray-200 text-xs">{{ ucfirst($document->status) }}</span>
          </div>
          @if($document->folder)
          <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400">Folder</span>
            <span class="text-blue-600 dark:text-blue-400 text-xs">{{ $document->folder }}</span>
          </div>
          @endif
        </div>
      </div>

      <div>
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Statistics</p>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Words</span><span id="stat-words" class="text-gray-800 dark:text-gray-200 text-xs">0</span></div>
          <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Characters</span><span id="stat-chars" class="text-gray-800 dark:text-gray-200 text-xs">0</span></div>
          <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Paragraphs</span><span id="stat-paras" class="text-gray-800 dark:text-gray-200 text-xs">0</span></div>
          <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Read time</span><span id="stat-read" class="text-gray-800 dark:text-gray-200 text-xs">0 min</span></div>
        </div>
      </div>

      <div>
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Actions</p>
        <div class="space-y-1">
          <a href="{{ route('documents.export', $document->slug) }}" target="_blank"
            class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 py-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Export / Print
          </a>
          <form method="POST" action="{{ route('documents.duplicate', $document->slug) }}">
            @csrf
            <button type="submit" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 w-full py-1.5">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
              Duplicate Document
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- History tab --}}
    <div id="panel-history" class="hidden flex-1 overflow-y-auto">
      <div class="p-4 border-b border-gray-100 dark:border-gray-800">
        <p class="text-xs text-gray-500 dark:text-gray-400">Versions are saved automatically on each save. Up to 30 versions kept.</p>
      </div>
      <div id="versions-list" class="divide-y divide-gray-100 dark:divide-gray-800">
        <div class="p-4 text-sm text-gray-400 dark:text-gray-500 text-center">Loading versions…</div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
const slug       = document.getElementById('editor-app').dataset.slug;
const editor     = document.getElementById('editor-content');
const titleInput = document.getElementById('doc-title');
const saveStatus = document.getElementById('save-status');
const wordCountEl= document.getElementById('word-count');

let saveTimer = null;
let isDirty   = false;

// ── Format ────────────────────────────────────────────────────────
function fmt(cmd) { document.execCommand(cmd, false, null); editor.focus(); markDirty(); updateToolbarState(); }
function fmtBlock(tag) { document.execCommand('formatBlock', false, tag); editor.focus(); markDirty(); }
function changeFontSize(s) { if (s) { document.execCommand('fontSize', false, s); editor.focus(); markDirty(); } }
function insertLink() {
  const url = prompt('Enter URL:', 'https://');
  if (url) { document.execCommand('createLink', false, url); editor.focus(); markDirty(); }
}

// ── Insert Table ─────────────────────────────────────────────────
function insertTable() {
  const rows = parseInt(prompt('Rows:', '3') || '3');
  const cols = parseInt(prompt('Columns:', '3') || '3');
  if (!rows || !cols || rows < 1 || cols < 1) return;
  let html = '<table style="border-collapse:collapse;width:100%;margin:12px 0"><tbody>';
  for (let r = 0; r < rows; r++) {
    html += '<tr>';
    for (let c = 0; c < cols; c++) {
      const tag = r === 0 ? 'th' : 'td';
      html += `<${tag} style="border:1px solid #d1d5db;padding:8px 12px;text-align:left;${r===0?'background:#f9fafb;font-weight:600':''}">&nbsp;</${tag}>`;
    }
    html += '</tr>';
  }
  html += '</tbody></table><p><br></p>';
  document.execCommand('insertHTML', false, html);
  editor.focus();
  markDirty();
}

// ── Image upload ──────────────────────────────────────────────────
async function handleImageUpload(input) {
  const file = input.files[0];
  if (!file) return;
  const form = new FormData();
  form.append('image', file);
  form.append('_token', CSRF);
  try {
    const res  = await fetch('/documents/upload-image', { method: 'POST', body: form });
    const data = await res.json();
    document.execCommand('insertHTML', false,
      `<img src="${data.url}" alt="${file.name}" style="max-width:100%;height:auto;border-radius:6px;margin:8px 0"/>`
    );
    editor.focus();
    markDirty();
  } catch {
    // Fallback: embed as base64
    const reader = new FileReader();
    reader.onload = (e) => {
      document.execCommand('insertHTML', false,
        `<img src="${e.target.result}" alt="${file.name}" style="max-width:100%;height:auto;border-radius:6px;margin:8px 0"/>`
      );
      editor.focus();
      markDirty();
    };
    reader.readAsDataURL(file);
  }
  input.value = '';
}

// ── Toolbar active state ──────────────────────────────────────────
function updateToolbarState() {
  ['bold','italic','underline'].forEach(cmd => {
    const btn = document.getElementById('btn-' + cmd);
    if (btn) {
      btn.classList.toggle('bg-blue-100', document.queryCommandState(cmd));
      btn.classList.toggle('text-blue-700', document.queryCommandState(cmd));
    }
  });
}
document.addEventListener('selectionchange', updateToolbarState);

// ── Stats ──────────────────────────────────────────────────────────
function updateStats() {
  const text  = editor.innerText.trim();
  const words = text ? text.split(/\s+/).filter(Boolean).length : 0;
  const chars = text.length;
  const paras = editor.querySelectorAll('p, h1, h2, h3, li').length;
  const mins  = Math.ceil(words / 200) || 0;
  wordCountEl.textContent = `${words} words · ${chars} chars`;
  document.getElementById('stat-words').textContent = words.toLocaleString();
  document.getElementById('stat-chars').textContent = chars.toLocaleString();
  document.getElementById('stat-paras').textContent = paras;
  document.getElementById('stat-read').textContent  = `${mins} min`;
}

// ── Dirty ──────────────────────────────────────────────────────────
function markDirty() {
  isDirty = true;
  saveStatus.innerHTML = '<span class="text-yellow-500">Unsaved changes</span>';
  clearTimeout(saveTimer);
  saveTimer = setTimeout(saveDocument, 3000);
  updateStats();
}
editor.addEventListener('input', markDirty);
titleInput.addEventListener('input', markDirty);

// ── Save ───────────────────────────────────────────────────────────
async function saveDocument() {
  if (!isDirty) return;
  clearTimeout(saveTimer);
  const btn = document.getElementById('save-btn');
  btn.disabled = true; btn.textContent = 'Saving…';
  saveStatus.innerHTML = '<span class="text-gray-400">Saving…</span>';
  try {
    const res  = await fetch(`/documents/${slug}`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({ title: titleInput.value.trim() || 'Untitled Document', content: editor.innerHTML }),
    });
    const data = await res.json();
    if (data.saved) {
      isDirty = false;
      saveStatus.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Saved ${data.last_saved_at}`;
      document.getElementById('info-modified').textContent = new Date().toLocaleString('en-US', {month:'short',day:'numeric',year:'numeric',hour:'numeric',minute:'2-digit'});
    }
  } catch {
    saveStatus.innerHTML = '<span class="text-red-500">Save failed — retrying…</span>';
    saveTimer = setTimeout(saveDocument, 5000);
  } finally {
    btn.disabled = false; btn.textContent = 'Save';
  }
}

// ── Keyboard shortcuts ────────────────────────────────────────────
document.addEventListener('keydown', (e) => {
  const mod = e.ctrlKey || e.metaKey;
  if (mod && e.key === 's') { e.preventDefault(); saveDocument(); }
  if (mod && e.key === 'b') { e.preventDefault(); fmt('bold'); }
  if (mod && e.key === 'i') { e.preventDefault(); fmt('italic'); }
  if (mod && e.key === 'u') { e.preventDefault(); fmt('underline'); }
  if (mod && e.key === 'h') { e.preventDefault(); toggleFindReplace(); }
  if (e.key === 'Escape')   { document.getElementById('find-replace-bar').classList.add('hidden'); }
});
window.addEventListener('beforeunload', (e) => { if (isDirty) { e.preventDefault(); e.returnValue = ''; } });

// ── Side panel ────────────────────────────────────────────────────
let activePanelTab = 'info';
function toggleInfoPanel()    { openSidePanel('info'); }
function toggleHistoryPanel() { openSidePanel('history'); loadVersions(); }
function openSidePanel(tab) {
  const panel = document.getElementById('side-panel');
  const isOpen = !panel.classList.contains('hidden');
  if (isOpen && activePanelTab === tab) { closeSidePanel(); return; }
  panel.classList.remove('hidden');
  switchTab(tab);
}
function closeSidePanel() {
  document.getElementById('side-panel').classList.add('hidden');
}
function switchTab(tab) {
  activePanelTab = tab;
  document.getElementById('panel-info').classList.toggle('hidden', tab !== 'info');
  document.getElementById('panel-history').classList.toggle('hidden', tab !== 'history');
  document.getElementById('tab-info').className    = `flex-1 px-3 py-2.5 text-xs font-medium border-b-2 transition-colors ${tab==='info'    ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'}`;
  document.getElementById('tab-history').className = `flex-1 px-3 py-2.5 text-xs font-medium border-b-2 transition-colors ${tab==='history' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'}`;
  if (tab === 'history') loadVersions();
}

// ── Version history ───────────────────────────────────────────────
let versionsLoaded = false;
async function loadVersions(force = false) {
  if (versionsLoaded && !force) return;
  const list = document.getElementById('versions-list');
  list.innerHTML = '<div class="p-4 text-sm text-gray-400 text-center">Loading…</div>';
  try {
    const res  = await fetch(`/documents/${slug}/versions`, { headers: { 'Accept': 'application/json' } });
    const data = await res.json();
    versionsLoaded = true;
    if (!data.length) {
      list.innerHTML = '<div class="p-4 text-sm text-gray-400 dark:text-gray-500 text-center">No versions yet.<br>Save the document to create one.</div>';
      return;
    }
    list.innerHTML = data.map(v => `
      <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 group">
        <div class="min-w-0">
          <p class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate">v${v.version_number} — ${v.title}</p>
          <p class="text-xs text-gray-400 dark:text-gray-500" title="${v.created_at_full}">${v.created_at}</p>
        </div>
        <button onclick="restoreVersion(${v.id})"
          class="ml-2 flex-shrink-0 text-xs text-blue-600 dark:text-blue-400 hover:underline opacity-0 group-hover:opacity-100 transition-opacity">
          Restore
        </button>
      </div>
    `).join('');
  } catch {
    list.innerHTML = '<div class="p-4 text-sm text-red-400 text-center">Failed to load versions.</div>';
  }
}

async function restoreVersion(versionId) {
  if (!confirm('Restore this version? Current content will be saved as a version first.')) return;
  try {
    const res  = await fetch(`/documents/${slug}/versions/${versionId}/restore`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    if (data.restored) {
      editor.innerHTML   = data.content;
      titleInput.value   = data.title;
      isDirty            = false;
      versionsLoaded     = false;
      saveStatus.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Restored`;
      updateStats();
      showToast('Version restored ✓');
      loadVersions(true);
    }
  } catch { showToast('Failed to restore version', 'error'); }
}

// ── Find & Replace ────────────────────────────────────────────────
let findMatches = [], findIndex = 0;
function toggleFindReplace() {
  const bar = document.getElementById('find-replace-bar');
  bar.classList.toggle('hidden');
  if (!bar.classList.contains('hidden')) document.getElementById('find-input').focus();
}
function findNext() {
  const term = document.getElementById('find-input').value;
  if (!term) return;
  const text = editor.innerText;
  const regex = new RegExp(term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
  const matches = [...text.matchAll(regex)];
  document.getElementById('find-count').textContent = matches.length ? `${Math.min(findIndex+1, matches.length)} / ${matches.length}` : 'Not found';
  if (!matches.length) return;
  // Use browser find as fallback for highlight
  window.find(term, false, false, true);
  findIndex = (findIndex + 1) % matches.length;
}
function replaceCurrent() {
  const find    = document.getElementById('find-input').value;
  const replace = document.getElementById('replace-input').value;
  if (!find) return;
  const sel = window.getSelection();
  if (sel && sel.toString().toLowerCase() === find.toLowerCase()) {
    document.execCommand('insertText', false, replace);
    markDirty();
  }
  findNext();
}
function replaceAll() {
  const find    = document.getElementById('find-input').value;
  const replace = document.getElementById('replace-input').value;
  if (!find) return;
  const regex = new RegExp(find.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
  editor.innerHTML = editor.innerHTML.replace(regex, replace);
  markDirty();
  document.getElementById('find-count').textContent = 'Replaced all';
}

// ── Toast ──────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
  const c = { success:'bg-green-600', error:'bg-red-600', info:'bg-blue-600' };
  const t = document.createElement('div');
  t.className = `fixed bottom-5 right-5 z-50 flex items-center gap-2 px-4 py-3 rounded-xl text-white text-sm shadow-lg ${c[type]} transition-all duration-300 translate-y-2 opacity-0`;
  t.textContent = msg;
  document.body.appendChild(t);
  requestAnimationFrame(() => t.classList.remove('translate-y-2','opacity-0'));
  setTimeout(() => { t.classList.add('translate-y-2','opacity-0'); setTimeout(() => t.remove(), 300); }, 3000);
}

// ── Mobile sidebar toggle ─────────────────────────────────────────
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.getElementById('find-replace-bar').classList.add('hidden');
    closeSidePanel();
  }
});

// ── Init ──────────────────────────────────────────────────────────
updateStats();
</script>
@endpush
