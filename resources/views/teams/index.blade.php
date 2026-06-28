@extends('layouts.app')

@section('title', 'My Teams')

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Teams</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Collaborate with others on shared documents</p>
        </div>
        <button onclick="document.getElementById('create-team-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            New Team
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg px-4 py-3 text-sm mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($teams->isEmpty())
        <div class="text-center py-20 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="text-gray-600 dark:text-gray-400 font-medium mb-1">No teams yet</h3>
            <p class="text-gray-400 dark:text-gray-500 text-sm mb-6">Create a team to start collaborating</p>
            <button onclick="document.getElementById('create-team-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                Create Your First Team
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($teams as $team)
            <a href="{{ route('teams.show', $team->slug) }}"
               class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-md p-5 transition-all">
                <div class="flex items-center gap-3 mb-3">
                    <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($team->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ $team->name }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            {{ ucfirst($team->pivot->role) }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                    <span>👥 {{ $team->members_count }} member{{ $team->members_count !== 1 ? 's' : '' }}</span>
                    <span>📄 {{ $team->documents_count }} doc{{ $team->documents_count !== 1 ? 's' : '' }}</span>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>

{{-- Create team modal --}}
<div id="create-team-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-6 w-full max-w-sm">
        <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Create Team</h3>
        <form method="POST" action="{{ route('teams.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Team Name</label>
                <input type="text" name="name" required autofocus
                    placeholder="e.g. Marketing Team"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"/>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('create-team-modal').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Create Team</button>
            </div>
        </form>
    </div>
</div>
@endsection
