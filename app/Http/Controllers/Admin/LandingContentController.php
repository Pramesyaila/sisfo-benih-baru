<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LandingContentController extends Controller
{
    public function edit()
    {
        $content = LandingContent::current();

        return view('admin.landing.edit', compact('content'));
    }

    public function update(Request $request)
    {
        $content = LandingContent::current();

        // Field ubah gambar hero dihapus: gambar hero tidak lagi dikelola dari
        // halaman ini. Logo tetap dapat diubah.
        $data = $request->validate([
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:1000'],
            'announcement' => ['nullable', 'string', 'max:500'],
        ], [
            'logo.image' => 'Logo harus berupa berkas gambar.',
            'hero_title.required' => 'Judul hero wajib diisi.',
            'hero_title.max' => 'Judul hero maksimal 255 karakter.',
            'hero_subtitle.max' => 'Subjudul maksimal 1000 karakter.',
            'announcement.max' => 'Pengumuman maksimal 500 karakter.',
        ]);

        // Logo: opsional, dapat dikosongkan untuk kembali ke logo bawaan.
        if ($request->hasFile('logo')) {
            if ($content->logo) {
                Storage::disk('public')->delete($content->logo);
            }

            $data['logo'] = $request->file('logo')->store('landing', 'public');
        } elseif ($request->boolean('remove_logo')) {
            if ($content->logo) {
                Storage::disk('public')->delete($content->logo);
            }

            $data['logo'] = null;
        } else {
            unset($data['logo']);
        }

        $data['updated_by'] = Auth::id();

        $content->update($data);

        return back()->with('success', 'Konten landing page berhasil diperbarui.');
    }
}