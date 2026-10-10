<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminUserController extends Controller
{
    /**
     * Halaman Kelola Admin hanya boleh dibuka super admin.
     */
    public function index()
    {
        $this->authorizeSuperAdmin();

        $users = User::whereIn('role', [User::ROLE_PETUGAS_LAYANAN, User::ROLE_PETUGAS_GUDANG])
            ->latest()
            ->paginate(15);

        return view('admin.admins.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeSuperAdmin();

        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $data = $this->validateData($request);

        $this->confirmSuperAdminPassword($request);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'nik' => $data['nik'] ?? null,
            'instansi' => $data['instansi'] ?? null,
            'alamat' => $data['alamat'] ?? null,
            'kelurahan' => $data['kelurahan'] ?? null,
            'kecamatan' => $data['kecamatan'] ?? null,
            'kabupaten_kota' => $data['kabupaten_kota'] ?? null,
            'provinsi' => $data['provinsi'] ?? null,
            'domisili' => $data['domisili'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.admins.index')->with('success', 'Akun petugas berhasil ditambahkan.');
    }

    public function edit(User $admin)
    {
        $this->authorizeSuperAdmin();
        abort_unless($this->isInternal($admin), 404);

        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, User $admin)
    {
        $this->authorizeSuperAdmin();
        abort_unless($this->isInternal($admin), 404);

        $data = $this->validateData($request, $admin);

        $this->confirmSuperAdminPassword($request);

        $admin->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'nik' => $data['nik'] ?? null,
            'instansi' => $data['instansi'] ?? null,
            'alamat' => $data['alamat'] ?? null,
            'kelurahan' => $data['kelurahan'] ?? null,
            'kecamatan' => $data['kecamatan'] ?? null,
            'kabupaten_kota' => $data['kabupaten_kota'] ?? null,
            'provinsi' => $data['provinsi'] ?? null,
            'domisili' => $data['domisili'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (! empty($data['password'])) {
            $admin->update(['password' => Hash::make($data['password'])]);
        }

        return redirect()->route('admin.admins.index')->with('success', 'Akun petugas berhasil diperbarui.');
    }

    public function destroy(Request $request, User $admin)
    {
        $this->authorizeSuperAdmin();
        abort_unless($this->isInternal($admin), 404);

        $this->confirmSuperAdminPassword($request);

        if ($request->user()->id === $admin->id) {
            return back()->with('error', 'Akun yang sedang digunakan tidak dapat dihapus.');
        }

        if ($admin->isSuperAdmin()) {
            return back()->with('error', 'Akun Super Admin tidak dapat dihapus.');
        }

        $admin->delete();

        return back()->with('success', 'Akun petugas berhasil dihapus.');
    }

    /**
     * Hanya super admin yang boleh mengakses Kelola Admin.
     */
    protected function authorizeSuperAdmin(): void
    {
        abort_unless(request()->user()?->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat mengelola Kelola Admin.');
    }

    /**
     * Setiap aksi pada Kelola Admin memerlukan kata sandi super admin.
     */
    protected function confirmSuperAdminPassword(Request $request): void
    {
        $request->validate([
            'super_admin_password' => ['required', 'string'],
        ], [
            'super_admin_password.required' => 'Kata sandi Super Admin wajib diisi.',
        ]);

        $superAdmin = User::where('is_super_admin', true)->first();

        if (! $superAdmin || ! Hash::check($request->input('super_admin_password'), $superAdmin->password)) {
            throw ValidationException::withMessages([
                'super_admin_password' => 'Kata sandi Super Admin tidak sesuai.',
            ]);
        }
    }

    protected function validateData(Request $request, ?User $admin = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($admin?->id)],
            'password' => [$admin ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([User::ROLE_PETUGAS_LAYANAN, User::ROLE_PETUGAS_GUDANG])],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9]+$/'],
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9]+$/'],
            'nik' => ['nullable', 'string', 'max:20'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'kelurahan' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'kabupaten_kota' => ['nullable', 'string', 'max:255'],
            'provinsi' => ['nullable', 'string', 'max:255'],
            'domisili' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'role.required' => 'Tipe role wajib dipilih.',
            'role.in' => 'Tipe role yang dipilih tidak valid.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka.',
            'whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
        ]);
    }

    protected function isInternal(User $user): bool
    {
        return in_array($user->role, [User::ROLE_PETUGAS_LAYANAN, User::ROLE_PETUGAS_GUDANG], true);
    }
}