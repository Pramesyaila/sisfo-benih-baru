<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    // Registrasi hanya untuk konsumen. Akun petugas dibuat oleh Petugas Layanan
    // melalui halaman Kelola Admin.
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nik' => ['nullable', 'string', 'max:20'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'kelurahan' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'kabupaten_kota' => ['nullable', 'string', 'max:255'],
            'provinsi' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9]+$/'],
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9]+$/'],
        ], [
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
            'whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'nik' => $data['nik'] ?? null,
            'instansi' => $data['instansi'] ?? null,
            'alamat' => $data['alamat'] ?? null,
            'kelurahan' => $data['kelurahan'] ?? null,
            'kecamatan' => $data['kecamatan'] ?? null,
            'kabupaten_kota' => $data['kabupaten_kota'] ?? null,
            'provinsi' => $data['provinsi'] ?? null,
            // Domisili diisi dari kabupaten/kota agar konsisten dengan dokumen resmi.
            'domisili' => $data['kabupaten_kota'] ?? null,
            'role' => User::ROLE_KONSUMEN,
        ]);

        Auth::login($user);

        return redirect()
            ->route('catalog.index')
            ->with('success', 'Registrasi berhasil, selamat datang!');
    }
}