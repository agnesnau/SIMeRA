<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMeRA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-white">

    <div class="flex min-h-screen">
        
        <!-- BAGIAN KIRI: Form Login -->
        <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24 w-full lg:w-[45%] bg-white z-10 relative">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                
                <!-- Logo & Judul -->
                <div class="mb-10">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-lg shadow-emerald-200">
                            <!-- Ikon Arsip/Database -->
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900 tracking-tight">SIMeRA</span>
                    </div>
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900">Selamat Datang</h2>
                    <p class="mt-2 text-sm text-gray-500">
                        Masuk untuk mengelola rekam medis & retensi arsip.
                    </p>
                </div>

                <!-- Alert Error -->
                @if ($errors->any())
                <div class="mb-6 rounded-lg bg-red-50 p-4 border border-red-100">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ $errors->first() }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Form -->
                <form action="{{ route('login') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="username" class="block text-sm font-medium leading-6 text-gray-900">Username / NIP</label>
                        <div class="mt-2">
                            <input id="username" name="username" type="text" autocomplete="username" required class="block w-full rounded-lg border-0 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 pl-4" placeholder="Masukkan ID Pengguna">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-lg border-0 py-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 pl-4" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-600">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-900">Ingat saya</label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-emerald-600 hover:text-emerald-500">Lupa password?</a>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-lg bg-emerald-700 px-3 py-3 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-emerald-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-all">
                            Masuk
                        </button>
                    </div>
                </form>
                
                <p class="mt-10 text-center text-xs text-gray-500">
                    &copy; 2026 UPDT Puskesmas Silo 1. Dilindungi Hak Cipta.
                </p>
            </div>
        </div>

        <!-- BAGIAN KANAN: Gambar Full Height -->
        <div class="relative hidden w-0 flex-1 lg:block">
            <!-- Gambar Background -->
            <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1586773860418-d37222d8fce3?q=80&w=2073&auto=format&fit=crop" alt="Medical Archive">
            
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-emerald-900/90 to-emerald-900/40 mix-blend-multiply"></div>
            
            <!-- Text on Image -->
            <div class="absolute bottom-0 left-0 right-0 p-20 text-white">
                <h2 class="text-4xl font-bold mb-4">Efisiensi Arsip Medis</h2>
                <p class="text-lg text-emerald-100 max-w-xl">
                    Sistem informasi terintegrasi untuk manajemen retensi rekam medis, verifikasi fisik, dan pelaporan pemusnahan yang akurat.
                </p>
            </div>
        </div>
        
    </div>

</body>
</html>