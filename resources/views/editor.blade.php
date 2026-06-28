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

      {{-- Font family --}}
      <select id="font-family-select" onchange="changeFontFamily(this.value)" class="toolbar-select text-xs w-28" title="Font Family">
        <option value="">Font</option>
        <option value="Arial, sans-serif">Arial</option>
        <option value="'Times New Roman', serif">Times New Roman</option>
        <option value="'Georgia', serif">Georgia</option>
        <option value="'Courier New', monospace">Courier New</option>
        <option value="'Verdana', sans-serif">Verdana</option>
        <option value="'Trebuchet MS', sans-serif">Trebuchet MS</option>
        <option value="'Comic Sans MS', cursive">Comic Sans MS</option>
        <option value="'Impact', sans-serif">Impact</option>
        <option value="'Lucida Console', monospace">Lucida Console</option>
        <option value="'Palatino', serif">Palatino</option>
        <option value="system-ui, sans-serif">System UI</option>
      </select>

      {{-- Font size --}}
      <select id="font-size-select" onchange="changeFontSize(this.value)" class="toolbar-select text-xs">
        <option value="">Size</option>
        <option value="1">8px</option>
        <option value="2">10px</option>
        <option value="3">12px</option>
        <option value="4">14px</option>
        <option value="5">18px</option>
        <option value="6">24px</option>
        <option value="7">36px</option>
      </select>

      {{-- Text color --}}
      <div class="relative">
        <input type="color" id="text-color-picker" value="#000000" onchange="changeTextColor(this.value)"
          class="absolute opacity-0 w-0 h-0" />
        <button onclick="document.getElementById('text-color-picker').click()" title="Text Color" class="toolbar-btn text-xs flex items-center gap-0.5">
          <span class="font-bold">A</span>
          <span id="color-indicator" class="h-1 w-4 rounded-sm bg-current block"></span>
        </button>
      </div>

      {{-- Highlight color --}}
      <div class="relative">
        <input type="color" id="bg-color-picker" value="#FFFF00" onchange="changeHighlight(this.value)"
          class="absolute opacity-0 w-0 h-0" />
        <button onclick="document.getElementById('bg-color-picker').click()" title="Highlight Color" class="toolbar-btn text-xs">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12m-4-4v4m-4 0h4"/></svg>
        </button>
      </div>

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

      {{-- Right side: word count + online presence + info + history + save --}}
      <div class="ml-auto flex items-center gap-2">

        {{-- Online users avatars (real-time) — PROMINENT --}}
        <div id="online-avatars" class="flex items-center -space-x-2 mr-1" title="Users editing now">
        </div>

        {{-- Live collaboration badge --}}
        <div id="collab-badge" class="hidden items-center gap-1 px-2 py-0.5 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-full text-xs text-green-700 dark:text-green-400 font-medium animate-pulse">
          <span class="h-2 w-2 bg-green-500 rounded-full inline-block"></span>
          <span id="collab-badge-text">Live</span>
        </div>

        {{-- Typing indicator — more visible --}}
        <div id="typing-indicator" class="hidden items-center gap-1 px-2 py-0.5 bg-blue-50 dark:bg-blue-900/20 rounded-full text-xs text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-700">
          <span class="flex gap-0.5">
            <span class="h-1.5 w-1.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay:0ms"></span>
            <span class="h-1.5 w-1.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay:150ms"></span>
            <span class="h-1.5 w-1.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay:300ms"></span>
          </span>
          <span id="typing-text">typing...</span>
        </div>

        <span id="word-count" class="text-xs text-gray-400 dark:text-gray-500 hidden lg:block">0 words</span>

        {{-- Doc info panel toggle --}}
        <button onclick="toggleInfoPanel()" title="Document Info" id="btn-info"
          class="toolbar-btn text-xs">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </button>

        {{-- Online presence panel toggle --}}
        <button onclick="togglePresencePanel()" title="Who's online" id="btn-presence"
          class="toolbar-btn text-xs flex items-center gap-0.5">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
          <span id="online-count-badge" class="hidden px-1 py-0 text-[10px] bg-green-500 text-white rounded-full min-w-[14px] text-center leading-tight">0</span>
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
      <button id="tab-presence" onclick="switchTab('presence')"
        class="flex-1 px-2 py-2.5 text-xs font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
        Online
      </button>
      <button id="tab-info" onclick="switchTab('info')"
        class="flex-1 px-2 py-2.5 text-xs font-medium border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 transition-colors">
        Info
      </button>
      <button id="tab-history" onclick="switchTab('history')"
        class="flex-1 px-2 py-2.5 text-xs font-medium border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
        History
      </button>
      <button onclick="closeSidePanel()" class="px-3 text-gray-400 hover:text-gray-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    {{-- Presence tab --}}
    <div id="panel-presence" class="hidden flex-1 overflow-y-auto p-4">
      <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Currently Online</p>
      <div id="presence-list" class="space-y-2">
        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Loading...</p>
      </div>
      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
        <p class="text-xs text-gray-400 dark:text-gray-500">
          <span class="inline-block h-2 w-2 rounded-full bg-green-500 mr-1"></span>
          Real-time presence updates every 2s
        </p>
      </div>
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

      {{-- Tags --}}
      <div>
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Tags</p>
        <div id="tags-display" class="flex flex-wrap gap-1 mb-2">
          @forelse($document->tags ?? [] as $tag)
            <span class="tag-pill px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-xs border border-blue-200 dark:border-blue-700">{{ $tag }}</span>
          @empty
            <span class="text-xs text-gray-400 dark:text-gray-500">No tags yet</span>
          @endforelse
        </div>
        <div class="flex gap-1">
          <input id="tag-input" type="text" placeholder="Add tag…" maxlength="30"
            class="flex-1 text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"/>
          <button onclick="addTag()" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded transition-colors">+</button>
        </div>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Press Enter or + to add</p>
      </div>

      {{-- Share --}}
      <div>
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Share</p>
        <div class="space-y-2">
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">Public link</span>
            <button onclick="togglePublic()" id="share-toggle"
              class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $document->is_public ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600' }}">
              <span id="share-toggle-dot" class="inline-block h-3.5 w-3.5 rounded-full bg-white shadow transition-transform {{ $document->is_public ? 'translate-x-4' : 'translate-x-0.5' }}"></span>
            </button>
          </div>
          <div id="share-url-box" class="{{ $document->is_public ? '' : 'hidden' }} space-y-1">
            <input id="share-url-input" type="text" readonly
              value="{{ $document->is_public ? route('documents.public', $document->share_token) : '' }}"
              class="w-full text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-1.5 bg-gray-50 dark:bg-gray-800 dark:text-white truncate focus:outline-none"/>
            <button onclick="copyShareUrl()" class="w-full text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 py-1.5 rounded transition-colors">
              Copy Link
            </button>
          </div>
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

// ── Font Family ───────────────────────────────────────────────────
function changeFontFamily(font) {
  if (!font) return;
  document.execCommand('fontName', false, font);
  editor.focus();
  markDirty();
}

// ── Text Color ────────────────────────────────────────────────────
function changeTextColor(color) {
  document.execCommand('foreColor', false, color);
  document.getElementById('color-indicator').style.backgroundColor = color;
  editor.focus();
  markDirty();
}

// ── Highlight Color ───────────────────────────────────────────────
function changeHighlight(color) {
  document.execCommand('hiliteColor', false, color);
  editor.focus();
  markDirty();
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
  saveTimer = setTimeout(saveDocument, 1500); // auto-save after 1.5s idle (faster for collab)
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
      lastSavedAtServer = new Date().toISOString();
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
  document.getElementById('panel-presence').classList.toggle('hidden', tab !== 'presence');
  const tabs = ['presence','info','history'];
  tabs.forEach(t => {
    const el = document.getElementById('tab-' + t);
    if (el) el.className = `flex-1 px-2 py-2.5 text-xs font-medium border-b-2 transition-colors ${t===tab ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'}`;
  });
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

// ── REAL-TIME PRESENCE ────────────────────────────────────────────
let presenceInterval = null;
let lastSavedAtServer = '{{ $document->last_saved_at?->toIso8601String() ?? '' }}';
let isTypingTimeout = null;
let localIsTyping = false;

function startPresence() {
  // First heartbeat immediately
  sendHeartbeat();
  // Fast polling: 800ms for near-realtime experience
  presenceInterval = setInterval(sendHeartbeat, 800);
}

async function sendHeartbeat() {
  try {
    const res = await fetch(`/documents/${slug}/presence/heartbeat`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({
        is_typing: localIsTyping,
        last_saved_at: lastSavedAtServer,
      }),
    });
    const data = await res.json();

    // Update online users display
    renderOnlineUsers(data.online_users);

    // Sync content from other editors — IMMEDIATE
    if (data.sync) {
      const serverTime = new Date(data.sync.last_saved_at).getTime();
      const clientTime = lastSavedAtServer ? new Date(lastSavedAtServer).getTime() : 0;
      
      if (serverTime > clientTime) {
        if (!isDirty) {
          // No local changes — apply sync silently
          editor.innerHTML = data.sync.content;
          titleInput.value = data.sync.title;
          lastSavedAtServer = data.sync.last_saved_at;
          saveStatus.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> <span class="text-blue-500">Synced</span>`;
          updateStats();
          showSyncNotification();
        } else {
          // User has local changes — show conflict notification
          showSyncConflict(data.sync);
        }
      }
    }
  } catch (err) {
    // Silently fail — will retry next interval
  }
}

function renderOnlineUsers(users) {
  const avatarContainer = document.getElementById('online-avatars');
  const presenceList = document.getElementById('presence-list');
  const typingEl = document.getElementById('typing-indicator');
  const typingText = document.getElementById('typing-text');
  const countBadge = document.getElementById('online-count-badge');
  const collabBadge = document.getElementById('collab-badge');
  const collabText = document.getElementById('collab-badge-text');

  const otherUsers = users.filter(u => !u.is_self);
  const allCount = users.length;

  // Collaboration badge — show when others are online
  if (otherUsers.length > 0) {
    collabBadge.classList.remove('hidden');
    collabBadge.classList.add('flex');
    collabText.textContent = `${otherUsers.length} online`;
  } else {
    collabBadge.classList.add('hidden');
    collabBadge.classList.remove('flex');
  }

  // Count badge
  if (allCount > 1) {
    countBadge.textContent = allCount;
    countBadge.classList.remove('hidden');
  } else {
    countBadge.classList.add('hidden');
  }

  // Toolbar avatars — show ALL users with colored ring
  const colors = ['bg-emerald-500','bg-purple-500','bg-pink-500','bg-orange-500','bg-cyan-500','bg-rose-500','bg-amber-500','bg-indigo-500'];
  const ringColors = ['ring-emerald-300','ring-purple-300','ring-pink-300','ring-orange-300','ring-cyan-300','ring-rose-300','ring-amber-300','ring-indigo-300'];

  avatarContainer.innerHTML = users.slice(0, 6).map((u, i) => {
    const isTyping = u.is_typing && !u.is_self;
    const color = u.is_self ? 'bg-blue-600' : colors[i % colors.length];
    const ring = u.is_self ? 'ring-blue-200' : ringColors[i % ringColors.length];
    return `
      <div class="relative" title="${u.name}${isTyping ? ' (typing...)' : ''}">
        <div class="h-7 w-7 rounded-full ${color} flex items-center justify-center text-white text-[11px] font-bold ring-2 ${ring} dark:ring-gray-800 cursor-default shadow-sm ${isTyping ? 'animate-pulse' : ''}">
          ${u.initial}
        </div>
        <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full ${isTyping ? 'bg-yellow-400' : 'bg-green-500'} ring-2 ring-white dark:ring-gray-900"></span>
      </div>`;
  }).join('') + (users.length > 6 ? `<div class="h-7 w-7 rounded-full bg-gray-500 flex items-center justify-center text-white text-[10px] font-bold ring-2 ring-gray-300 dark:ring-gray-800 shadow-sm">+${users.length - 6}</div>` : '');

  // Typing indicator — show who's typing with names
  const typingUsers = users.filter(u => u.is_typing && !u.is_self);
  if (typingUsers.length > 0) {
    const names = typingUsers.map(u => u.name.split(' ')[0]).join(', ');
    typingText.textContent = `${names} typing`;
    typingEl.classList.remove('hidden');
    typingEl.classList.add('flex');
  } else {
    typingEl.classList.add('hidden');
    typingEl.classList.remove('flex');
  }

  // Presence panel list — detailed view
  if (presenceList) {
    if (users.length === 0) {
      presenceList.innerHTML = '<p class="text-sm text-gray-400 text-center py-4">No one online</p>';
    } else {
      presenceList.innerHTML = users.map((u, i) => {
        const color = u.is_self ? 'bg-blue-600' : colors[i % colors.length];
        return `
        <div class="flex items-center gap-3 py-2 px-2 rounded-lg ${u.is_typing && !u.is_self ? 'bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800' : ''}">
          <div class="relative flex-shrink-0">
            <div class="h-9 w-9 rounded-full ${color} flex items-center justify-center text-white text-sm font-bold shadow-sm">
              ${u.initial}
            </div>
            <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full ${u.is_typing ? 'bg-yellow-400 animate-pulse' : 'bg-green-500'} ring-2 ring-white dark:ring-gray-900"></span>
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
              ${u.name}${u.is_self ? ' <span class="text-xs text-blue-500 font-normal">(you)</span>' : ''}
            </p>
            <p class="text-xs flex items-center gap-1 ${u.is_typing && !u.is_self ? 'text-blue-500 font-medium' : 'text-gray-400 dark:text-gray-500'}">
              ${u.is_typing && !u.is_self
                ? '<span class="flex gap-0.5"><span class="h-1 w-1 bg-blue-500 rounded-full animate-bounce" style="animation-delay:0ms"></span><span class="h-1 w-1 bg-blue-500 rounded-full animate-bounce" style="animation-delay:150ms"></span><span class="h-1 w-1 bg-blue-500 rounded-full animate-bounce" style="animation-delay:300ms"></span></span> typing now'
                : u.is_self ? '🟢 editing' : '🟢 viewing'}
            </p>
          </div>
          ${u.is_typing && !u.is_self ? '<span class="text-[10px] px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 rounded-full font-medium">TYPING</span>' : ''}
        </div>`;
      }).join('');
    }
  }
}

// Track typing state
editor.addEventListener('input', () => {
  localIsTyping = true;
  clearTimeout(isTypingTimeout);
  isTypingTimeout = setTimeout(() => { localIsTyping = false; }, 1500); // Quick typing timeout
});

// Notify server on page leave
window.addEventListener('beforeunload', () => {
  navigator.sendBeacon(`/documents/${slug}/presence/leave`,
    new URLSearchParams({ '_token': CSRF })
  );
});

// Presence panel toggle
function togglePresencePanel() { openSidePanel('presence'); }

// Start presence system
startPresence();

// ── Sync notifications ────────────────────────────────────────────
function showSyncNotification() {
  const el = document.createElement('div');
  el.className = 'fixed top-16 right-4 z-50 flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-xl shadow-lg animate-fade-in-up';
  el.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Document updated by teammate`;
  document.body.appendChild(el);
  setTimeout(() => { el.classList.add('opacity-0', 'translate-y-2'); setTimeout(() => el.remove(), 300); }, 2500);
}

function showSyncConflict(syncData) {
  // Show a non-blocking notification with option to accept or keep local
  const existing = document.getElementById('sync-conflict-bar');
  if (existing) return; // already showing

  const bar = document.createElement('div');
  bar.id = 'sync-conflict-bar';
  bar.className = 'fixed top-16 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 px-5 py-3 bg-amber-500 text-white text-sm rounded-xl shadow-xl animate-fade-in-up';
  bar.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    <span>Teammate updated this doc</span>
    <button onclick="acceptSync()" class="px-2 py-1 bg-white text-amber-700 rounded font-medium text-xs hover:bg-amber-50">Accept changes</button>
    <button onclick="dismissSync()" class="px-2 py-1 bg-amber-600 text-white rounded text-xs hover:bg-amber-700 border border-amber-400">Keep mine</button>
  `;
  document.body.appendChild(bar);

  window._pendingSync = syncData;
}

function acceptSync() {
  if (window._pendingSync) {
    editor.innerHTML = window._pendingSync.content;
    titleInput.value = window._pendingSync.title;
    lastSavedAtServer = window._pendingSync.last_saved_at;
    isDirty = false;
    saveStatus.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> <span class="text-blue-500">Synced</span>`;
    updateStats();
    window._pendingSync = null;
  }
  dismissSync();
}

function dismissSync() {
  const bar = document.getElementById('sync-conflict-bar');
  if (bar) bar.remove();
  window._pendingSync = null;
}

// ── Tags ──────────────────────────────────────────────────────────
let currentTags = @json($document->tags ?? []);

function renderTags() {
  const display = document.getElementById('tags-display');
  if (!display) return;
  if (!currentTags.length) {
    display.innerHTML = '<span class="text-xs text-gray-400 dark:text-gray-500">No tags yet</span>';
    return;
  }
  display.innerHTML = currentTags.map(tag =>
    `<span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-xs border border-blue-200 dark:border-blue-700 cursor-pointer group" onclick="removeTag('${tag}')">
      ${tag}
      <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 opacity-50 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </span>`
  ).join('');
}

async function saveTags() {
  try {
    await fetch(`/documents/${slug}/tags`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
      body: JSON.stringify({ tags: currentTags }),
    });
  } catch { /* silent */ }
}

function addTag() {
  const input = document.getElementById('tag-input');
  const val = input.value.trim().toLowerCase().replace(/[^a-z0-9\-_ ]/g, '');
  if (!val || currentTags.includes(val) || currentTags.length >= 10) return;
  currentTags.push(val);
  renderTags();
  saveTags();
  input.value = '';
}

function removeTag(tag) {
  currentTags = currentTags.filter(t => t !== tag);
  renderTags();
  saveTags();
}

document.getElementById('tag-input')?.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') { e.preventDefault(); addTag(); }
});

renderTags();

// ── Share / Public toggle ─────────────────────────────────────────
async function togglePublic() {
  try {
    const res  = await fetch(`/documents/${slug}/public`, {
      method: 'PATCH',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    });
    const data = await res.json();
    const toggle    = document.getElementById('share-toggle');
    const dot       = document.getElementById('share-toggle-dot');
    const urlBox    = document.getElementById('share-url-box');
    const urlInput  = document.getElementById('share-url-input');

    if (data.is_public) {
      toggle.classList.replace('bg-gray-300','bg-blue-600');
      toggle.classList.replace('dark:bg-gray-600','bg-blue-600');
      dot.classList.replace('translate-x-0.5','translate-x-4');
      urlInput.value = data.share_url;
      urlBox.classList.remove('hidden');
      showToast('🔗 Document is now public');
    } else {
      toggle.classList.replace('bg-blue-600','bg-gray-300');
      dot.classList.replace('translate-x-4','translate-x-0.5');
      urlBox.classList.add('hidden');
      showToast('Document is now private', 'info');
    }
  } catch { showToast('Failed to update share settings', 'error'); }
}

function copyShareUrl() {
  const val = document.getElementById('share-url-input').value;
  navigator.clipboard.writeText(val).then(() => showToast('🔗 Link copied to clipboard'));
}
</script>
@endpush
