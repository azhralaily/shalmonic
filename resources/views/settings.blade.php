<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings - ShalMonic</title>
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
                {{-- Link yang bisa dilihat semua user yang sudah login --}}
                <a href="{{ route('dashboard') }}" class="px-5 py-2 font-light hover:text-gray-900 {{ request()->routeIs('dashboard') ? 'text-teal-600 border-b-2 border-teal-600' : 'text-gray-600' }}">Dashboard</a>
                
                {{-- Link HANYA untuk Admin dan Operator --}}
                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'operator')
                <a href="{{ route('controls') }}" class="px-5 py-2 font-light hover:text-gray-900 {{ request()->routeIs('controls') ? 'text-teal-600 border-b-2 border-teal-600' : 'text-gray-600' }}">Controls</a>
                <a href="{{ route('dataoverview') }}" class="px-5 py-2 font-light hover:text-gray-900 {{ request()->routeIs('dataoverview') ? 'text-teal-600 border-b-2 border-teal-600' : 'text-gray-600' }}">Data Overview</a>
                @endif
                
                {{-- Link HANYA untuk Admin --}}
                @if(Auth::user()->role == 'admin')
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
            <h1 class="text-teal-800 text-3xl sm:text-4xl font-bold">Account Management</h1>
            <p class="text-gray-500 mt-1">Manage user accounts, roles, and permissions.</p>
        </div>

        <div class="bg-white/75 backdrop-blur-lg border border-gray-200/80 shadow-md rounded-2xl p-6">
            <section>
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">User List</h2>
                        <p class="mt-1 text-sm text-gray-600">A list of all users registered in the system.</p>
                    </div>
                    <button x-data @click.prevent="$dispatch('open-modal', 'add-user-modal')" class="btn-gradient font-semibold px-4 py-2 rounded-lg flex items-center justify-center text-sm">
                        <i class="fas fa-plus mr-2"></i>Add Account
                    </button>
                </div>

                {{-- Notifikasi --}}
                @if(session('status') || session('error'))
                <div class="mb-4">
                    @if (session('status') === 'user-created')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-medium text-green-700">✓ New account created successfully.</p>
                    @endif
                    @if (session('status') === 'user-updated')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-medium text-green-700">✓ User account has been updated.</p>
                    @endif
                    @if (session('status') === 'user-deleted')
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-medium text-green-700">✓ User account has been deleted.</p>
                    @endif
                    @if (session('error'))
                    <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 4000)" class="text-sm font-medium text-red-700">✗ {{ session('error') }}</p>
                    @endif
                </div>
                @endif
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->role == 'admin' ? 'bg-teal-100 text-teal-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold space-x-4">
                                    <a href="{{ route('settings.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    <form action="{{ route('settings.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 disabled:opacity-40 disabled:cursor-not-allowed" {{ $user->id === Auth::id() ? 'disabled' : '' }}>Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No users found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    {{-- Modal untuk Tambah Akun --}}
    <x-modal name="add-user-modal" :show="$errors->isNotEmpty()" focusable>
        <form method="post" action="{{ route('settings.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Add New User Account</h2>
            <p class="mt-1 text-sm text-gray-600">Ensure the account uses a permanent email address and a strong password.</p>
            <div class="mt-6"><x-input-label for="name" value="Name" /><x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus /><x-input-error :messages="$errors->get('name')" class="mt-2" /></div>
            <div class="mt-6"><x-input-label for="email" value="Email" /><x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required /><x-input-error :messages="$errors->get('email')" class="mt-2" /></div>
            <div class="mt-6"><x-input-label for="password" value="Password" /><x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password')" class="mt-2" /></div>
            <div class="mt-6"><x-input-label for="password_confirmation" value="Confirm Password" /><x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required /><x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" /></div>
            <div class="mt-6">
                <x-input-label for="role" value="Role" />
                <select id="role" name="role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="user" @selected(old('role') == 'user')>User</option>
                    <option value="operator" @selected(old('role') == 'operator')>Operator</option> {{-- Tambahkan ini --}}
                    <option value="admin" @selected(old('role') == 'admin')>Admin</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                <x-primary-button class="ms-3">Add Account</x-primary-button>
            </div>
        </form>
    </x-modal>
</body>
</html>