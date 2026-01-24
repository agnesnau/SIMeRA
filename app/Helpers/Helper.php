<?php

namespace App\Helpers;

class Helper
{
    /**
     * Fungsi Terbilang Bahasa Indonesia
     * Mengubah angka menjadi format kata-kata (Contoh: 125 -> Seratus Dua Puluh Lima)
     */
    public static function terbilang($angka)
    {
        $angka = abs($angka);
        $baca  = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        $terbilang = "";

        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = self::terbilang($angka - 10) . " Belas";
        } elseif ($angka < 100) {
            $terbilang = self::terbilang($angka / 10) . " Puluh" . self::terbilang($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = " Seratus" . self::terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = self::terbilang($angka / 100) . " Ratus" . self::terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = " Seribu" . self::terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = self::terbilang($angka / 1000) . " Ribu" . self::terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = self::terbilang($angka / 1000000) . " Juta" . self::terbilang($angka % 1000000);
        }

        return trim($terbilang);
    }
}