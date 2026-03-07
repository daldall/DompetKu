<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = User::find(Auth::id());

        return view('profile.index', compact('user'));
    }

    public function edit()
    {
        $user = User::find(Auth::id());

        return view('profile.edit', compact('user'));
    }

    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = User::find(Auth::id());

        if ($user->foto && Storage::disk('public')->exists($user->foto)) {
            Storage::disk('public')->delete($user->foto);
        }

        $user->foto = $request->file('foto')->store('foto-users', 'public');
        $user->save();

        return redirect()->route('profile')->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::id());

        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio'   => 'nullable|string|max:500',
        ]);

        $user->update([
            'nama'  => $request->nama,
            'email' => $request->email,
            'bio'   => $request->bio,
        ]);

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
