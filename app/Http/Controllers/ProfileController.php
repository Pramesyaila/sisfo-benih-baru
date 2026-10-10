<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $layout = $user->isKonsumen() ? 'layouts.app' : 'layouts.admin';

        $view = $user->isKonsumen() ? 'customer.profile.show' : 'admin.profile.show';

        return view($view, compact('user', 'layout'));
    }

    public function edit()
    {
        $user = auth()->user();

        $layout = $user->isKonsumen() ? 'layouts.app' : 'layouts.admin';

        $view = $user->isKonsumen() ? 'customer.profile.edit' : 'admin.profile.edit';

        return view($view, compact('user', 'layout'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9]+$/'],
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9]+$/'],
            'nik' => ['nullable', 'string', 'max:20'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'kelurahan' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'kabupaten_kota' => ['nullable', 'string', 'max:255'],
            'provinsi' => ['nullable', 'string', 'max:255'],
            'domisili' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
            'whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
        ]);

        unset($validated['profile_photo']);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $validated['profile_photo'] = $request
                ->file('profile_photo')
                ->store('profile-photos', 'public');
        }

        $user->update($validated);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profile berhasil diperbarui.');
    }

    /**
     * Rincian field alamat yang dipakai bersama oleh form registrasi,
     * form tambah petugas, dan form profil.
     *
     * @return array<int, array<string, string>>
     * 
     */
    public static function addressFields(): array
    {
        return [
            ['name' => 'kelurahan', 'label' => 'Kelurahan/Desa', 'placeholder' => 'Contoh: Desa Sukamaju'],
            ['name' => 'kecamatan', 'label' => 'Kecamatan', 'placeholder' => 'Contoh: Bogor Timur'],
            ['name' => 'kabupaten_kota', 'label' => 'Kabupaten/Kota', 'placeholder' => 'Contoh: Kabupaten Bogor'],
            ['name' => 'provinsi', 'label' => 'Provinsi', 'placeholder' => 'Contoh: Jawa Barat'],
        ];
    }
}