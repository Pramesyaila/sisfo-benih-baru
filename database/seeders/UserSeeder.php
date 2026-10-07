<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Kredensial akun bawaan sistem.
     *
     * @var array<string, string>
     */
    public const SUPER_ADMIN = [
        'name' => 'Super Admin',
        'email' => 'superadmin@benih.test',
        'password' => 'superadmin123',
    ];

    public function run(): void
    {
        // Akun Super Admin bawaan sistem: satu-satunya akun yang dapat
        // mengelola fitur Kelola Admin.
        User::updateOrCreate(
            ['email' => self::SUPER_ADMIN['email']],
            [
                'name' => self::SUPER_ADMIN['name'],
                'password' => Hash::make(self::SUPER_ADMIN['password']),
                'role' => User::ROLE_PETUGAS_LAYANAN,
                'is_super_admin' => true,
                'is_active' => true,
            ]
        );

        // Pastikan hanya ada satu akun Super Admin.
        User::where('is_super_admin', true)
            ->where('email', '!=', self::SUPER_ADMIN['email'])
            ->update(['is_super_admin' => false]);

        User::updateOrCreate(
            ['email' => 'layanan@benih.test'],
            [
                'name' => 'Petugas Layanan',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PETUGAS_LAYANAN,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'gudang@benih.test'],
            [
                'name' => 'Petugas Gudang',
                'password' => Hash::make('password'),
                'role' => User::ROLE_PETUGAS_GUDANG,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'konsumen@benih.test'],
            [
                'name' => 'Pramesyaila (Konsumen Contoh)',
                'password' => Hash::make('password'),
                'role' => User::ROLE_KONSUMEN,
                'phone' => '081234567890',
                'whatsapp' => '081234567890',
                'nik' => '3201234567890001',
                'instansi' => 'Kelompok Tani Sejahtera',
                'alamat' => 'Jl. Contoh No. 1',
                'kelurahan' => 'Sukamaju',
                'kecamatan' => 'Bogor Timur',
                'kabupaten_kota' => 'Kabupaten Bogor',
                'provinsi' => 'Jawa Barat',
                'domisili' => 'Kabupaten Bogor',
                'is_active' => true,
            ]
        );
    }
}
