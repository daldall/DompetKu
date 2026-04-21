<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()?->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $user_id = Auth::user()->id;

        // hitung pemasukan sepanjang waktu
        $pemasukan = Transaction::where('user_id', $user_id)
            ->where('tipe', 'pemasukan')
            ->sum('jumlah');

        // hitung pengeluaran sepanjang waktu
        $pengeluaran = Transaction::where('user_id', $user_id)
            ->where('tipe', 'pengeluaran')
            ->sum('jumlah');

        // hitung saldo (harus selaras dengan total saldo kategori pemasukan)
        $saldo = $pemasukan - $pengeluaran;

        // ambil target 3 terbaru
        $targets = Target::where('user_id', $user_id)->orderBy('id', 'desc')->take(3)->get();

        // data chart pengeluaran berdasarkan kategori
        $pengeluaranKategori = Transaction::where('transactions.user_id', $user_id)
            ->where('transactions.tipe', 'pengeluaran')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->selectRaw('categories.nama_kategori as label, SUM(transactions.jumlah) as total')
            ->groupBy('categories.id', 'categories.nama_kategori')
            ->get();

        return view('dashboard', compact('pemasukan', 'pengeluaran', 'saldo', 'targets', 'pengeluaranKategori'));
    }
}
