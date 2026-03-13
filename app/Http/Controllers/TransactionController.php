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
            'judul'       => 'required|string|max:255',
            'tipe'        => 'required|in:pemasukan,pengeluaran',
            'category_id' => [
                'required',
                // Pastikan kategori ada dan milik user yang sedang login (Cegah IDOR / Inspect Element Hack)
                function ($attribute, $value, $fail) {
                    $exists = Category::where('id', $value)->where('user_id', Auth::user()->id)->exists();
                    if (!$exists) {
                        $fail('Kategori yang dipilih tidak valid atau bukan milik Anda.');
                    }
                },
            ],
            // Mencegah nilai angka terlalu besar hingga error database (max 999 Miliar)
            'jumlah'      => 'required|integer|min:1|max:999999999999',
            // Mencegah input transaksi hari esok/masa depan
            'tanggal'     => 'required|date|before_or_equal:today',
            // Keterangan wajib string dan panjang wajar agar hemat database
            'keterangan'  => 'nullable|string|max:1000'
        ]);

        $user_id = Auth::user()->id;
        $notif_pengeluaran = null;
        
        // Cek saldo kalau dia pengeluaran
        if ($request->tipe == 'pengeluaran') {
            $total_masuk = Transaction::where('user_id', $user_id)->where('tipe', 'pemasukan')->sum('jumlah');
            $total_keluar = Transaction::where('user_id', $user_id)->where('tipe', 'pengeluaran')->sum('jumlah');

            $bulan_transaksi = date('m', strtotime($request->tanggal));
            $tahun_transaksi = date('Y', strtotime($request->tanggal));
            
            $total_keluar_bulan_ini = Transaction::where('user_id', $user_id)
                ->where('tipe', 'pengeluaran')
                ->whereMonth('tanggal', $bulan_transaksi)
                ->whereYear('tanggal', $tahun_transaksi)
                ->sum('jumlah');

            $kelipatan_lama = floor($total_keluar_bulan_ini / 1000000);
            $kelipatan_baru = floor(($total_keluar_bulan_ini + $request->jumlah) / 1000000);

            if ($kelipatan_baru > $kelipatan_lama && $kelipatan_baru > 0) {
                $rupiah = number_format($kelipatan_baru * 1000000, 0, ',', '.');
                $notif_pengeluaran = "Peringatan: Total pengeluaran Anda bulan ini telah mencapai Rp {$rupiah}. Harap perhatikan keuangan Anda!";
            }

            $sisa_saldo = $total_masuk - $total_keluar;

            if ($sisa_saldo < $request->jumlah) {
                return redirect()->back()->withInput()->with('error', 'Saldo pemasukan tidak cukup untuk melakukan pengeluaran!');
            }

            // Potong saldo di kategori pemasukan
            $this->potongSaldoKategoriPemasukan($user_id, $request->jumlah);
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

        if ($notif_pengeluaran != null) {
            return redirect()->route('transaksi.index')
                ->with('success', 'Transaksi berhasil ditambahkan.')
                ->with('warning', $notif_pengeluaran);
        }

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
            'judul'       => 'required|string|max:255',
            'tipe'        => 'required|in:pemasukan,pengeluaran',
            'category_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $exists = Category::where('id', $value)->where('user_id', Auth::user()->id)->exists();
                    if (!$exists) {
                        $fail('Kategori yang dipilih tidak valid atau bukan milik Anda.');
                    }
                },
            ],
            'jumlah'      => 'required|integer|min:1|max:999999999999',
            'tanggal'     => 'required|date|before_or_equal:today',
            'keterangan'  => 'nullable|string|max:1000'
        ]);

        $user_id = Auth::user()->id;
        $notif_pengeluaran = null;

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
            $total_masuk = Transaction::where('user_id', $user_id)->where('tipe', 'pemasukan')->where('id', '!=', $transaksi->id)->sum('jumlah');
            $total_keluar = Transaction::where('user_id', $user_id)->where('tipe', 'pengeluaran')->where('id', '!=', $transaksi->id)->sum('jumlah');

            $bulan_transaksi = date('m', strtotime($request->tanggal));
            $tahun_transaksi = date('Y', strtotime($request->tanggal));
            
            $total_keluar_bulan_ini = Transaction::where('user_id', $user_id)
                ->where('tipe', 'pengeluaran')
                ->where('id', '!=', $transaksi->id)
                ->whereMonth('tanggal', $bulan_transaksi)
                ->whereYear('tanggal', $tahun_transaksi)
                ->sum('jumlah');

            $kelipatan_lama = floor($total_keluar_bulan_ini / 1000000);
            $kelipatan_baru = floor(($total_keluar_bulan_ini + $request->jumlah) / 1000000);

            if ($kelipatan_baru > $kelipatan_lama && $kelipatan_baru > 0) {
                $rupiah = number_format($kelipatan_baru * 1000000, 0, ',', '.');
                $notif_pengeluaran = "Peringatan: Total pengeluaran Anda bulan ini telah mencapai Rp {$rupiah}. Harap perhatikan keuangan Anda!";
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
            $this->potongSaldoKategoriPemasukan($user_id, $request->jumlah);
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

        if ($notif_pengeluaran != null) {
            return redirect()->route('transaksi.index')
                ->with('success', 'Transaksi berhasil diupdate.')
                ->with('warning', $notif_pengeluaran);
        }

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

    private function potongSaldoKategoriPemasukan($user_id, $sisa_potong)
    {
        $kategori_pemasukan = Category::where('user_id', $user_id)->where('warna', 'success')->where('saldo', '>', 0)->orderBy('saldo', 'desc')->get();
        foreach ($kategori_pemasukan as $kat) {
            if ($sisa_potong <= 0) break;

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
