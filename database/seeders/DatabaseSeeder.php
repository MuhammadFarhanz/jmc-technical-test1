<?php

namespace Database\Seeders;

use App\Models\Kabupaten;
use App\Models\Penduduk;
use App\Models\Provinsi;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        // Seed 5 sample provinces (without population count)
        $provinsis = [
            ['nama' => 'Jawa Barat'],
            ['nama' => 'Jawa Tengah'],
            ['nama' => 'Jawa Timur'],
            ['nama' => 'Banten'],
            ['nama' => 'DI Yogyakarta']
        ];

        foreach ($provinsis as $provinsi) {
            $createdProvinsi = Provinsi::create($provinsi);

            // Create 2 kabupaten per provinsi WITH population counts
            $kabupatens = $this->getKabupatensForProvinsi($createdProvinsi['nama']);

            foreach ($kabupatens as $kabupaten) {
                Kabupaten::create([
                    'nama' => $kabupaten['nama'],
                    'provinsi_id' => $createdProvinsi->id,
                    'jumlah_penduduk' => $kabupaten['jumlah_penduduk'] // Population only here
                ]);
            }
        }

        // Seed 5 sample residents
        $penduduks = [
            ['nama' => 'Budi Santoso', 'nik' => '3273010101010001', 'umur' => 25, 'alamat' => 'Jl. Merdeka No. 1'],
            ['nama' => 'Ani Wijaya', 'nik' => '3273010101010002', 'umur' => 30, 'alamat' => 'Jl. Sudirman No. 45'],
            ['nama' => 'Citra Dewi', 'nik' => '3273010101010003', 'umur' => 22, 'alamat' => 'Jl. Gatot Subroto No. 12'],
            ['nama' => 'Dodi Pratama', 'nik' => '3273010101010004', 'umur' => 35, 'alamat' => 'Jl. Thamrin No. 8'],
            ['nama' => 'Eka Suryani', 'nik' => '3273010101010005', 'umur' => 28, 'alamat' => 'Jl. Asia Afrika No. 10'],
        ];

        $kabupatenIds = Kabupaten::pluck('id')->toArray();

        foreach ($penduduks as $penduduk) {
            $randomKabupatenId = $kabupatenIds[array_rand($kabupatenIds)];
            $kabupaten = Kabupaten::find($randomKabupatenId);

            Penduduk::create([
                ...$penduduk,
                'provinsi_id' => $kabupaten->provinsi_id,
                'kabupaten_id' => $randomKabupatenId
            ]);
        }
    }

    private function getKabupatensForProvinsi($provinsiName): array
    {
        return match ($provinsiName) {
            'Jawa Barat' => [
                ['nama' => 'Bandung', 'jumlah_penduduk' => 2500000],
                ['nama' => 'Bogor', 'jumlah_penduduk' => 1100000]
            ],
            'Jawa Tengah' => [
                ['nama' => 'Semarang', 'jumlah_penduduk' => 1800000],
                ['nama' => 'Solo', 'jumlah_penduduk' => 600000]
            ],
            'Jawa Timur' => [
                ['nama' => 'Surabaya', 'jumlah_penduduk' => 3000000],
                ['nama' => 'Malang', 'jumlah_penduduk' => 900000]
            ],
            'Banten' => [
                ['nama' => 'Tangerang', 'jumlah_penduduk' => 2000000],
                ['nama' => 'Serang', 'jumlah_penduduk' => 700000]
            ],
            'DI Yogyakarta' => [
                ['nama' => 'Yogyakarta', 'jumlah_penduduk' => 400000],
                ['nama' => 'Bantul', 'jumlah_penduduk' => 200000]
            ],
            default => []
        };
    }
}