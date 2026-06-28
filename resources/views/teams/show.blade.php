@extends('layouts.app')

@section('title', $team->name)

@section('content')
<div class="max-w-6xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('teams.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                {{ strtoupper(substr($team->name, 0, 2)) }}
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $team->name }}</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $members->count() }} member{{ $members->count() !== 1 ? 's' : '' }} · Your role: <span class="font-medium text-blue-600 dark:text-blue-400">{{ ucfirst($role) }}</span></p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            {{-- Search --}}
            <form method="GET" action="{{ route('teams.show', $team->slug) }}" class="relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search docs..."
                    class="pl-8 pr-4 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-40 bg-white dark:bg-gray-800 dark:text-white"/>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </form>

            @if(in_array($role, ['owner', 'editor']))
            <form method="POST" action="{{ route('teams.create-document', $team->slug) }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-3 py-1.5 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    New Doc
                </button>
            </form>
            @endif

            @if($role === 'owner')
            <a href="{{ route('teams.settings', $team->slug) }}"
                class="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg px-4 py-3 text-sm mb-6">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Documents --}}
        <div class="flex-1 min-w-0">
            @if($documents->isEmpty())
                <div class="text-center py-16 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-gray-500 dark:text-gray-400 font-medium mb-1">No team documents yet</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm">Create a document to start collaborating</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                    @foreach($documents as $doc)
                    <a href="{{ route('documents.edit', $doc->slug) }}"
                        class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-md p-4 transition-all group">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg h-20 flex items-center justify-center mb-3 overflow-hidden relative">
                            @if($doc->content)
                                <div class="absolute inset-0 p-2 text-[7px] leading-tight overflow-hidden pointer-events-none select-none text-gray-400">
                                    {!! strip_tags(substr($doc->content, 0, 200)) !!}
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-blue-50 dark:to-blue-900/20"></div>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-300 dark:text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @endif
                        </div>
                        <p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $doc->title }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">By {{ $doc->user->name }} · {{ $doc->updated_at->diffForHumans() }}</p>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Members sidebar --}}
        <div class="w-full lg:w-64 flex-shrink-0">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Members</p>
                <div class="space-y-2">
                    @foreach($members as $member)
                    <div class="flex items-center gap-2">
                        <div class="h-7 w-7 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-gray-900 dark:text-white truncate">{{ $member->name }}</p>
                            <p class="text-xs text-gray-400">{{ ucfirst($member->pivot->role) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($role !== 'owner')
            <form method="POST" action="{{ route('teams.leave', $team->slug) }}" class="mt-3"
                onsubmit="return confirm('Are you sure you want to leave this team?')">
                @csrf
                <button type="submit" class="w-full text-center text-sm text-red-500 hover:text-red-700 dark:text-red-400 py-2 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    Leave Team
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
