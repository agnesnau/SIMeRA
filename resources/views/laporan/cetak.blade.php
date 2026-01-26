<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Berita Acara - SIMeRA</title>
    <style>
        @page { size: A4; margin: 2cm 2cm; }
        body { 
            font-family: 'Times New Roman', serif; 
            line-height: 1.3; 
            color: #000; 
            font-size: 11pt; 
        }
        
        /* Kop Surat */
        .kop-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-logo { width: 80px; text-align: center; }
        .kop-text { text-align: center; }
        .kop-text h1 { font-size: 14pt; margin: 0; font-weight: bold; text-transform: uppercase; }
        .kop-text h2 { font-size: 16pt; margin: 0; font-weight: bold; text-transform: uppercase; }
        .kop-text p { margin: 0; font-size: 9pt; font-style: italic; }

        /* Judul Surat */
        .judul { text-align: center; margin-bottom: 20px; }
        .judul h3 { text-decoration: underline; margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: bold; }
        .judul p { margin: 2px 0 0; font-size: 11pt; }

        /* Konten */
        .content { text-align: justify; margin-bottom: 15px; }
        .table-info { width: 100%; border-collapse: collapse; margin-left: 20px; margin-bottom: 10px; }
        .table-info td { vertical-align: top; padding: 2px; }

        /* Tanda Tangan */
        .ttd-container { width: 100%; margin-top: 30px; display: table; }
        .ttd-box { display: table-cell; width: 50%; text-align: center; vertical-align: top; }
        .ttd-space { height: 70px; }
        .ttd-nama { font-weight: bold; text-decoration: underline; margin-bottom: 0; }
        .ttd-nip { margin-top: 0; font-size: 10pt; }

        /* Tabel Lampiran */
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10pt; }
        table.data th, table.data td { border: 1px solid #000; padding: 5px; }
        table.data th { background-color: #e0e0e0; text-align: center; font-weight: bold; }
        
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    {{-- FUNGSI TERBILANG (Ditanam langsung biar gak error) --}}
    @php
        function penyebut($nilai) {
            $nilai = abs($nilai);
            $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
            $temp = "";
            if ($nilai < 12) {
                $temp = " ". $huruf[$nilai];
            } else if ($nilai <20) {
                $temp = penyebut($nilai - 10). " belas";
            } else if ($nilai < 100) {
                $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
            } else if ($nilai < 200) {
                $temp = " seratus" . penyebut($nilai - 100);
            } else if ($nilai < 1000) {
                $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
            } else if ($nilai < 2000) {
                $temp = " seribu" . penyebut($nilai - 1000);
            } else if ($nilai < 1000000) {
                $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
            } 
            return $temp;
        }

        function terbilang($nilai) {
            if($nilai<0) {
                $hasil = "minus ". trim(penyebut($nilai));
            } else {
                $hasil = trim(penyebut($nilai));
            }
            return ucwords($hasil);
        }

        \Carbon\Carbon::setLocale('id');
        $dateObj = \Carbon\Carbon::parse($tanggal);
    @endphp

    {{-- KOP SURAT --}}
    <table class="kop-table">
        <tr>
            {{-- LOGO KIRI (Pastikan file ada di public/img/logo-dinkes.png) --}}
            <td class="kop-logo">
                @if(file_exists(public_path('img/logo-dinkes.png')))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/logo-dinkes.png'))) }}" style="width: 70px;">
                @endif
            </td>
            <td class="kop-text">
                <h1>PEMERINTAH KABUPATEN JEMBER</h1>
                <h1>DINAS KESEHATAN</h1>
                <h2>UPTD PUSKESMAS SILO 1</h2>
                <p>Jl. Jend. Ahmad Yani No.154, Krajan, Sumberjati, Kec. Silo, Kabupaten Jember<br>
                Telepon: (0331) 521169 | Email: pkmsilo1@gmail.com</p>
            </td>
            {{-- LOGO KANAN (Pastikan file ada di public/img/logo-puskesmas.jpg) --}}
            <td class="kop-logo">
                @if(file_exists(public_path('img/logo-puskesmas.jpg')))
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('img/logo-puskesmas.jpg'))) }}" style="width: 70px;">
                @endif
            </td>
        </tr>
    </table>

    @if($jenis_ba == 'retensi')
        {{-- KONTEN BA RETENSI --}}
        <div class="judul">
            <h3>BERITA ACARA RETENSI REKAM MEDIS</h3>
            <p>Nomor: {{ $no_surat }}</p>
        </div>

        <div class="content">
            <p>Pada hari ini <strong>{{ $dateObj->translatedFormat('l') }}</strong> tanggal <strong>{{ $dateObj->translatedFormat('d') }}</strong> bulan <strong>{{ $dateObj->translatedFormat('F') }}</strong> tahun <strong>{{ $dateObj->translatedFormat('Y') }}</strong>, bertempat di Ruang Penyimpanan Rekam Medis UPTD Puskesmas Silo 1, kami yang bertanda tangan di bawah ini:</p>
        </div>

        <table class="table-info">
            <tr>
                <td width="30%">1. Nama</td>
                <td width="2%">:</td>
                <td><strong>{{ $nama_p1 }}</strong></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;NIP</td>
                <td>:</td>
                <td>{{ $nip_p1 ?? '-' }}</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;Jabatan</td>
                <td>:</td>
                <td>{{ $jabatan_p1 }}</td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 5px 0;">Selanjutnya disebut <strong>PIHAK PERTAMA</strong>.</td>
            </tr>
            <tr>
                <td>2. Nama</td>
                <td>:</td>
                <td><strong>{{ $nama_p2 }}</strong></td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;NIP</td>
                <td>:</td>
                <td>{{ $nip_p2 ?? '-' }}</td>
            </tr>
            <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;Jabatan</td>
                <td>:</td>
                <td>{{ $jabatan_p2 }}</td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 5px 0;">Selanjutnya disebut <strong>PIHAK KEDUA</strong>.</td>
            </tr>
        </table>

        <div class="content">
            <p>PIHAK PERTAMA menyerahkan arsip rekam medis inaktif tahun <strong>{{ $rentang_tahun }}</strong> kepada PIHAK KEDUA untuk disimpan di Gudang Arsip Inaktif (Retensi). PIHAK KEDUA menerima penyerahan tersebut dengan rincian jumlah berkas:</p>
            <p style="text-align: center; font-weight: bold; margin: 10px 0;">
                SEBANYAK: {{ $total_berkas }} ({{ terbilang($total_berkas) }}) BERKAS
            </p>
            <p>Demikian berita acara ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
        </div>

    @else
        {{-- KONTEN BA PEMUSNAHAN --}}
        <div class="judul">
            <h3>BERITA ACARA PEMUSNAHAN REKAM MEDIS</h3>
            <p>Nomor: {{ $no_surat }}</p>
        </div>

        <div class="content">
            <p>Berdasarkan Surat Keputusan Kepala Puskesmas Silo 1 Nomor: <strong>{{ $sk_kapus }}</strong>, pada hari ini <strong>{{ $dateObj->translatedFormat('l') }}</strong> tanggal <strong>{{ $dateObj->translatedFormat('d') }}</strong> bulan <strong>{{ $dateObj->translatedFormat('F') }}</strong> tahun <strong>{{ $dateObj->translatedFormat('Y') }}</strong>, telah dilaksanakan pemusnahan arsip rekam medis yang sudah tidak memiliki nilai guna (inaktif).</p>
            
            <p>Pelaksanaan pemusnahan dilakukan secara total dengan cara <strong>{{ $metode }}</strong> sehingga fisik dan informasi di dalamnya tidak dapat dikenali lagi.</p>
            
            <p>Jumlah berkas yang dimusnahkan adalah sebanyak <strong>{{ $total_berkas }} ({{ terbilang($total_berkas) }})</strong> berkas, sebagaimana tercantum dalam Daftar Pertelaan Arsip (Lampiran).</p>
        </div>
    @endif

    {{-- KOLOM TANDA TANGAN --}}
    <div class="ttd-container">
        <div class="ttd-box">
            <p>{{ $jenis_ba == 'retensi' ? 'Pihak Pertama,' : 'Ketua Tim Pemusnah,' }}</p>
            <div class="ttd-space"></div>
            <p class="ttd-nama">{{ $nama_p1 }}</p>
            <p class="ttd-nip">NIP. {{ $nip_p1 ?? '-' }}</p>
        </div>
        <div class="ttd-box">
            <p>{{ $jenis_ba == 'retensi' ? 'Pihak Kedua,' : 'Saksi / Pengawas,' }}</p>
            <div class="ttd-space"></div>
            <p class="ttd-nama">{{ $nama_p2 }}</p>
            <p class="ttd-nip">NIP. {{ $nip_p2 ?? '-' }}</p>
        </div>
    </div>

    {{-- TTD MENGETAHUI (KEPALA PUSKESMAS) --}}
    <div style="width: 100%; text-align: center; margin-top: 20px;">
        <p>Mengetahui,<br>Kepala UPTD Puskesmas Silo 1</p>
        <div class="ttd-space"></div>
        <p class="ttd-nama">{{ $nama_kapus }}</p>
        <p class="ttd-nip">NIP. {{ $nip_kapus ?? '-' }}</p>
    </div>

    {{-- HALAMAN 2: LAMPIRAN --}}
    <div class="page-break"></div>

    <div class="judul">
        <h3>LAMPIRAN DAFTAR PERTELAAN REKAM MEDIS</h3>
        <p>Status Dokumen: {{ $jenis_ba == 'retensi' ? 'INAKTIF (RETENSI)' : 'DIMUSNAHKAN' }}</p>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">No. RM</th>
                <th width="35%">Nama Pasien</th>
                <th width="25%">Kunjungan Terakhir</th>
                <th width="15%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data_berkas as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center; font-family: monospace; font-weight: bold;">{{ $item->no_rm }}</td>
                <td>{{ $item->nama_pasien }}</td>
                <td style="text-align: center;">
                    {{ $item->lastVisit ? $item->lastVisit->tgl_kunjungan->format('d/m/Y') : '-' }}
                </td>
                <td style="text-align: center;">
                    {{ $jenis_ba == 'retensi' ? 'Retensi' : 'Musnah' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">
                    <i>-- Data berkas tidak ditemukan dalam sistem --</i>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>