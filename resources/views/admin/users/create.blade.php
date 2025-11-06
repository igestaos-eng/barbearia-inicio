@extends('admin.layouts.app')

@section('title', 'Create Admin User')
@section('page-title', 'Create New Admin User')

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
        <div class="mb-6 bg-blue-900 border border-blue-700 text-blue-100 px-4 py-3 rounded-lg">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Note:</strong> Only SuperAdmin users can create admin or superadmin accounts.
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-user mr-2"></i>Full Name *
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}"
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
                    value="{{ old('email') }}"
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
                    value="{{ old('phone') }}"
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="+1 234 567 8900"
                >
                @error('phone')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role -->
            <div class="mb-6">
                <label for="role" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-user-tag mr-2"></i>Role *
                </label>
                <select 
                    id="role" 
                    name="role" 
                    required
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                >
                    <option value="">Select role...</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>SuperAdmin</option>
                </select>
                @error('role')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs mt-2">
                    <i class="fas fa-info-circle mr-1"></i>SuperAdmin has access to manage other admin users
                </p>
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-lock mr-2"></i>Password *
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
                <p class="text-gray-400 text-xs mt-2">
                    <i class="fas fa-info-circle mr-1"></i>Minimum 8 characters with mixed case, numbers, and symbols
                </p>
            </div>

            <!-- Password Confirmation -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-300 text-sm font-medium mb-2">
                    <i class="fas fa-lock mr-2"></i>Confirm Password *
                </label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required
                    class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                    placeholder="••••••••"
                >
                @error('password_confirmation')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Active Status -->
            <div class="mb-6">
                <label class="flex items-center text-gray-300">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 focus:ring-2"
                    >
                    <span class="ml-2">Active account</span>
                </label>
                @error('is_active')
                    <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                @enderror
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
                    Create Admin User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
