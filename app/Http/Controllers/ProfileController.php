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
        $user = User::find(Auth::user()->id);

        return view('profile.index', compact('user'));
    }

    public function edit()
    {
        $user = User::find(Auth::user()->id);

        return view('profile.edit', compact('user'));
    }

    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = User::find(Auth::user()->id);

        // Hapus file lama kalau ada
        if ($user->foto != null) {
            if (Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
        }

        $nama_file = $request->file('foto')->store('foto-users', 'public');
        $user->foto = $nama_file;
        $user->save();

        return redirect()->route('profile')->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function updateProfile(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->bio = $request->bio;

        $user->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
