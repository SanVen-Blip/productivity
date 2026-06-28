@extends('layouts.app')

@section('title', $team->name . ' — Settings')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Back --}}
    <div class="mb-6">
        <a href="{{ route('teams.show', $team->slug) }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back to {{ $team->name }}
        </a>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Team Settings</h1>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 rounded-lg px-4 py-3 text-sm mb-6">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg px-4 py-3 text-sm mb-6">
            @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
        </div>
    @endif

    {{-- Team name --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="font-semibold text-gray-800 dark:text-white mb-4">General</h2>
        <form method="POST" action="{{ route('teams.update', $team->slug) }}" class="flex gap-3">
            @csrf
            @method('PATCH')
            <input type="text" name="name" value="{{ $team->name }}" required
                class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"/>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">Save</button>
        </form>
    </div>

    {{-- Invite member --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="font-semibold text-gray-800 dark:text-white mb-4">Invite Member</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Add members by their email address. They must have a SanvenDocs account.</p>
        <form method="POST" action="{{ route('teams.invite', $team->slug) }}" class="flex flex-col sm:flex-row gap-2">
            @csrf
            <input type="email" name="email" required placeholder="member@email.com"
                class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"/>
            <select name="role" class="border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white">
                <option value="editor">Editor</option>
                <option value="viewer">Viewer</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors whitespace-nowrap">Invite</button>
        </form>
    </div>

    {{-- Members list --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h2 class="font-semibold text-gray-800 dark:text-white mb-4">Members ({{ $members->count() }})</h2>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($members as $member)
            <div class="flex items-center justify-between py-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $member->name }}
                            @if($member->id === Auth::id()) <span class="text-xs text-gray-400">(you)</span> @endif
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $member->email }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    @if($member->pivot->role === 'owner')
                        <span class="px-2 py-0.5 text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-full">Owner</span>
                    @else
                        <form method="POST" action="{{ route('teams.update-role', [$team->slug, $member->id]) }}" class="inline">
                            @csrf @method('PATCH')
                            <select name="role" onchange="this.form.submit()"
                                class="text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-1 dark:bg-gray-800 dark:text-white cursor-pointer">
                                <option value="editor" {{ $member->pivot->role === 'editor' ? 'selected' : '' }}>Editor</option>
                                <option value="viewer" {{ $member->pivot->role === 'viewer' ? 'selected' : '' }}>Viewer</option>
                            </select>
                        </form>
                        <form method="POST" action="{{ route('teams.remove-member', [$team->slug, $member->id]) }}"
                            onsubmit="return confirm('Remove {{ $member->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 p-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Remove">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Danger zone --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-red-200 dark:border-red-800 p-6">
        <h2 class="font-semibold text-red-700 dark:text-red-400 mb-2">Danger Zone</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Deleting the team will unlink all documents (they return to their creator's personal workspace).</p>
        <form method="POST" action="{{ route('teams.destroy', $team->slug) }}"
            onsubmit="return confirm('Delete team \'{{ addslashes($team->name) }}\'? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="text-sm text-red-600 hover:text-red-700 border border-red-200 dark:border-red-700 hover:border-red-300 px-4 py-2 rounded-lg transition-colors hover:bg-red-50 dark:hover:bg-red-900/20">
                Delete Team
            </button>
        </form>
    </div>
</div>
@endsection
