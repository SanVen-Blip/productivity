@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-2xl mx-auto">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Profile Settings</h1>
        <p class="text-sm text-gray-500 mt-1">Manage your account information</p>
    </div>

    {{-- Profile Info --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-800 mb-5">Account Information</h2>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 text-sm mb-5 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update-info') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            {{-- Avatar placeholder --}}
            <div class="flex items-center gap-4 mb-6">
                <div class="h-16 w-16 rounded-full bg-blue-600 flex items-center justify-center text-white text-2xl font-bold select-none">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input
                    id="name" type="text" name="name"
                    value="{{ old('name', $user->name) }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror"
                />
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input
                    id="email" type="email" name="email"
                    value="{{ old('email', $user->email) }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror"
                />
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
        <h2 class="text-base font-semibold text-gray-800 mb-5">Change Password</h2>

        <form method="POST" action="{{ route('profile.update-password') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input
                    id="current_password" type="password" name="current_password"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-400 @enderror"
                    placeholder="••••••••"
                />
                @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input
                    id="password" type="password" name="password"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror"
                    placeholder="Min. 8 characters"
                />
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input
                    id="password_confirmation" type="password" name="password_confirmation"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="••••••••"
                />
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium px-5 py-2 rounded-lg transition-colors">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    {{-- Danger zone --}}
    <div class="bg-white rounded-2xl border border-red-100 p-6">
        <h2 class="text-base font-semibold text-red-700 mb-2">Danger Zone</h2>
        <p class="text-sm text-gray-500 mb-4">Once you delete your account, all documents will be permanently removed.</p>
        <button
            onclick="alert('Account deletion coming in a future update.')"
            class="text-sm text-red-600 hover:text-red-700 border border-red-200 hover:border-red-300 px-4 py-2 rounded-lg transition-colors">
            Delete Account
        </button>
    </div>

</div>
@endsection
