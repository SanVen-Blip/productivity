@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Documents</h1>
            <p class="text-sm text-gray-500 mt-1">Create and manage your documents</p>
        </div>

        <form method="POST" action="{{ route('documents.store') }}">
            @csrf
            <button
                type="submit"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                New Document
            </button>
        </form>
    </div>

    @if($documents->isEmpty())
        {{-- Empty state --}}
        <div class="text-center py-24 bg-white rounded-2xl border border-dashed border-gray-300">
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
        </div>
    @else
        {{-- Document grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($documents as $doc)
                <div class="group bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col">
                    {{-- Document preview --}}
                    <a href="{{ route('documents.edit', $doc->slug) }}" class="flex-1 p-5 block">
                        {{-- Doc icon --}}
                        <div class="bg-blue-50 rounded-lg h-28 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>

                        <h3 class="font-medium text-gray-900 text-sm truncate">{{ $doc->title }}</h3>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $doc->updated_at->diffForHumans() }}
                        </p>
                    </a>

                    {{-- Footer actions --}}
                    <div class="border-t border-gray-100 px-5 py-2 flex items-center justify-between">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $doc->status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst($doc->status) }}
                        </span>

                        <form method="POST" action="{{ route('documents.destroy', $doc->slug) }}"
                            onsubmit="return confirm('Delete this document?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-gray-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
