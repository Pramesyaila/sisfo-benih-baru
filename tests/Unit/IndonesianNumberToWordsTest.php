<?php

namespace Tests\Unit;

use App\Services\IndonesianNumberToWords;
use Tests\TestCase;

class IndonesianNumberToWordsTest extends TestCase
{
    public function test_convert_known_amounts(): void
    {
        $cases = [
            0 => 'Nol',
            1 => 'satu',
            10 => 'sepuluh',
            11 => 'sebelas',
            20 => 'duapuluh',
            21 => 'duapuluh satu',
            100 => 'seratus',
            101 => 'seratus satu',
            110 => 'seratus sepuluh',
            999 => 'sembilan ratus sembilan puluh sembilan',
            1000 => 'satu ribu',
            1500 => 'satu ribu lima ratus',
            10000 => 'sepuluh ribu',
            50000 => 'lima puluh ribu',
            65000 => 'enam puluh lima ribu',
            100000 => 'seratus ribu',
            150000 => 'seratus lima puluh ribu',
            1000000 => 'satu juta',
            1500000 => 'satu juta lima ratus ribu',
            12500000 => 'dua belas juta lima ratus ribu',
        ];

        foreach ($cases as $number => $expected) {
            $this->assertSame($expected, IndonesianNumberToWords::convert($number), "Gagal untuk angka {$number}");
        }
    }

    public function test_rupiah_appends_unit(): void
    {
        $this->assertSame('seratus lima puluh ribu rupiah', IndonesianNumberToWords::rupiah(150000));
        $this->assertSame('Nol rupiah', IndonesianNumberToWords::rupiah(0));
    }
}