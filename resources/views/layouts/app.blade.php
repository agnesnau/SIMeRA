<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMeRA - @yield('title')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        [x-cloak] { display: none !important; }
        
        /* SIDEBAR STYLING */
        .sidebar {
            width: 280px;
            background-color: #064e3b; /* Emerald 900 */
            color: white;
            min-height: 100vh;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            flex-shrink: 0;
        }

        .nav-link {
            color: #d1fae5;
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 12px;
            margin: 0.25rem 1rem;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: white;
            transform: translateX(4px);
        }

        .nav-link.active {
            background-color: #10b981;
            color: white;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2), 0 4px 6px -2px rgba(16, 185, 129, 0.1);
        }

        .sub-nav-link {
            padding: 0.6rem 1rem 0.6rem 2.5rem;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(209, 250, 229, 0.6);
            border-radius: 10px;
            margin: 0.1rem 1rem 0.1rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sub-nav-link:hover, .sub-nav-link.active-sub {
            color: white;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .rotate-180 { transform: rotate(180deg); }
        .sidebar-header { border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
    </style>
</head>
<body class="flex min-h-screen">

    <aside class="sidebar sticky-top overflow-y-auto flex flex-col">
        <div class="p-6 mb-6 sidebar-header flex items-center gap-3">
            <div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center border border-white/20 shadow-xl">
                <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <div class="flex flex-col">
                <span class="text-xl font-extrabold tracking-tighter uppercase leading-none">SIMeRA</span>
                <span class="text-[9px] font-bold text-emerald-400/60 uppercase tracking-[0.2em] mt-1">Sistem Manajemen Retensi Arsip</span>
            </div>
        </div>

        <ul class="nav flex-column flex-1">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
            </li>

            <div class="px-7 mt-8 mb-2 text-[10px] font-black text-emerald-400/40 uppercase tracking-[0.3em]">Data Master</div>
            
            <li class="nav-item">
                <a href="{{ route('patients.index') }}" class="nav-link {{ request()->is('master/patients*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Data Pasien
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('visits.index') }}" class="nav-link {{ request()->is('master/visits*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Data Kunjungan
                </a>
            </li>

            <div class="px-7 mt-8 mb-2 text-[10px] font-black text-emerald-400/40 uppercase tracking-[0.3em]">Kearsipan</div>

            <li class="nav-item" x-data="{ open: {{ request()->is('retensi*') ? 'true' : 'false' }} }">
                <a href="javascript:void(0)" @click="open = !open" class="nav-link justify-between {{ request()->is('retensi*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Retensi RM
                    </div>
                    <small :class="open ? 'rotate-180' : ''" class="transition-transform duration-300">▼</small>
                </a>
                <div x-show="open" x-cloak x-transition.origin.top class="space-y-1 py-1">
                    <a href="{{ route('retensi.index') }}" class="sub-nav-link {{ request()->routeIs('retensi.index') ? 'text-white bg-white/5 font-bold' : '' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('retensi.index') ? 'bg-emerald-400' : 'bg-white/20' }}"></span>
                        Daftar Utama
                    </a>
                    <a href="{{ route('retensi.pemilahan') }}" class="sub-nav-link {{ request()->routeIs('retensi.pemilahan') ? 'text-white bg-white/5 font-bold' : '' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('retensi.pemilahan') ? 'bg-blue-400 animate-pulse shadow-[0_0_8px_rgba(96,165,250,1)]' : 'bg-white/20' }}"></span>
                        Pemilahan Berkas
                    </a>
                </div>
            </li>

           <li class="nav-item" x-data="{ open: {{ request()->is('pemusnahan*') ? 'true' : 'false' }} }">
                <a href="javascript:void(0)" @click="open = !open" class="nav-link justify-between {{ request()->is('pemusnahan*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pemusnahan RM
                    </div>
                    <small :class="open ? 'rotate-180' : ''" class="transition-transform duration-300">▼</small>
                </a>

                <div x-show="open" x-cloak x-transition.origin.top class="space-y-1 py-1">
                    
                    <a href="{{ route('pemusnahan.index') }}" class="sub-nav-link {{ request()->routeIs('pemusnahan.index') ? 'text-white bg-white/5 font-bold' : '' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('pemusnahan.index') ? 'bg-red-400' : 'bg-white/20' }}"></span>
                        Daftar Utama
                    </a>

                    <a href="{{ route('pemusnahan.eksekusi') }}" class="sub-nav-link {{ request()->routeIs('pemusnahan.eksekusi') ? 'text-white bg-white/5 font-bold' : '' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('pemusnahan.eksekusi') ? 'bg-red-600 animate-pulse shadow-[0_0_8px_rgba(220,38,38,1)]' : 'bg-white/20' }}"></span>
                        Eksekusi Berkas
                    </a>
                </div>
            </li>

            @if(auth()->user() && auth()->user()->level !== 'petugas')
            <li class="nav-item">
                <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Berita Acara
                </a>
            </li>
            @endif

            @if(auth()->user() && strtolower(auth()->user()->level) === 'admin')
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->is('master/users*') ? 'active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    Manajemen User
                </a>
            </li>
            @endif
        </ul>

        <div class="p-6 mt-auto border-t border-white/5 mx-4 mb-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-3 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white rounded-2xl text-xs font-black transition-all flex items-center justify-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    KELUAR SISTEM
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-grow min-h-screen overflow-x-hidden flex flex-col">
        <nav class="bg-white/90 backdrop-blur-xl sticky top-0 px-8 py-4 border-b border-slate-200 flex justify-between items-center z-50 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="h-8 w-1.5 bg-emerald-600 rounded-full"></div>
                <h1 class="text-sm font-black text-slate-800 uppercase tracking-widest">
                    @yield('title')
                </h1>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-xs font-black text-slate-900 uppercase leading-none">{{ auth()->user()->nama_lengkap ?? 'Administrator' }}</p>
                    <p class="text-[9px] text-emerald-600 font-bold uppercase tracking-widest mt-1">{{ auth()->user()->level ?? 'Level Akses' }}</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-sm border-2 border-white shadow-md overflow-hidden">
                @if(auth()->user() && auth()->user()->foto)
                    <img src="{{ asset('storage/' . auth()->user()->foto) }}" class="w-full h-full object-cover">
                @else
                  {{ substr(auth()->user()->nama_lengkap ?? 'A', 0, 1) }}
                     @endif
            </div>
        </nav>

        <div class="p-8 flex-1">
            @yield('content')
        </div>
        
        <footer class="p-8 pt-0 text-[10px] text-slate-400 font-bold uppercase tracking-widest text-center">
            &copy; 2026 Puskesmas Silo 1 - Jember. All Rights Reserved.
        </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>