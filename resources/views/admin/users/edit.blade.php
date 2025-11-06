@extends('admin.layouts.app')

@section('title', 'Edit Admin User')
@section('page-title', 'Edit Admin User')

@section('content')
<div class="max-w-3xl">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-white transition flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Admin Users
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-gray-800 rounded-lg shadow-lg p-8">
        <!-- Current User Info -->
        <div class="mb-6 p-4 bg-gray-700 rounded-lg flex items-center justify-between">
            <div>
                <p class="text-gray-300 text-sm mb-1">Editing user:</p>
                <p class="text-white font-semibold">{{ $user->name }}</p>
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full mt-2 {{ $user->role->value === 'superadmin' ? 'bg-purple-900 text-purple-200' : 'bg-blue-900 text-blue-200' }}">
                    {{ $user->role->label() }}
                </span>
            </div>
            @if($user->id === auth()->id())
                <div class="bg-yellow-900 border border-yellow-700 text-yellow-100 px-3 py-2 rounded text-sm">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    You are editing your own account
                </div>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-user mr-2"></i>Full Name *
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $user->name) }}"
                    required
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="John Doe"
                >
                @error('name')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-envelope mr-2"></i>Email Address *
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $user->email) }}"
                    required
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="admin@example.com"
                >
                @error('email')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone -->
            <div class="mb-6">
                <label for="phone" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-phone mr-2"></i>Phone Number
                </label>
                <input 
                    type="text" 
                    id="phone" 
                    name="phone" 
                    value="{{ old('phone', $user->phone) }}"
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="+1 234 567 8900"
                >
                @error('phone')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            @if($user->id !== auth()->id())
            <div class="mb-6">
                <label class="flex items-center text-gray-300">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1"
                        {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 focus:ring-2"
                    >
                    <span class="ml-2">Active account</span>
                </label>
                @error('is_active')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <!-- Account Info -->
            <div class="mb-6 p-4 bg-gray-700 rounded-lg">
                <p class="text-gray-400 text-xs mb-2">Account Information</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-400 text-xs">Created</p>
                        <p class="text-white text-sm">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs">Last Updated</p>
                        <p class="text-white text-sm">{{ $user->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Password Change Notice -->
            <div class="mb-6 bg-blue-900 border border-blue-700 text-blue-100 px-4 py-3 rounded-lg">
                <i class="fas fa-info-circle mr-2"></i>
                To change the password, please use the forgot password functionality from the login page.
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.users.index') }}" 
                   class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
