<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags untuk pengaturan karakter dan viewport -->
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title> Login </title>
    <!-- Import Tailwind CSS dari CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Import asset CSS dan JavaScript dari Laravel Vite -->
    @vite('resources/js/app.js')
    <!-- Import Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
</head>

<!-- Body dengan background abu-abu muda dan konten di tengah -->
<body class=" flex items-center justify-center min-h-screen">
    <!-- Container utama dengan max width dan flex layout -->
    <div class="flex items-stretch overflow-hidden rounded-3xl shadow-lg max-w-3xl mx-auto">
        <!-- Bagian gambar (hanya muncul di layar medium ke atas) -->
        <div class="hidden md:block w-1/3">
            <img alt="Plant Factory" 
                class="w-full h-full object-cover" 
                src="assets/Plant-Factory.jpg"/>
        </div>
        <!-- Container form dengan background putih -->
        <div class="w-full md:w-2/3 p-8 bg-white">
            <!-- Logo/ikon di bagian atas -->
            <div class="flex items-center justify-center mb-8">
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
                <div class="flex items-center gap-3">
                    <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L3 7V17L12 22L21 17V7L12 2Z" stroke="teal" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M12 2L3 7L12 12L21 7L12 2Z" stroke="teal" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M12 22V12" stroke="teal" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                    <h1 class="text-3xl font-bold text-black">ShalMonic</h1>
                </div>
            </div>
            <!-- Judul form -->
            <h2 class="text-xl font-bold text-teal-900 mb-1">
                Your Plant Factory Control Assistant,
            </h2>
            <!-- Deskripsi form -->
            <p class="text-gray-400 mb-6">Helping you to make right decision and control your plant!</p>
            <!-- Form pendaftaran -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Input field untuk email -->
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="email">
                        Email address
                    </label>
                    <input class="w-full px-4 py-2 border rounded-full border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-700" 
                        id="email" 
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        autofocus/>
                </div>
                <!-- Input field untuk password -->
                <div class="mb-6">
                    <label class="block text-gray-700 mb-2" for="password">
                        Password (maks. 24 characters)
                    </label>
                    <input class="w-full px-4 py-2 border rounded-full border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-teal-700" 
                        id="password"
                        name="password"
                        type="password"
                        required/>
                </div>
                <!-- Remember Me -->
                <div class="mb-6 flex items-center">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember_me" name="remember" class="rounded border-gray-300">
                        <label for="remember_me" class="ml-2 text-sm text-gray-600">Remember me</label>
                    </div>
                </div>
                <!-- Tombol submit -->
                <button type="submit" class="w-full btn-gradient py-2 rounded-full font-semibold text-lg">
                    Continue
                </button>
            </form>
            <!-- Register Link -->
            <p class="mt-4 text-center text-sm text-gray-600">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Register now</a>
            </p>
        </div>
    </div>
</body>
</html>

