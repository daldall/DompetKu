<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
