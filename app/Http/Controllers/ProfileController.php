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

        $layout = $user->isKonsumen()
            ? 'layouts.app'
            : 'layouts.admin';

        return view('profile.show', compact('user', 'layout'));
    }

    public function edit()
    {
        $user = auth()->user();

        $layout = $user->isKonsumen()
            ? 'layouts.app'
            : 'layouts.admin';

        return view('profile.edit', compact('user', 'layout'));
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
            'phone' => ['nullable', 'string', 'max:20'],
            'nik' => ['nullable', 'string', 'max:20'],
            'domisili' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

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
}
