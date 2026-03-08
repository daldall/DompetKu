<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TargetController extends Controller
{
    public function index()
    {
        $targets = Target::where('user_id', Auth::id())->latest()->get();

        return view('target.index', compact('targets'));
    }

    public function create()
    {
        return view('target.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_target'    => 'required|string|max:255',
            'target_nominal' => 'required|integer|min:1',
            'terkumpul'      => 'nullable|integer|min:0',
            'tanggal_target' => 'nullable|date',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto-targets', 'public');
        }

        $data['terkumpul'] = $data['terkumpul'] ?? 0;

        $user = User::find(Auth::id());
        $user->targets()->create($data);

        return redirect()->route('target.index')->with('success', 'Target tabungan berhasil dibuat.');
    }

    public function edit(Target $target)
    {
        if ($target->user_id !== Auth::id()) abort(403);

        return view('target.edit', compact('target'));
    }

    public function update(Request $request, Target $target)
    {
        if ($target->user_id !== Auth::id()) abort(403);

        $data = $request->validate([
            'nama_target'    => 'required|string|max:255',
            'target_nominal' => 'required|integer|min:1',
            'terkumpul'      => 'nullable|integer|min:0',
            'tanggal_target' => 'nullable|date',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($target->foto && Storage::disk('public')->exists($target->foto)) {
                Storage::disk('public')->delete($target->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto-targets', 'public');
        }

        $data['terkumpul'] = $data['terkumpul'] ?? 0;

        $target->update($data);

        return redirect()->route('target.index')->with('success', 'Target tabungan berhasil diperbarui.');
    }

    public function destroy(Target $target)
    {
        if ($target->user_id !== Auth::id()) abort(403);

        if ($target->foto && Storage::disk('public')->exists($target->foto)) {
            Storage::disk('public')->delete($target->foto);
        }

        $target->delete();

        return redirect()->route('target.index')->with('success', 'Target tabungan berhasil dihapus.');
    }

    public function nabung(Request $request, Target $target)
    {
        if ($target->user_id !== Auth::id()) abort(403);

        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $target->increment('terkumpul', $request->jumlah);

        $pesan = 'Berhasil menabung Rp ' . number_format($request->jumlah, 0, ',', '.') . ' ke ' . $target->nama_target . '!';
        $route = $request->input('from') === 'dashboard' ? 'dashboard' : 'target.index';

        return redirect()->route($route)->with('success', $pesan);
    }
}
