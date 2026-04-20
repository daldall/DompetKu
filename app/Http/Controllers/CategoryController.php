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
            ->whereNotIn('nama_kategori', ['Tabungan', 'Struk'])
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

        $kategori->saldo = 0;

        $kategori->save();

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

        // Anggaran pengeluaran tidak digunakan lagi.
        if ($request->warna === 'danger') {
            $kategori->saldo = 0;
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

    private function potongSaldoKategoriPemasukan($user_id, $sisa_potong)
    {
        $kategori_pemasukan = Category::where('user_id', $user_id)
            ->where('warna', 'success')
            ->where('saldo', '>', 0)
            ->orderBy('saldo', 'desc')
            ->get();

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
    }
}
