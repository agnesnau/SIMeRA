<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Berita Acara</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; line-height: 1.5; color: #000; max-width: 210mm; margin: 0 auto; padding: 20px; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { font-size: 18pt; margin: 0; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 11pt; }
        
        .judul { text-align: center; margin-bottom: 30px; }
        .judul h2 { text-decoration: underline; margin: 0; font-size: 14pt; }
        .judul p { margin: 5px 0 0; }

        .content { text-align: justify; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 11pt; }
        table th, table td { border: 1px solid #000; padding: 8px; }
        table th { background-color: #f0f0f0; }

        .ttd { margin-top: 50px; display: flex; justify-content: flex-end; }
        .ttd-box { text-align: center; width: 250px; }
        .ttd-nama { font-weight: bold; text-decoration: underline; margin-top: 80px; }

        /* Hapus elemen browser saat print */
        @media print {
            @page { margin: 2cm; }
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Tombol Back (Hanya di layar) -->
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.history.back()" style="padding: 10px 20px; cursor: pointer;">Kembali</button>
    </div>

    <!-- KOP SURAT -->
    <div class="header">
        <h1>UPDT Puskesmas Silo 1</h1>
        <p>Jl. Jend. Ahmad Yani No.154, Krajan, Sumberjati, Kec. Silo, Kabupaten Jember, Jawa Timur 68184</p>
        <p>Telepon : (0331) 521169 | Website: https://sites.google.com/view/uptdpuskesmassilo1publik</p>
    </div>

    <!-- JUDUL -->
    <div class="judul">
        <h2>BERITA ACARA PEMUSNAHAN REKAM MEDIS</h2>
        <p>Nomor: {{ $no_surat }}</p>
    </div>

    <!-- ISI -->
    <div class="content">
        <p>
            Pada hari ini <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l') }}</strong>, 
            tanggal <strong>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}</strong>, 
            bertempat di <strong>{{ $lokasi }}</strong>, kami yang bertanda tangan di bawah ini telah melaksanakan 
            pemusnahan berkas Rekam Medis yang telah dinyatakan inaktif dan telah melewati masa retensi sesuai dengan 
            Jadwal Retensi Arsip (JRA) yang berlaku.
        </p>
        <p>Adapun rincian berkas yang dimusnahkan adalah sebagai berikut:</p>
    </div>

    <!-- TABEL DATA -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">No. RM</th>
                <th style="width: 30%">Nama Pasien</th>
                <th style="width: 20%">Tahun Terakhir</th>
                <th style="width: 30%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data_berkas as $index => $data)
            <tr>
                <td style="text-align: center">{{ $index + 1 }}</td>
                <td style="text-align: center">{{ $data->no_rm }}</td>
                <td>{{ $data->nama_pasien }}</td>
                <td style="text-align: center">
                    {{ $data->lastVisit ? $data->lastVisit->tgl_kunjungan->format('Y') : '-' }}
                </td>
                <td>
                    {{ $data->manual_status == 'dimusnahkan' ? 'Sudah Dimusnahkan' : 'Siap Musnah' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; font-style: italic;">Tidak ada data berkas yang dimusnahkan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="content">
        <p>
            Total berkas yang dimusnahkan: <strong>{{ count($data_berkas) }}</strong> berkas. <br>
            Demikian Berita Acara ini dibuat dengan sesungguhnya untuk dipergunakan sebagaimana mestinya.
        </p>
    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd">
        <div class="ttd-box">
            <p>Ketua Pelaksana,</p>
            <div class="ttd-nama">{{ $ketua }}</div>
            <p>NIP. {{ $nip_ketua }}</p>
        </div>
    </div>

</body>
</html>