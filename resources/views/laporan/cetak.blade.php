<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Dokumen - SIMeRA</title>
    <style>
        @page { size: A4; margin: 1.5cm 2cm; }
        body { 
            font-family: 'Times New Roman', serif; 
            line-height: 1.5; 
            color: #000; 
            font-size: 11pt; 
        }
        
        /* Kop Surat */
        .kop-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-logo { width: 70px; text-align: center; }
        .kop-text { text-align: center; }
        .kop-text h1 { font-size: 14pt; margin: 0; font-weight: bold; text-transform: uppercase; }
        .kop-text h2 { font-size: 16pt; margin: 0; font-weight: bold; text-transform: uppercase; }
        .kop-text p { margin: 0; font-size: 9pt; font-style: italic; }

        /* Judul Surat */
        .judul { text-align: center; margin-bottom: 20px; }
        .judul h3 { margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: bold; }
        .judul p { margin: 2px 0 0; font-size: 11pt; }

        /* Konten Paragraf */
        .content { text-align: justify; margin-bottom: 15px; }
        .content p { margin-bottom: 10px; text-indent: 40px; }

        /* TANDA TANGAN BERSAMA (ANTI POTONG & FULL CENTER) */
        table.ttd-master {
            width: 100%;
            border: none;
            border-collapse: collapse;
            margin-top: 30px;
            page-break-inside: avoid; /* Memaksa PDF tidak memotong tabel ini setengah-setengah */
        }
        table.ttd-master td {
            border: none !important;
            text-align: center; /* Memastikan semua teks di dalam sel berada tepat di tengah */
            vertical-align: top;
            padding: 0;
        }
        .ttd-p {
            margin: 0; /* Wajib: Menghilangkan margin bawaan agar teks tidak miring */
            line-height: 1.3;
        }
        .ttd-space {
            height: 70px; /* Jarak untuk coretan tanda tangan */
        }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }
        .ttd-nip {
            font-size: 10pt;
            margin: 2px 0 0 0;
        }

        /* Tabel Data Lampiran */
        table.data { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10pt; }
        table.data th, table.data td { border: 1px solid #000; padding: 6px; }
        table.data th { background-color: #f2f2f2; text-align: center; font-weight: bold; text-transform: uppercase; }

        /* CSS UNTUK MEMAKSA PINDAH HALAMAN (LAMPIRAN) */
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    @php
        // FUNGSI TERBILANG UNTUK JUMLAH BERKAS
        function penyebut($nilai) {
            $nilai = abs($nilai);
            $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
            $temp = "";
            if ($nilai < 12) { $temp = " ". $huruf[$nilai]; } 
            else if ($nilai < 20) { $temp = penyebut($nilai - 10). " belas"; } 
            else if ($nilai < 100) { $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10); } 
            else if ($nilai < 200) { $temp = " seratus" . penyebut($nilai - 100); } 
            else if ($nilai < 1000) { $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100); } 
            else if ($nilai < 2000) { $temp = " seribu" . penyebut($nilai - 1000); } 
            else if ($nilai < 1000000) { $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000); } 
            return $temp;
        }

        function terbilang($nilai) {
            $hasil = trim(penyebut($nilai));
            return ucwords($hasil);
        }

        \Carbon\Carbon::setLocale('id');
        $dateObj = \Carbon\Carbon::parse($tanggal);
        
        // Cek apakah ini Usulan atau BA Final
        $isUsulan = ($tipe_dokumen ?? 'berita_acara') == 'pertelaan';
    @endphp

    {{-- ========================================================================= --}}
    {{-- HALAMAN 1: SURAT PENGANTAR / BERITA ACARA --}}
    {{-- ========================================================================= --}}

    {{-- KOP SURAT --}}
    <table class="kop-table" style="border: none;">
        <tr style="border: none;">
            <td class="kop-logo" style="border: none;">
                @if(file_exists(public_path('img/logo-dinkes.png')))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/logo-dinkes.png'))) }}" style="width: 70px;">
                @endif
            </td>
            <td class="kop-text" style="border: none;">
                <h1>PEMERINTAH KABUPATEN JEMBER</h1>
                <h1>DINAS KESEHATAN</h1>
                <h2>UPTD PUSKESMAS SILO 1</h2>
                <p>Jl. Jend. Ahmad Yani No.154, Krajan, Sumberjati, Kec. Silo, Kabupaten Jember<br>
                Telepon: (0331) 521169 | Email: pkmsilo1@gmail.com</p>
            </td>
            <td class="kop-logo" style="border: none;">
                @if(file_exists(public_path('img/logo-puskesmas.jpg')))
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('img/logo-puskesmas.jpg'))) }}" style="width: 70px;">
                @endif
            </td>
        </tr>
    </table>

    {{-- JUDUL SURAT --}}
    <div class="judul">
        <h3 style="text-decoration: underline;">
            @if($isUsulan)
                SURAT USULAN {{ $jenis_ba == 'retensi' ? 'PEMILAHAN & RETENSI' : 'PEMUSNAHAN' }} ARSIP REKAM MEDIS
            @else
                BERITA ACARA {{ $jenis_ba == 'retensi' ? 'RETENSI' : 'PEMUSNAHAN' }} REKAM MEDIS
            @endif
        </h3>
        <p>Nomor: {{ $no_surat }}</p>
    </div>

    {{-- PARAGRAF PEMBUKA YANG SUDAH DIBUAT TOTALITAS --}}
    <div class="content">
        @if($isUsulan)
            {{-- KALIMAT KHUSUS SURAT USULAN --}}
            <p>Sehubungan dengan pelaksanaan program tertib administrasi kearsipan di lingkungan UPTD Puskesmas Silo 1, maka pada hari ini <strong>{{ $dateObj->translatedFormat('l') }}</strong> tanggal <strong>{{ $dateObj->translatedFormat('d') }}</strong> bulan <strong>{{ $dateObj->translatedFormat('F') }}</strong> tahun <strong>{{ $dateObj->translatedFormat('Y') }}</strong>, kami selaku Tim Penilai Arsip mengusulkan sejumlah berkas sebagaimana terdapat dalam <strong>Lampiran Daftar Pertelaan</strong> untuk dilakukan proses <strong>{{ $jenis_ba == 'retensi' ? 'Pemindahan ke Gudang Inaktif (Retensi)' : 'Pemusnahan Fisik Arsip' }}</strong>.</p>
            
            @if($jenis_ba == 'retensi')
                <p>Usulan pemindahan ini diajukan dikarenakan berkas rekam medis tersebut telah mencapai batas waktu penyimpanan aktif di rak utama (maksimal 2 tahun sejak tanggal kunjungan terakhir). Proses retensi ke rak inaktif ini bertujuan mutlak untuk mengoptimalkan kapasitas ruang penyimpanan utama dan mengamankan fisik berkas sebelum memasuki masa tinjauan inaktif sesuai dengan Standar Operasional Prosedur (SOP) Kearsipan.</p>
            @else
                <p>Berkas-berkas tersebut diusulkan untuk dimusnahkan secara total dikarenakan telah melewati masa simpan di gudang inaktif dan dinilai sudah tidak memiliki nilai guna administrasi dan hukum. Pemusnahan ini penting dilaksanakan guna menjaga efisiensi ruang penyimpanan sesuai dengan pedoman pemusnahan arsip Rekam Medis yang berlaku.</p>
            @endif
            
            <p>Total berkas yang diusulkan dalam agenda ini adalah sebanyak <strong>{{ $total_berkas }} ({{ terbilang($total_berkas) }}) berkas</strong>. Demikian surat usulan ini kami buat dengan sebenar-benarnya untuk dapat dipergunakan sebagai dasar pertimbangan dalam penerbitan Surat Persetujuan dari Kepala UPTD Puskesmas Silo 1.</p>
            
        @else
            {{-- KALIMAT KHUSUS BERITA ACARA FINAL --}}
            <p>Pada hari ini <strong>{{ $dateObj->translatedFormat('l') }}</strong> tanggal <strong>{{ $dateObj->translatedFormat('d') }}</strong> bulan <strong>{{ $dateObj->translatedFormat('F') }}</strong> tahun <strong>{{ $dateObj->translatedFormat('Y') }}</strong>, bertempat di UPTD Puskesmas Silo 1, telah dilaksanakan kegiatan <strong>{{ $jenis_ba == 'retensi' ? 'Retensi dan Pemindahan' : 'Pemusnahan' }}</strong> terhadap berkas rekam medis inaktif sebanyak <strong>{{ $total_berkas }} ({{ terbilang($total_berkas) }}) berkas</strong> sebagaimana tercantum rinciannya dalam Lampiran Daftar Pertelaan.</p>
            
            @if($jenis_ba == 'retensi')
                <p>Pelaksanaan retensi telah dilakukan melalui tahap pemilahan berkas dari rak penyimpanan aktif, pencatatan data ke dalam sistem informasi, dan pemindahan fisik ke rak inaktif di Gudang. Berkas-berkas tersebut selanjutnya akan disimpan serta diawasi selama masa inaktif (2 tahun) sebelum dilakukan penilaian kembali untuk tindakan penyusutan akhir.</p>
            @else
                <p>Pelaksanaan pemusnahan dokumen rekam medis tersebut dilakukan dengan cara <strong>{{ $metode }}</strong> sehingga bentuk fisik dan informasi di dalamnya telah hancur dan tidak dapat dikenali lagi. Tindakan ini dilaksanakan berdasarkan Surat Keputusan (SK) Kepala Puskesmas Nomor: <strong>{{ $sk_kapus }}</strong> serta disaksikan langsung oleh pihak-pihak terkait.</p>
            @endif

            <p>Demikian Berita Acara ini dibuat dengan sesungguhnya dan ditandatangani oleh pihak-pihak yang berwenang untuk dapat dipergunakan sebagai pedoman serta pertanggungjawaban administrasi di masa mendatang sebagaimana mestinya.</p>
        @endif
    </div>

    {{-- TANDA TANGAN (Satu Tabel Master Anti Potong) --}}
    @if($jenis_ba == 'pemusnahan')
        {{-- LAYOUT KHUSUS PEMUSNAHAN (6 ORANG) --}}
        <table class="ttd-master">
            {{-- Baris 1: 3 Saksi --}}
            <tr>
                <td style="width: 33%;">
                    <p class="ttd-p">Saksi 1,</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">{{ $nama_saksi1 ?? '' }}</p>
                    <p class="ttd-nip">NIP/NIK. {{ $nip_saksi1 ?? '-' }}</p>
                </td>
                <td style="width: 34%;">
                    <p class="ttd-p">Saksi 2,</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">{{ $nama_saksi2 ?? '' }}</p>
                    <p class="ttd-nip">NIP/NIK. {{ $nip_saksi2 ?? '-' }}</p>
                </td>
                <td style="width: 33%;">
                    <p class="ttd-p">Saksi 3,</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">{{ $nama_saksi3 ?? '' }}</p>
                    <p class="ttd-nip">NIP/NIK. {{ $nip_saksi3 ?? '-' }}</p>
                </td>
            </tr>
            
            {{-- Baris 2: Kasubag TU & Ketua Tim --}}
            <tr>
                <td style="padding-top: 25px;">
                    <p class="ttd-p">Kasubag TU,</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">{{ $nama_tu ?? '' }}</p>
                    <p class="ttd-nip">NIP. {{ $nip_tu ?? '-' }}</p>
                </td>
                <td style="padding-top: 25px;"></td>
                <td style="padding-top: 25px;">
                    <p class="ttd-p">Ketua Tim (PJ RM),</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">{{ $nama_ketua ?? '' }}</p>
                    <p class="ttd-nip">NIP. {{ $nip_ketua ?? '-' }}</p>
                </td>
            </tr>

            {{-- Baris 3: Kepala Puskesmas --}}
            <tr>
                <td colspan="3" style="padding-top: 25px;">
                    <p class="ttd-p">Mengetahui,</p>
                    <p class="ttd-p">Kepala UPTD Puskesmas Silo 1</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">{{ $nama_kapus ?? '' }}</p>
                    <p class="ttd-nip">NIP. {{ $nip_kapus ?? '-' }}</p>
                </td>
            </tr>
        </table>

    @else
        {{-- LAYOUT KHUSUS RETENSI / USULAN (3 ORANG) --}}
        <table class="ttd-master">
            <tr>
                <td style="width: 35%;">
                    @if(isset($nama_p2) && $nama_p2)
                        <p class="ttd-p">{{ $isUsulan ? 'Saksi,' : 'Pihak Kedua,' }}</p>
                        <p class="ttd-p">{{ $jabatan_p2 ?? 'Kasubag TU' }}</p>
                        <div class="ttd-space"></div>
                        <p class="ttd-nama">{{ $nama_p2 }}</p>
                        <p class="ttd-nip">NIP. {{ $nip_p2 ?? '-' }}</p>
                    @endif
                </td>
                
                <td style="width: 30%;"></td>
                
                <td style="width: 35%;">
                    <p class="ttd-p">{{ $isUsulan ? 'Diusulkan Oleh,' : 'Pihak Pertama,' }}</p>
                    <p class="ttd-p">{{ $jabatan_p1 ?? 'PJ Rekam Medis' }}</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">{{ $nama_p1 ?? '' }}</p>
                    <p class="ttd-nip">NIP. {{ $nip_p1 ?? '-' }}</p>
                </td>
            </tr>
            
            <tr>
                <td colspan="3" style="padding-top: 30px;">
                    <p class="ttd-p">{{ $isUsulan ? 'Menyetujui,' : 'Mengetahui,' }}</p>
                    <p class="ttd-p">Kepala UPTD Puskesmas Silo 1</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">{{ $nama_kapus ?? '' }}</p>
                    <p class="ttd-nip">NIP. {{ $nip_kapus ?? '-' }}</p>
                </td>
            </tr>
        </table>
    @endif


    {{-- ========================================================================= --}}
    {{-- HALAMAN 2: LAMPIRAN (MEMAKSA PINDAH HALAMAN DENGAN CSS) --}}
    {{-- ========================================================================= --}}
    <div class="page-break"></div>

    <div class="judul">
        <h3 style="text-decoration: underline;">LAMPIRAN DAFTAR PERTELAAN</h3>
        <p>Lampiran {{ $isUsulan ? 'Surat Usulan' : 'Berita Acara' }} Nomor: {{ $no_surat }}</p>
    </div>
    
    <p style="font-size: 10pt; margin-bottom: 5px; font-weight: bold;">
        Daftar {{ $jenis_ba == 'retensi' ? 'Pemindahan' : 'Pemusnahan' }} Berkas Rekam Medis:
    </p>

    {{-- TABEL DAFTAR BERKAS --}}
    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">No. RM</th>
                <th width="40%">Nama Pasien</th>
                <th width="20%">Kunjungan Terakhir</th>
                <th width="20%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data_berkas as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center; font-weight: bold; font-family: monospace;">{{ $item->no_rm }}</td>
                <td>{{ $item->nama_pasien }}</td>
                <td style="text-align: center;">{{ $item->lastVisit ? $item->lastVisit->tgl_kunjungan->format('d/m/Y') : '-' }}</td>
                <td style="text-align: center;">
                    @if($isUsulan)
                        Usul {{ ucfirst($jenis_ba) }}
                    @else
                        {{ ucfirst($jenis_ba) }} Selesai
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px; font-style: italic;">
                    -- Tidak ada data berkas --
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>