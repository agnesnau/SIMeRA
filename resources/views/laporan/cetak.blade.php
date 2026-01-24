<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara - SIMeRA</title>
    <style>
        @page { size: A4; margin: 1cm 1.5cm; }
        body { 
            font-family: 'Times New Roman', serif; 
            line-height: 1.4; 
            color: #000; 
            font-size: 11pt; 
            margin: 0;
        }
        
        /* Kop Surat Utama */
        .kop-surat { 
            width: 100%; 
            border-bottom: 4px double #000; 
            padding-bottom: 5px; 
            margin-bottom: 15px; 
        }
        .kop-surat table { width: 100%; border-collapse: collapse; }
        .kop-logo { width: 80px; }
        .kop-teks { text-align: center; }
        .kop-teks h1 { font-size: 14pt; margin: 0; text-transform: uppercase; font-weight: bold; }
        .kop-teks h2 { font-size: 16pt; margin: 0; text-transform: uppercase; font-weight: bold; }
        .kop-teks p { margin: 0; font-size: 9pt; }
        
        .judul { text-align: center; margin-bottom: 15px; }
        .judul h3 { text-decoration: underline; margin: 0; font-size: 12pt; text-transform: uppercase; font-weight: bold; }
        .judul p { margin: 2px 0 0; font-weight: bold; font-size: 10pt; }

        .content { text-align: justify; margin-bottom: 10px; }
        .table-identitas { width: 100%; margin-left: 20px; margin-bottom: 15px; border-collapse: collapse; }
        .table-identitas td { vertical-align: top; padding: 3px; border: none; }

        .ttd-container { width: 100%; margin-top: 20px; page-break-inside: avoid; }
        .ttd-box { width: 45%; display: inline-block; text-align: center; vertical-align: top; }
        .ttd-space { height: 60px; }
        .ttd-nama { font-weight: bold; text-decoration: underline; }

        table.data { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10pt; }
        table.data th, table.data td { border: 1px solid #000; padding: 6px; }
        table.data th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
    </style>
</head>
<body>

    @php 
        \Carbon\Carbon::setLocale('id');
        $dateObj = \Carbon\Carbon::parse($tanggal);
    @endphp

    <div class="kop-surat">
        <table>
            <tr>
                <td class="kop-logo">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('img/logo-dinkes.png'))) }}" style="width: 70px;">
                </td>
                <td class="kop-teks">
                    <h1>PEMERINTAH KABUPATEN JEMBER</h1>
                    <h1>DINAS KESEHATAN</h1>
                    <h2>UPTD PUSKESMAS SILO 1</h2>
                    <p>Jl. Jend. Ahmad Yani No.154, Krajan, Sumberjati, Kec. Silo, Kabupaten Jember, Jawa Timur 68184</p>
                    <p>Telepon: (0331) 521169 | Email : pkmsilo1@gmail.com</p>
                </td>
                <td class="kop-logo" style="text-align: right;">
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('img/logo-puskesmas.jpg'))) }}" style="width: 80px;">
                </td>
            </tr>
        </table>
    </div>

    @if($jenis_ba == 'retensi')
        <div class="judul">
            <h3>BERITA ACARA RETENSI REKAM MEDIS</h3>
            <p>Nomor: {{ $no_surat }}</p>
        </div>

        <div class="content">
            Pada hari ini, <strong>{{ $dateObj->translatedFormat('l') }}</strong>, 
            tanggal <strong>{{ $dateObj->translatedFormat('d') }}</strong> 
            bulan <strong>{{ $dateObj->translatedFormat('F') }}</strong> 
            tahun <strong>{{ $dateObj->translatedFormat('Y') }}</strong>, 
            telah dilaksanakan pemindahan berkas rekam medis inaktif oleh:
        </div>

        <table class="table-identitas">
            <tr>
                <td width="30%">Kasubag Tata Usaha</td>
                <td width="2%">:</td>
                <td><strong>{{ $nama_p1 }}</strong> (NIP. {{ $nip_p1 ?? '-' }})</td>
            </tr>
            <tr>
                <td>PJ. Rekam Medis</td>
                <td>:</td>
                <td><strong>{{ $nama_p2 }}</strong> (NIP. {{ $nip_p2 ?? '-' }})</td>
            </tr>
        </table>
    @else
        <div class="judul">
            <h3>BERITA ACARA PEMUSNAHAN REKAM MEDIS</h3>
            <p>Nomor: {{ $no_surat }}</p>
        </div>
        <div class="content">
    Berdasarkan Surat Keputusan Kepala Puskesmas Silo 1 Nomor <strong>{{ $sk_kapus ?? '-' }}</strong> mengenai penetapan Tim Pemusnahan...
        </div>
    @endif

    <div class="ttd-container">
        <div class="ttd-box">
            <p>Kasubag Tata Usaha,</p>
            <div class="ttd-space"></div>
            <p class="ttd-nama">{{ $nama_p1 }}</p>
            <p>NIP. {{ $nip_p1 ?? '..........................' }}</p>
        </div>
        <div class="ttd-box" style="float: right;">
            <p>PJ. Rekam Medis,</p>
            <div class="ttd-space"></div>
            <p class="ttd-nama">{{ $nama_p2 }}</p>
            <p>NIP. {{ $nip_p2 ?? '..........................' }}</p>
        </div>
        <div style="clear: both; text-align: center; margin-top: 25px;">
            <p>Mengetahui,</p>
            <p>Kepala Puskesmas Silo 1</p>
            <div class="ttd-space" style="height: 50px;"></div>
            <p class="ttd-nama">{{ $nama_kapus }}</p>
            <p>NIP. {{ $nip_kapus ?? '..........................' }}</p>
        </div>
    </div>

    <div style="page-break-before: always;"></div>
    <div class="judul">
        <h3>LAMPIRAN DAFTAR PERTELAAN REKAM MEDIS</h3>
        <p>Status: {{ ucfirst($jenis_ba) }}</p>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">No. RM</th>
                <th width="35%">Nama Pasien</th>
                <th width="20%">Kunjungan Terakhir</th>
                <th width="10%">Umur Arsip</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data_berkas as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center;">{{ $item->no_rm }}</td>
                <td>{{ $item->nama_pasien }}</td>
                <td style="text-align: center;">{{ $item->lastVisit->tgl_kunjungan->format('d/m/Y') }}</td>
                <td style="text-align: center;">{{ $item->lastVisit->tgl_kunjungan->diffInYears(now()) }} Thn</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>