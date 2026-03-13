<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Target;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TargetController extends Controller
{
    public function index()
    {
        $targets = Target::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->get();

        return view('target.index', compact('targets'));
    }

    public function create()
    {
        return view('target.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_target'    => 'required|string|max:255',
            'target_nominal' => 'required|integer|min:1',
            'foto'           => 'nullable|image|max:2048',
        ]);

        $target = new Target();
        $target->user_id = Auth::user()->id;
        $target->nama_target = $request->nama_target;
        $target->target_nominal = $request->target_nominal;
        $target->tanggal_target = $request->tanggal_target;

        if ($request->terkumpul != null) {
            $target->terkumpul = $request->terkumpul;
        } else {
            $target->terkumpul = 0;
        }

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $nama = $file->store('foto-targets', 'public');
            $target->foto = $nama;
        }

        $target->save();

        return redirect()->route('target.index')->with('success', 'Target tabungan berhasil dibuat.');
    }

    public function edit($id)
    {
        $target = Target::find($id);

        if ($target->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        return view('target.edit', compact('target'));
    }

    public function update(Request $request, $id)
    {
        $target = Target::find($id);

        if ($target->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        $request->validate([
            'nama_target'    => 'required',
            'target_nominal' => 'required|integer|min:1',
        ]);

        $target->nama_target = $request->nama_target;
        $target->target_nominal = $request->target_nominal;
        $target->tanggal_target = $request->tanggal_target;

        if ($request->terkumpul != null) {
            $target->terkumpul = $request->terkumpul;
        } else {
            $target->terkumpul = 0;
        }

        if ($request->hasFile('foto')) {
            if ($target->foto != null) {
                if (Storage::disk('public')->exists($target->foto)) {
                    Storage::disk('public')->delete($target->foto);
                }
            }
            $target->foto = $request->file('foto')->store('foto-targets', 'public');
        }

        $target->save();

        return redirect()->route('target.index')->with('success', 'Target tabungan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $target = Target::find($id);

        if ($target->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        if ($target->foto != null) {
            Storage::disk('public')->delete($target->foto);
        }

        $target->delete();

        return redirect()->route('target.index')->with('success', 'Target tabungan berhasil dihapus.');
    }

    public function nabung(Request $request, $id)
    {
        $target = Target::find($id);

        if ($target->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        $jumlah = $request->jumlah;
        $user_id = Auth::user()->id;

        $sisa_target = $target->target_nominal - $target->terkumpul;
        if ($jumlah > $sisa_target) {
            $sisa_rp = number_format($sisa_target, 0, ',', '.');
            $pesan = "Nominal kelebihan! Sisa target tabungan ini hanya sisa Rp {$sisa_rp}.";
            
            if ($request->input('from') == 'dashboard') {
                return redirect()->route('dashboard')->with('error', $pesan);
            } else {
                return redirect()->route('target.index')->with('error', $pesan);
            }
        }

        // Cek saldo pemasukan dulu
        $trx_banyak_masuk = Transaction::where('user_id', $user_id)->where('tipe', 'pemasukan')->get();
        $total_pemasukan = 0;
        foreach ($trx_banyak_masuk as $trx) {
            $total_pemasukan = $total_pemasukan + $trx->jumlah;
        }

        $trx_banyak_keluar = Transaction::where('user_id', $user_id)->where('tipe', 'pengeluaran')->get();
        $total_pengeluaran = 0;
        foreach ($trx_banyak_keluar as $trx) {
            $total_pengeluaran = $total_pengeluaran + $trx->jumlah;
        }

        $saldo_sekarang = $total_pemasukan - $total_pengeluaran;

        if ($saldo_sekarang < $jumlah) {
            if ($request->input('from') == 'dashboard') {
                return redirect()->route('dashboard')->with('error', 'Saldo pemasukan tidak cukup untuk menabung.');
            } else {
                return redirect()->route('target.index')->with('error', 'Saldo pemasukan tidak cukup untuk menabung.');
            }
        }

        // Cari atau bikin kategori "Tabungan" khusus untuk nampung riwayat aja (disembunyikan nanti)
        $kategoriTabungan = Category::where('user_id', $user_id)->where('nama_kategori', 'Tabungan')->first();
        if ($kategoriTabungan == null) {
            $kategoriTabungan = new Category();
            $kategoriTabungan->user_id = $user_id;
            $kategoriTabungan->nama_kategori = 'Tabungan';
            $kategoriTabungan->ikon = 'bi-piggy-bank';
            $kategoriTabungan->warna = 'danger';
            $kategoriTabungan->saldo = 0;
            $kategoriTabungan->save();
        }

        // Simpan transaksi
        $transaksi = new Transaction();
        $transaksi->user_id = $user_id;
        $transaksi->category_id = $kategoriTabungan->id;
        $transaksi->judul = 'Nabung - ' . $target->nama_target;
        $transaksi->tipe = 'pengeluaran';
        $transaksi->jumlah = $jumlah;
        $transaksi->tanggal = date('Y-m-d');
        $transaksi->keterangan = 'Menabung ke target "' . $target->nama_target . '"';
        $transaksi->save();

        // Tambah terkumpul nya target
        $target->terkumpul = $target->terkumpul + $jumlah;
        $target->save();

        // Potong saldo di kategori pemasukan buat uang yg ditabung
        $sisa_potong = $jumlah;
        $kategori_pemasukan = Category::where('user_id', $user_id)->where('warna', 'success')->where('saldo', '>', 0)->orderBy('saldo', 'desc')->get();

        foreach ($kategori_pemasukan as $kat) {
            if ($sisa_potong <= 0) {
                break;
            }
            if ($kat->saldo >= $sisa_potong) {
                $kat->saldo = $kat->saldo - $sisa_potong;
                $sisa_potong = 0;
            } else {
                $sisa_potong = $sisa_potong - $kat->saldo;
                $kat->saldo = 0;
            }
            $kat->save();
        }

        $pesan = 'Berhasil menabung Rp ' . number_format($jumlah, 0, ',', '.') . ' ke ' . $target->nama_target . '!';
        if ($request->input('from') == 'dashboard') {
            return redirect()->route('dashboard')->with('success', $pesan);
        } else {
            return redirect()->route('target.index')->with('success', $pesan);
        }
    }
}
