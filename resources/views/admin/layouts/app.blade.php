<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Barbershop Premium</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-900 text-gray-100" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
            :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
        >
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 bg-gray-900 border-b border-gray-700">
                    <h1 class="text-xl font-bold text-white">Barbershop Admin</h1>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto py-4">
                    <ul class="space-y-1 px-3">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : '' }}">
                                <i class="fas fa-tachometer-alt w-5"></i>
                                <span class="ml-3">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.appointments.index') }}" 
                               class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition {{ request()->routeIs('admin.appointments.*') ? 'bg-gray-700 text-white' : '' }}">
                                <i class="fas fa-calendar-check w-5"></i>
                                <span class="ml-3">Appointments</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.barbers.index') }}" 
                               class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition {{ request()->routeIs('admin.barbers.*') ? 'bg-gray-700 text-white' : '' }}">
                                <i class="fas fa-cut w-5"></i>
                                <span class="ml-3">Barbers</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.services.index') }}" 
                               class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition {{ request()->routeIs('admin.services.*') ? 'bg-gray-700 text-white' : '' }}">
                                <i class="fas fa-briefcase w-5"></i>
                                <span class="ml-3">Services</span>
                            </a>
                        </li>
                        @can('manage-admins')
                        <li>
                            <a href="{{ route('admin.users.index') }}" 
                               class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-gray-700 text-white' : '' }}">
                                <i class="fas fa-users-cog w-5"></i>
                                <span class="ml-3">Admin Users</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </nav>

                <!-- User info and logout -->
                <div class="border-t border-gray-700 p-4">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-gray-300"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400">{{ auth()->user()->role->label() }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 hover:text-white rounded-lg transition">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span class="ml-3">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top bar -->
            <header class="flex items-center justify-between h-16 bg-gray-800 border-b border-gray-700 px-6">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-300 hover:text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Page title -->
                <h2 class="text-xl font-semibold text-white">@yield('page-title', 'Dashboard')</h2>

                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" target="_blank" class="text-gray-300 hover:text-white">
                        <i class="fas fa-external-link-alt"></i>
                        <span class="ml-2 hidden sm:inline">View Site</span>
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-900 p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-4 bg-green-900 border border-green-700 text-green-100 px-4 py-3 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded-lg">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div 
        x-show="sidebarOpen" 
        @click="sidebarOpen = false"
        x-cloak
        class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
    ></div>

    @stack('scripts')
</body>
</html>
