<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMeRA - Sistem Informasi Manajemen Retensi Arsip</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        [x-cloak] { display: none !important; } 
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: #064e3b; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #10b981; border-radius: 10px; }
    </style>
</head>
<body x-data="{ showUserModal: false, showGuideModal: false }" class="bg-gray-50 flex h-screen overflow-hidden text-gray-800">

    <aside class="w-64 bg-emerald-900 text-white flex-shrink-0 flex flex-col shadow-2xl z-20">
        
        <div class="h-16 flex items-center px-6 bg-emerald-950 border-b border-emerald-800 shadow-sm">
            <svg class="w-8 h-8 text-emerald-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span class="font-bold text-xl tracking-wide uppercase italic">SIMeRA</span>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 sidebar-scroll">
            
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('dashboard') ? 'bg-emerald-800 text-white shadow-lg' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="font-medium text-sm">Dashboard</span>
            </a>

            <div x-data="{ open: {{ request()->is('patients*') || request()->is('users*') || request()->is('visits*') ? 'true' : 'false' }} }" class="space-y-1 pt-2">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg transition-all text-emerald-100 hover:bg-emerald-800/50 hover:text-white group">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-emerald-400/70 group-hover:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span class="font-medium text-sm">Master Data</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180 text-white' : 'text-emerald-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-cloak class="pl-4 space-y-1">
                    <div class="border-l-2 border-emerald-800 pl-4 space-y-1">
                        @if(auth()->user()->level === 'admin')
                            <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('users.*') ? 'text-white font-semibold bg-emerald-800' : 'text-emerald-300 hover:text-white hover:bg-emerald-800/50' }}">Data Pengguna</a>
                        @endif
                        <a href="{{ route('patients.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('patients.*') ? 'text-white font-semibold bg-emerald-800' : 'text-emerald-300 hover:text-white hover:bg-emerald-800/50' }}">Data Pasien</a>
                        <a href="{{ route('visits.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('visits.*') ? 'text-white font-semibold bg-emerald-800' : 'text-emerald-300 hover:text-white hover:bg-emerald-800/50' }}">Data Kunjungan</a>
                    </div>
                </div>
            </div>

            <a href="{{ route('retensi.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('retensi.*') ? 'bg-emerald-800 text-white shadow-lg' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium text-sm">Retensi RM</span>
            </a>
            <a href="{{ route('pemusnahan.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('pemusnahan.*') ? 'bg-emerald-800 text-white shadow-lg' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                <span class="font-medium text-sm">Pemusnahan RM</span>
            </a>
            @if(auth()->user()->level !== 'petugas')
            <a href="{{ route('laporan.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('laporan.*') ? 'bg-emerald-800 text-white shadow-lg' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="font-medium text-sm">Pelaporan & Statistik</span>
            </a>
            @endif
        </nav>

        <div class="mt-auto border-t border-emerald-800 bg-emerald-950/30">
            <button @click="showGuideModal = true" class="w-full flex items-center px-6 py-4 transition-all text-emerald-100 hover:bg-emerald-800/50 hover:text-white group">
                <svg class="w-5 h-5 mr-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.168.477-4.5 1.253"></path></svg>
                <span class="font-bold text-sm tracking-wide">Petunjuk Teknis</span>
            </button>

            <div class="border-t border-emerald-800 p-4">
                <div class="flex items-center gap-3">
                    <button @click="showUserModal = true" class="flex flex-1 items-center gap-3 min-w-0 text-left group transition-all">
                        <div class="w-9 h-9 rounded-full bg-emerald-500 flex items-center justify-center font-bold text-white text-sm shadow-sm ring-2 ring-emerald-900 group-hover:ring-emerald-400 overflow-hidden">
                            @if(Auth::user()->foto)
                                <img src="{{ asset('storage/'.Auth::user()->foto) }}" class="w-full h-full object-cover">
                            @else
                                {{ substr(Auth::user()->nama_lengkap ?? 'U', 0, 1) }}
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-emerald-50 truncate leading-tight group-hover:text-white">{{ Auth::user()->nama_lengkap }}</p>
                            <p class="text-[10px] text-emerald-400 font-medium truncate uppercase tracking-widest">{{ auth()->user()->level }}</p>
                        </div>
                    </button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-emerald-500 hover:text-red-400 transition-colors p-1"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg></button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col relative overflow-hidden bg-slate-50">
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 z-10 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800 tracking-tight">@yield('title', 'Dashboard Utama')</h2>
        </header>
        <div class="flex-1 overflow-y-auto p-8 scroll-smooth">@yield('content')</div>
    </main>

    <div x-show="showUserModal" x-cloak class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-emerald-950/60 backdrop-blur-sm">
        <div @click.away="showUserModal = false" class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden animate-fade-in-up">
            <div class="h-24 bg-gradient-to-r from-emerald-800 to-emerald-600 relative"></div>
            <div class="px-6 pb-8 text-center -mt-12 relative">
                <div class="w-24 h-24 rounded-2xl bg-white p-1 mx-auto shadow-xl">
                    <div class="w-full h-full rounded-xl bg-emerald-100 flex items-center justify-center overflow-hidden">
                        @if(Auth::user()->foto)
                            <img src="{{ asset('storage/'.Auth::user()->foto) }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-12 h-12 text-emerald-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        @endif
                    </div>
                </div>
                <div class="mt-4">
                    <h3 class="text-lg font-black text-gray-800 uppercase italic">SIMeRA</h3>
                    <p class="text-base font-bold text-gray-700 leading-tight mt-1">{{ Auth::user()->nama_lengkap }}</p>
                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mt-1">{{ auth()->user()->level }}</p>
                </div>
                <div class="mt-6 space-y-2 text-left">
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100"><p class="text-[9px] text-gray-400 font-bold uppercase">NIP / Kode Petugas</p><p class="text-sm font-medium text-gray-700">{{ Auth::user()->nip ?? '-' }}</p></div>
                </div>
                <div class="mt-8 flex gap-2">
                    <button @click="showUserModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-xs font-bold uppercase">Tutup</button>
                    <a href="{{ route('users.edit', Auth::user()->id) }}" class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase text-center shadow-lg shadow-emerald-200">Edit Profil</a>
                </div>
            </div>
        </div>
    </div>

    </body>
</html>