<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('category')
            ->where('user_id', Auth::user()->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(5);

        return view('transaksi.index', compact('transactions'));
    }

    public function create()
    {
        $categories = Category::where('user_id', Auth::user()->id)
            ->where('nama_kategori', '!=', 'Tabungan')
            ->get();

        return view('transaksi.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'       => 'required',
            'tipe'        => 'required|in:pemasukan,pengeluaran',
            'category_id' => 'required',
            'jumlah'      => 'required|integer|min:1',
            'tanggal'     => 'required|date',
        ]);

        $user_id = Auth::user()->id;

        // Cek saldo kalau dia pengeluaran
        if ($request->tipe == 'pengeluaran') {
            $trx_pemasukan_all = Transaction::where('user_id', $user_id)->where('tipe', 'pemasukan')->get();
            $total_masuk = 0;
            foreach ($trx_pemasukan_all as $trx) {
                $total_masuk = $total_masuk + $trx->jumlah;
            }

            $trx_pengeluaran_all = Transaction::where('user_id', $user_id)->where('tipe', 'pengeluaran')->get();
            $total_keluar = 0;
            foreach ($trx_pengeluaran_all as $trx) {
                $total_keluar = $total_keluar + $trx->jumlah;
            }

            $sisa_saldo = $total_masuk - $total_keluar;

            if ($sisa_saldo < $request->jumlah) {
                return redirect()->back()->withInput()->with('error', 'Saldo pemasukan tidak cukup untuk melakukan pengeluaran!');
            }

            // Potong saldo di kategori pemasukan
            $sisa_potong = $request->jumlah;
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
        }

        // Kalau pemasukan, tambah ke kategori pemasukan
        if ($request->tipe == 'pemasukan') {
            $kategori = Category::find($request->category_id);
            if ($kategori) {
                $kategori->saldo = $kategori->saldo + $request->jumlah;
                $kategori->save();
            }
        }

        $transaksi = new Transaction();
        $transaksi->user_id = $user_id;
        $transaksi->judul = $request->judul;
        $transaksi->tipe = $request->tipe;
        $transaksi->category_id = $request->category_id;
        $transaksi->jumlah = $request->jumlah;
        $transaksi->tanggal = $request->tanggal;
        $transaksi->keterangan = $request->keterangan;
        $transaksi->save();

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function show($id)
    {
        $transaksi = Transaction::find($id);

        if ($transaksi->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        return view('transaksi.show', compact('transaksi'));
    }

    public function edit($id)
    {
        $transaksi = Transaction::find($id);

        if ($transaksi->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        $categories = Category::where('user_id', Auth::user()->id)
            ->where('nama_kategori', '!=', 'Tabungan')
            ->get();

        return view('transaksi.edit', compact('transaksi', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $transaksi = Transaction::find($id);

        if ($transaksi->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        $request->validate([
            'judul'       => 'required',
            'tipe'        => 'required|in:pemasukan,pengeluaran',
            'category_id' => 'required',
            'jumlah'      => 'required|integer|min:1',
            'tanggal'     => 'required|date',
        ]);

        $user_id = Auth::user()->id;

        // Kembalikan saldo yang lama dulu
        if ($transaksi->tipe == 'pemasukan') {
            $kategori_lama = Category::find($transaksi->category_id);
            if ($kategori_lama) {
                $kategori_lama->saldo = $kategori_lama->saldo - $transaksi->jumlah;
                $kategori_lama->save();
            }
        } else {
            // Kalau dlu pengeluaran, kembalikan saldonya ke salah satu kategori pemasukan
            $kategori_balik = Category::where('user_id', $user_id)->where('warna', 'success')->orderBy('id', 'desc')->first();
            if ($kategori_balik) {
                $kategori_balik->saldo = $kategori_balik->saldo + $transaksi->jumlah;
                $kategori_balik->save();
            }
        }

        // Cek saldo kalau yg baru adalah pengeluaran
        if ($request->tipe == 'pengeluaran') {
            // Hitung ulang saldo dgn transaksi ini dianggap 0 dulu (udah dicancel diatas / exclude)
            $trx_pemasukan_semua = Transaction::where('user_id', $user_id)->where('tipe', 'pemasukan')->where('id', '!=', $transaksi->id)->get();
            $total_masuk = 0;
            foreach ($trx_pemasukan_semua as $trx) {
                $total_masuk = $total_masuk + $trx->jumlah;
            }

            $trx_pengeluaran_semua = Transaction::where('user_id', $user_id)->where('tipe', 'pengeluaran')->where('id', '!=', $transaksi->id)->get();
            $total_keluar = 0;
            foreach ($trx_pengeluaran_semua as $trx) {
                $total_keluar = $total_keluar + $trx->jumlah;
            }

            // + kalo dia asalnya pemasukan, berarti tadi saldo belum berkurang di DB transaksi
            $sisa_saldo = $total_masuk - $total_keluar;

            if ($sisa_saldo < $request->jumlah) {
                // Balikin lg kategori yg lama kalau tadi sempet dikurangin / ditambah
                if ($transaksi->tipe == 'pemasukan') {
                    $kategori_lama = Category::find($transaksi->category_id);
                    if ($kategori_lama) {
                        $kategori_lama->saldo = $kategori_lama->saldo + $transaksi->jumlah;
                        $kategori_lama->save();
                    }
                } else {
                    $kategori_balik = Category::where('user_id', $user_id)->where('warna', 'success')->orderBy('id', 'desc')->first();
                    if ($kategori_balik) {
                        $kategori_balik->saldo = $kategori_balik->saldo - $transaksi->jumlah;
                        $kategori_balik->save();
                    }
                }

                return redirect()->back()->withInput()->with('error', 'Saldo pemasukan tidak cukup!');
            }

            // Potong saldo di kategori pemasukan buat yg baru
            $sisa_potong = $request->jumlah;
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
        }

        // Terapkan perubahan kategori baru kalau pemasukan
        if ($request->tipe == 'pemasukan') {
            $kategori_baru = Category::find($request->category_id);
            if ($kategori_baru) {
                $kategori_baru->saldo = $kategori_baru->saldo + $request->jumlah;
                $kategori_baru->save();
            }
        }

        $transaksi->judul = $request->judul;
        $transaksi->tipe = $request->tipe;
        $transaksi->category_id = $request->category_id;
        $transaksi->jumlah = $request->jumlah;
        $transaksi->tanggal = $request->tanggal;
        $transaksi->keterangan = $request->keterangan;
        $transaksi->save();

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diupdate.');
    }

    public function destroy($id)
    {
        $transaksi = Transaction::find($id);

        if ($transaksi->user_id != Auth::user()->id) {
            return redirect()->back();
        }

        // Kalau dihapus berarti batalin pemasukan
        if ($transaksi->tipe == 'pemasukan') {
            $kategori = Category::find($transaksi->category_id);
            if ($kategori) {
                $kategori->saldo = $kategori->saldo - $transaksi->jumlah;
                $kategori->save();
            }
        } else {
             // Kalau dihapus batalin pengeluaran, balikin saldonya
            $kategori_balik = Category::where('user_id', Auth::user()->id)->where('warna', 'success')->orderBy('id', 'desc')->first();
            if ($kategori_balik) {
                $kategori_balik->saldo = $kategori_balik->saldo + $transaksi->jumlah;
                $kategori_balik->save();
            }
        }

        $transaksi->delete();

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
