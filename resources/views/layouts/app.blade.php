<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMeRA - Sistem Informasi Manajemen Retensi Arsip</title>
    
    <!-- 1. CDN Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- 2. Alpine.js (Untuk Interaktivitas UI) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- 3. Font Google (Plus Jakarta Sans) -->
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

    <!-- SIDEBAR UTAMA (PANEL KIRI) -->
    <aside class="w-64 bg-emerald-900 text-white flex-shrink-0 flex flex-col shadow-2xl z-20">
        
        <!-- Header Logo Sistem -->
        <div class="h-16 flex items-center px-6 bg-emerald-950 border-b border-emerald-800 shadow-sm">
            <svg class="w-8 h-8 text-emerald-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <span class="font-bold text-xl tracking-wide uppercase">SIMeRA</span>
        </div>

        <!-- Menu Navigasi Sidebar -->
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 sidebar-scroll">
            
            <!-- Link Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('dashboard') ? 'bg-emerald-800 text-white shadow-lg ring-1 ring-emerald-700' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-emerald-400' : 'text-emerald-400/70 group-hover:text-emerald-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <!-- Dropdown Master Data -->
            <div x-data="{ open: {{ request()->is('master*') || request()->is('users*') || request()->is('patients*') || request()->is('visits*') ? 'true' : 'false' }} }" class="space-y-1 pt-2">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg transition-all text-emerald-100 hover:bg-emerald-800/50 hover:text-white group">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-emerald-400/70 group-hover:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span class="font-medium">Master Data</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180 text-white' : 'text-emerald-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" x-collapse x-cloak class="pl-4 space-y-1">
                    <div class="border-l-2 border-emerald-800 pl-4 space-y-1">
                        <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('users.*') ? 'text-white font-semibold bg-emerald-800' : 'text-emerald-300 hover:text-white hover:bg-emerald-800/50' }}">Data Pengguna</a>
                        <a href="{{ route('patients.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('patients.*') ? 'text-white font-semibold bg-emerald-800' : 'text-emerald-300 hover:text-white hover:bg-emerald-800/50' }}">Data Pasien</a>
                        <a href="{{ route('visits.index') }}" class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('visits.*') ? 'text-white font-semibold bg-emerald-800' : 'text-emerald-300 hover:text-white hover:bg-emerald-800/50' }}">Data Kunjungan</a>
                    </div>
                </div>
            </div>

            <!-- Modul Retensi RM -->
            <a href="{{ route('retensi.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('retensi.*') ? 'bg-emerald-800 text-white shadow-lg ring-1 ring-emerald-700' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('retensi.*') ? 'text-emerald-400' : 'text-emerald-400/70 group-hover:text-emerald-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">Retensi RM</span>
            </a>

            <!-- Modul Pemusnahan RM -->
            <a href="{{ route('pemusnahan.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('pemusnahan.*') ? 'bg-emerald-800 text-white shadow-lg ring-1 ring-emerald-700' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('pemusnahan.*') ? 'text-emerald-400' : 'text-emerald-400/70 group-hover:text-emerald-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span class="font-medium">Pemusnahan RM</span>
            </a>

            <!-- Modul Pelaporan -->
            <a href="{{ route('laporan.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('laporan.*') ? 'bg-emerald-800 text-white shadow-lg ring-1 ring-emerald-700' : 'text-emerald-100 hover:bg-emerald-800/50 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 {{ request()->routeIs('laporan.*') ? 'text-emerald-400' : 'text-emerald-400/70 group-hover:text-emerald-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="font-medium">Pelaporan</span>
            </a>
        </nav>

        <!-- FOOTER SIDEBAR (PROFIL & PANDUAN) -->
        <div class="mt-auto border-t border-emerald-800 bg-emerald-950/50">
            
            <!-- Tombol Panduan Teknis -->
            <button @click="showGuideModal = true" class="w-full flex items-center px-6 py-3 text-emerald-300 hover:text-white hover:bg-emerald-800/50 transition-colors text-sm border-b border-emerald-800/30 group">
                <svg class="w-4 h-4 mr-3 text-emerald-500 group-hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span>Panduan Teknis</span>
            </button>

            <!-- Profil Pengguna (Dapat Diklik) -->
            <div class="p-4 flex items-center gap-3">
                <button @click="showUserModal = true" class="flex flex-1 items-center gap-3 min-w-0 text-left hover:bg-emerald-900/40 p-1.5 rounded-lg transition-all group outline-none">
                    <div class="w-9 h-9 rounded-full bg-emerald-500 flex items-center justify-center font-bold text-white text-sm shadow-sm ring-2 ring-emerald-900 group-hover:ring-emerald-400 transition-all">
                        {{ substr(Auth::user()->nama_lengkap ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-emerald-50 truncate group-hover:text-white">{{ Auth::user()->nama_lengkap ?? 'Administrator' }}</p>
                        <p class="text-xs text-emerald-400 truncate group-hover:text-emerald-300">{{ ucfirst(Auth::user()->level ?? 'Petugas') }}</p>
                    </div>
                </button>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="text-emerald-500 hover:text-red-400 hover:bg-emerald-900 p-2 rounded-lg transition-colors" title="Keluar dari Sistem">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- PANEL KONTEN UTAMA -->
    <main class="flex-1 flex flex-col relative overflow-hidden bg-slate-50">
        <!-- Header Bar Atas -->
        <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 z-10 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800 tracking-tight">@yield('title', 'Dashboard Utama')</h2>
            <div class="hidden md:flex items-center text-sm font-medium text-gray-500 bg-gray-50 px-4 py-1.5 rounded-full border border-gray-200">
                <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </header>

        <!-- Area Scroll Konten -->
        <div class="flex-1 overflow-y-auto p-8 scroll-smooth">
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r shadow-sm flex items-center justify-between animate-fade-in-down">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="$el.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    <!-- MODAL INFORMASI PENGGUNA -->
    <div x-show="showUserModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" @click.self="showUserModal = false" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden" @click.stop>
            <div class="h-24 bg-gradient-to-br from-emerald-600 to-emerald-800 flex items-end justify-center pb-0">
                <div class="w-20 h-20 rounded-full bg-white p-1 mb-[-40px] shadow-lg">
                    <div class="w-full h-full rounded-full bg-emerald-100 flex items-center justify-center text-3xl font-bold text-emerald-700">
                        {{ substr(Auth::user()->nama_lengkap ?? 'A', 0, 1) }}
                    </div>
                </div>
            </div>
            <div class="pt-12 pb-8 px-6 text-center">
                <h3 class="text-xl font-bold text-gray-800">{{ Auth::user()->nama_lengkap ?? 'User Sistem' }}</h3>
                <p class="text-emerald-600 font-medium text-sm mb-6">{{ ucfirst(Auth::user()->level ?? 'Petugas') }}</p>
                <div class="space-y-3 text-left bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Nama ID Pengguna</span>
                        <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->username ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Nomor Induk Pegawai (NIP)</span>
                        <p class="text-sm font-semibold text-gray-700">{{ Auth::user()->nip ?? '-' }}</p>
                    </div>
                </div>
                <button @click="showUserModal = false" class="mt-8 w-full py-2.5 bg-gray-100 text-gray-600 rounded-lg font-bold hover:bg-gray-200 transition-colors">Tutup Jendela</button>
            </div>
        </div>
    </div>

    <!-- MODAL PANDUAN TEKNIS PENGGUNA (DETIL) -->
    <div x-show="showGuideModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" @click.self="showGuideModal = false" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden" @click.stop>
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-emerald-50">
                <h3 class="font-bold text-emerald-800 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    Pituduh Teknis Penggunaan SIMeRA
                </h3>
                <button @click="showGuideModal = false" class="text-emerald-400 hover:text-emerald-600 transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="p-6 max-h-[75vh] overflow-y-auto space-y-6">
                
                <!-- Definisi Logika Dasar -->
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-lg">
                    <h4 class="font-bold text-emerald-800 text-sm mb-1 underline">Logika Retensi Otomatis (2+2=4 Tahun)</h4>
                    <p class="text-xs text-emerald-700 leading-relaxed italic">Berdasarkan SOP Puskesmas Silo 1: Berkas disimpan aktif selama 2 tahun dan inaktif selama 2 tahun. Total masa simpan adalah 4 tahun sebelum dapat dimusnahkan secara permanen.</p>
                </div>

                <!-- 1. Menu Dashboard -->
                <section>
                    <h5 class="font-bold text-gray-800 border-b pb-1 mb-2 flex items-center gap-2"><span class="bg-emerald-600 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px]">1</span> Modul Dashboard</h5>
                    <p class="text-xs text-gray-600 mb-2">Halaman utama untuk memantau kondisi rekam medis secara menyeluruh:</p>
                    <ul class="list-disc pl-5 text-xs text-gray-600 space-y-1">
                        <li><strong>Kartu Statistik:</strong> Memberikan ringkasan jumlah Pengguna, Pasien, serta klasifikasi berkas Aktif dan Inaktif secara instan.</li>
                        <li><strong>Audit Trail:</strong> Menampilkan log aktivitas yang diperbarui secara otomatis tanpa muat ulang halaman. Berfungsi untuk memantau siapa, kapan, dan apa tindakan yang dilakukan pada berkas.</li>
                    </ul>
                </section>

                <!-- 2. Master Data -->
                <section>
                    <h5 class="font-bold text-gray-800 border-b pb-1 mb-2 flex items-center gap-2"><span class="bg-emerald-600 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px]">2</span> Modul Master Data</h5>
                    <ul class="list-disc pl-5 text-xs text-gray-600 space-y-1">
                        <li><strong>Data Pengguna:</strong> Khusus Administrator untuk mengatur hak akses petugas rekam medis.</li>
                        <li><strong>Data Pasien:</strong> Digunakan untuk meregistrasi pasien baru atau <strong>Impor Massal Excel</strong> dari aplikasi SIMKES. Ikon "Mata" memungkinkan petugas melihat profil detail tanpa berpindah halaman (timbul/pop-up).</li>
                        <li><strong>Data Kunjungan:</strong> Mencatat tanggal berobat terakhir pasien. Tanggal inilah yang menjadi penentu status retensi oleh sistem.</li>
                    </ul>
                </section>

                <!-- 3. Retensi RM -->
                <section>
                    <h5 class="font-bold text-gray-800 border-b pb-1 mb-2 flex items-center gap-2"><span class="bg-emerald-600 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px]">3</span> Modul Retensi Rekam Medis</h5>
                    <p class="text-xs text-gray-600 mb-2">Pusat pengendalian sortir berkas fisik:</p>
                    <ul class="list-disc pl-5 text-xs text-gray-600 space-y-1">
                        <li><strong>Verifikasi Fisik:</strong> Gunakan tombol "Upload Nilai Guna" untuk mengunggah bukti digital (foto/scan) berkas yang memiliki nilai hukum atau medis tinggi.</li>
                        <li><strong>Tombol Retensi:</strong> Digunakan untuk memindahkan berkas yang sudah dievaluasi secara fisik ke daftar "Siap Musnah".</li>
                    </ul>
                </section>

                <!-- 4. Pemusnahan RM -->
                <section>
                    <h5 class="font-bold text-gray-800 border-b pb-1 mb-2 flex items-center gap-2"><span class="bg-emerald-600 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px]">4</span> Modul Pemusnahan Rekam Medis</h5>
                    <ul class="list-disc pl-5 text-xs text-gray-600 space-y-1">
                        <li><strong>Daftar Eksekusi:</strong> Menampung berkas yang telah disetujui untuk dimusnahkan secara fisik.</li>
                        <li><strong>Fitur Restore:</strong> Tombol pengaman jika terjadi kesalahan input untuk mengembalikan berkas ke status inaktif.</li>
                        <li><strong>Tindakan Musnahkan:</strong> Langkah terakhir untuk menandai bahwa berkas telah dihancurkan (dicacah/dibakar) secara fisik.</li>
                    </ul>
                </section>

                <!-- 5. Pelaporan -->
                <section>
                    <h5 class="font-bold text-gray-800 border-b pb-1 mb-2 flex items-center gap-2"><span class="bg-emerald-600 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px]">5</span> Modul Pelaporan Digital</h5>
                    <ul class="list-disc pl-5 text-xs text-gray-600 space-y-1">
                        <li><strong>Pembuatan Berita Acara:</strong> Masukkan parameter resmi seperti Nomor Surat, Nama Ketua Pelaksana, dan Lokasi Pemusnahan.</li>
                        <li><strong>Cetak PDF:</strong> Sistem akan men-generate dokumen resmi secara otomatis yang siap dicetak sebagai bukti legalitas proses retensi dan pemusnahan berkas rekam medis.</li>
                    </ul>
                </section>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                <button @click="showGuideModal = false" class="px-6 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold hover:bg-emerald-700 transition-all">Selesai Membaca</button>
            </div>
        </div>
    </div>

</body>
</html>