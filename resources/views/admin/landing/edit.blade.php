@extends('layouts.admin')

@section('title', 'Kelola Landing Page Konsumen')

@section('content')
<div class="max-w-4xl">
    <p class="text-sm text-gray-500 mb-4">
        Ubah logo, teks, dan gambar yang tampil pada halaman katalog konsumen.
        Perubahan langsung berlaku tanpa menyentuh source code.
    </p>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold mb-4">Konten Landing Page</h2>

            <form action="{{ route('admin.landing.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5" id="landing-form">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium mb-1">Logo</label>
                    <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,.svg" class="w-full text-sm">
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP, atau SVG. Maksimal 2 MB.</p>

                    <div class="mt-3 flex items-start gap-4">
                        <div class="w-20 h-20 rounded-lg border border-gray-200 bg-base flex items-center justify-center overflow-hidden shrink-0">
                            @if ($content->logo)
                                <img src="{{ asset('storage/' . $content->logo) }}" alt="Logo saat ini" class="max-w-full max-h-full object-contain" id="logo-preview">
                            @else
                                <span class="text-xs text-gray-400" id="logo-preview">Logo bawaan</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 space-y-1">
                            @if ($content->logo)
                                <p>Logo saat ini sudah aktif di header katalog.</p>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300">
                                    Kembalikan ke logo bawaan
                                </label>
                            @else
                                <p>Belum ada logo. Saat ini header memakai ikon bawaan.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="border-t pt-5">
                    <label class="block text-sm font-medium mb-1">Judul Hero</label>
                    <textarea name="hero_title" rows="2" required class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">{{ old('hero_title', $content->hero_title) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Tampil sebagai headline besar di bagian atas katalog.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Subjudul / Deskripsi</label>
                    <textarea name="hero_subtitle" rows="3" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm">{{ old('hero_subtitle', $content->hero_subtitle) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Pengumuman</label>
                    <textarea name="announcement" rows="2" class="w-full rounded-md border-gray-300 px-3 py-2 border text-sm" placeholder="Informasi singkat yang perlu diketahui konsumen.">{{ old('announcement', $content->announcement) }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Opsional. Tampil sebagai catatan di hero.</p>
                </div>

                <div class="pt-2">
                    <button class="bg-primary text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-primary-dark">Simpan Konten</button>
                    <a href="{{ route('catalog.index') }}" target="_blank" class="ml-3 text-sm text-primary-dark hover:underline">Lihat katalog →</a>
                </div>
            </form>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-semibold mb-3">Informasi</h2>
                <ul class="text-sm text-gray-600 space-y-2 list-disc list-inside">
                    <li>Logo dan konten ini hanya memengaruhi tampilan halaman katalog konsumen.</li>
                    <li>Gambar hero tidak dapat diubah dari halaman ini.</li>
                    <li>Foto produk dan kategori dikelola Petugas Gudang melalui menu Produk dan Kategori.</li>
                    <li>Informasi ini tersimpan di database sehingga dapat diubah kapan saja.</li>
                </ul>
                <p class="mt-4 text-xs text-gray-400">
                    Terakhir diperbarui: {{ $content->updated_at ? $content->updated_at->format('d M Y H:i') : 'belum pernah' }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-semibold mb-3">Pratinjau Header</h2>
                <div class="border border-gray-200 rounded-lg p-4 bg-base">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-full bg-accent-light flex items-center justify-center shrink-0">
                            @if ($content->logo)
                                <img src="{{ asset('storage/' . $content->logo) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                            @else
                                🌾
                            @endif
                        </span>
                        <div>
                            <p class="text-[10px] uppercase tracking-wide text-gray-500">KATALOG RESMI</p>
                            <p class="font-bold text-primary-dark">Benih &amp; Bibit</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Pratinjau logo langsung sebelum halaman disimpan.
    (function () {
        var input = document.querySelector('#landing-form input[name="logo"]');
        var preview = document.getElementById('logo-preview');

        if (!input || !preview) return;

        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function (event) {
                preview.outerHTML = '<img src="' + event.target.result + '" alt="Pratinjau logo" class="max-w-full max-h-full object-contain" id="logo-preview">';
                preview = document.getElementById('logo-preview');
            };
            reader.readAsDataURL(file);
        });
    })();
</script>
@endsection
