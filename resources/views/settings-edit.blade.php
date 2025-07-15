<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User - ShalMonic</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite('resources/js/app.js')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <style>
        body{background:linear-gradient(135deg,#f8fafc,#e0e7ff,#c7f9cc,#fceabb);background-size:400% 400%;animation:gradient-animation 10s ease infinite;font-family:'Figtree',sans-serif}
        @keyframes gradient-animation{0%{background-position:0 50%}50%{background-position:100% 50%}100%{background-position:0 50%}}
        .btn-gradient {background: linear-gradient(90deg, #38bdf8 0%, #4ade80 100%);color: white;transition: all 0.3s;box-shadow: 0 2px 8px rgba(56,189,248,0.15);}
        .btn-gradient:hover {background: linear-gradient(90deg, #4ade80 0%, #38bdf8 100%);transform: translateY(-2px) scale(1.03);box-shadow: 0 6px 20px rgba(56,189,248,0.18);}
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <nav class="bg-white/75 backdrop-blur-lg shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 hover:opacity-75 transition-opacity">
                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L3 7V17L12 22L21 17V7L12 2Z" stroke="teal" stroke-width="2" stroke-linejoin="round"/><path d="M12 2L3 7L12 12L21 7L12 2Z" stroke="teal" stroke-width="2" stroke-linejoin="round"/><path d="M12 22V12" stroke="teal" stroke-width="2" stroke-linejoin="round"/></svg>
                        <span class="text-2xl font-semibold text-gray-800">ShalMonic</span>
                    </a>
                </div>
                <div class="hidden sm:ml-1 sm:flex sm:items-center">
                    <a href="{{ route('dashboard') }}" class="px-5 py-2 font-light hover:text-gray-900 {{ request()->routeIs('dashboard') ? 'text-teal-600 border-b-2 border-teal-600' : 'text-gray-600' }}">Dashboard</a>
                    <a href="{{ route('controls') }}" class="px-5 py-2 font-light hover:text-gray-900 {{ request()->routeIs('controls') ? 'text-teal-600 border-b-2 border-teal-600' : 'text-gray-600' }}">Controls</a>
                    <a href="{{ route('dataoverview') }}" class="px-5 py-2 font-light hover:text-gray-900 {{ request()->routeIs('dataoverview') ? 'text-teal-600 border-b-2 border-teal-600' : 'text-gray-600' }}">Data Overview</a>
                    @if(Auth::check() && Auth::user()->role == 'admin')
                        <a href="{{ route('settings.index') }}" class="px-5 py-2 font-light hover:text-gray-900 {{ request()->routeIs('settings.*') ? 'text-teal-600 border-b-2 border-teal-600' : 'text-gray-600' }}">Settings</a>
                    @endif
                </div>
                <div class="flex items-center">
                    <form method="POST" action="{{ route('logout') }}?redirect_to=dashboard">
                        @csrf
                        <button type="submit" class="w-full text-sm bg-teal-600 text-white rounded-md hover:bg-teal-700 transition-colors px-4 py-2 font-light">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1 w-full max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-teal-800 text-3xl sm:text-4xl font-bold">Edit User</h1>
            <p class="text-gray-500 mt-1">Update details for <span class="font-semibold text-gray-600">{{ $user->name }}</span>.</p>
        </div>

        <div class="bg-white/75 backdrop-blur-lg border border-gray-200/80 shadow-md rounded-2xl p-6 sm:p-8">
            <form method="post" action="{{ route('settings.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email Address</label>
                    <div class="mt-2">
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                     @error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium leading-6 text-gray-900">Role</label>
                    <div class="mt-2">
                        <select id="role" name="role" class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="user" @selected(old('role', $user->role) == 'user')>User</option>
                            <option value="admin" @selected(old('role', $user->role) == 'admin')>Admin</option>
                        </select>
                    </div>
                     @error('role')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                
                <div class="mt-8 flex items-center justify-end gap-x-4 border-t border-gray-900/10 pt-6">
                    <a href="{{ route('settings.index') }}" class="text-sm font-semibold leading-6 text-gray-700 hover:text-gray-900">Cancel</a>
                    <button type="submit" class="btn-gradient font-semibold px-4 py-2 rounded-lg text-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>