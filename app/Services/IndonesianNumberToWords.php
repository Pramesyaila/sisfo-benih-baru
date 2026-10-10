<?php

namespace App\Services;

class IndonesianNumberToWords
{
    private const UNITS = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
    private const TENS = ['', '', 'duapuluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'];
    private const SCALES = ['', 'ribu', 'juta', 'miliar', 'triliun'];

    /**
     * Ubah angka menjadi ejaan bahasa Indonesia, contoh:
     * 150000 => "Seratus lima puluh ribu".
     */
    public static function convert(int|float $number): string
    {
        $number = (int) round((float) $number);

        if ($number === 0) {
            return 'Nol';
        }

        if ($number < 0) {
            return 'Minus ' . self::convert(abs($number));
        }

        $words = [];
        $scaleIndex = 0;

        while ($number > 0) {
            $chunk = $number % 1000;
            $number = intdiv($number, 1000);

            if ($chunk > 0) {
                $words[] = self::chunkToWords($chunk) . ' ' . self::SCALES[$scaleIndex];
            }

            $scaleIndex++;
        }

        return trim(implode(' ', array_reverse($words)));
    }

    /**
     * Terbilang untuk nilai rupiah.
     */
    public static function rupiah(int|float $number): string
    {
        return self::convert($number) . ' rupiah';
    }

    private static function chunkToWords(int $number): string
    {
        $parts = [];

        if ($number >= 100) {
            $hundreds = intdiv($number, 100);
            $remainder = $number % 100;

            $parts[] = ($hundreds === 1 ? 'seratus' : self::UNITS[$hundreds] . ' ratus');

            if ($remainder > 0) {
                $parts[] = self::belowHundred($remainder);
            }
        } elseif ($number > 0) {
            $parts[] = self::belowHundred($number);
        }

        return trim(implode(' ', array_filter($parts)));
    }

    private static function belowHundred(int $number): string
    {
        if ($number < 12) {
            // 1-11 dibaca apa adanya: satu, dua, ... sebelas.
            return self::UNITS[$number];
        }

        if ($number < 20) {
            // 12-19 memakai kata "belas": dua belas, tiga belas, ...
            return self::UNITS[$number - 10] . ' belas';
        }

        $tens = intdiv($number, 10);
        $units = $number % 10;

        return self::TENS[$tens] . ($units > 0 ? ' ' . self::UNITS[$units] : '');
    }
}