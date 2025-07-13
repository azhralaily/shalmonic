<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Register - ShalMonic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/js/app.js')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
</head>
<body class="flex items-center justify-center min-h-screen">
<style>
    body {
     background: linear-gradient(135deg, #f8fafc, #e0e7ff, #c7f9cc, #fceabb);
     background-size: 400% 400%;
     animation: gradient-animation 10s ease infinite;
     font-family: 'Poppins', sans-serif;
    }
        @keyframes gradient-animation {
                0% {
                    background-position: 0% 50%;
                }
                50% {
                    background-position: 100% 50%;
                }
                100% {
                    background-position: 0% 50%;
                }
            }
         .btn-gradient {
                background: linear-gradient(90deg, #38bdf8 0%, #4ade80 100%);
                color: white;
                transition: background 0.3s, transform 0.2s;
                box-shadow: 0 2px 8px rgba(56,189,248,0.15);
            }
            .btn-gradient:hover {
                background: linear-gradient(90deg, #4ade80 0%, #38bdf8 100%);
                transform: translateY(-2px) scale(1.03);
                box-shadow: 0 6px 20px rgba(56,189,248,0.18);
     }
</style>
    <div class="w-full max-w-md bg-white p-8 rounded-3xl shadow-lg mx-auto">
        <!-- Logo/ikon di bagian atas -->
        <div class="flex items-center justify-center mb-8">
            <div class="flex items-center gap-3">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L3 7V17L12 22L21 17V7L12 2Z" stroke="teal" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M12 2L3 7L12 12L21 7L12 2Z" stroke="teal" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M12 22V12" stroke="teal" stroke-width="2" stroke-linejoin="round"/>
                </svg>
                <h1 class="text-3xl font-bold text-black">ShalMonic</h1>
            </div>
        </div>
        <h2 class="text-xl font-bold text-teal-900 mb-1 text-center">
            Create Your Account
        </h2>
        <p class="text-gray-400 mb-6 text-center">Join us to start controlling your plant factory!</p>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <!-- Name -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="name">Full Name</label>
                <input class="w-full px-4 py-2 border rounded-full border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-700" 
                    id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"/>
                @error('name')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <!-- Email -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="email">Email address</label>
                <input class="w-full px-4 py-2 border rounded-full border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-700" 
                    id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"/>
                @error('email')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <!-- Password -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2" for="password">Password</label>
                <input class="w-full px-4 py-2 border rounded-full border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-700" 
                    id="password" name="password" type="password" required autocomplete="new-password"/>
                @error('password')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <!-- Confirm Password -->
            <div class="mb-6">
                <label class="block text-gray-700 mb-2" for="password_confirmation">Confirm Password</label>
                <input class="w-full px-4 py-2 border rounded-full border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-700" 
                    id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"/>
                @error('password_confirmation')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="w-full btn-gradient py-2 rounded-full font-semibold text-lg">
                Register
            </button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-600">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login here</a>
        </p>
    </div>
</body>
</html>

