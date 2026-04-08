<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('user_id', Auth::user()->id)
            ->where('nama_kategori', '!=', 'Tabungan')
            ->orderBy('id', 'desc')
            ->paginate(5);

        return view('kategori.index', compact('categories'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'ikon'          => 'required|string|max:50',
            'warna'         => 'required|in:success,danger',
        ]);

        $kategori = new Category();
        $kategori->user_id = Auth::user()->id;
        $kategori->nama_kategori = $request->nama_kategori;
        $kategori->ikon = $request->ikon;
        $kategori->warna = $request->warna;

        if ($request->saldo) {
            $kategori->saldo = $request->saldo;
        } else {
            $kategori->saldo = 0;
        }

        $kategori->save();

        // FIX: Catat riwayat transaksi jika ada saldo awal agar sinkron dengan Saldo Bersih
        if ($kategori->saldo > 0) {
            $transaksi = new \App\Models\Transaction();
            $transaksi->user_id = Auth::user()->id;
            $transaksi->category_id = $kategori->id;
            $transaksi->judul = 'Saldo Awal - ' . $kategori->nama_kategori;
            $transaksi->tipe = $kategori->warna == 'success' ? 'pemasukan' : 'pengeluaran';
            $transaksi->jumlah = $kategori->saldo;
            $transaksi->tanggal = date('Y-m-d');
            $transaksi->keterangan = 'Saldo awal saat pembuatan kategori';
            $transaksi->save();
        }

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori = Category::find($id);

        // cek punya user bukan
        if ($kategori->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = Category::find($id);

        if ($kategori->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'ikon'          => 'required|string|max:50',
            'warna'         => 'required|in:success,danger',
        ]);

        $kategori->nama_kategori = $request->nama_kategori;
        $kategori->ikon = $request->ikon;
        $kategori->warna = $request->warna;

        // Kalau merah berarti pengeluaran
        if ($request->warna == 'danger') {
            if ($request->saldo) {
                $kategori->saldo = $request->saldo;
            } else {
                $kategori->saldo = 0;
            }
        }

        $kategori->save();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = Category::find($id);

        if ($kategori->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
