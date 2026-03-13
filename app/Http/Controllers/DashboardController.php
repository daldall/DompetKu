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
        $user_id = Auth::user()->id;

        // hitung pemasukan bulan ini
        $pemasukan = Transaction::where('user_id', $user_id)
            ->where('tipe', 'pemasukan')
            ->whereMonth('tanggal', date('m'))
            ->whereYear('tanggal', date('Y'))
            ->sum('jumlah');

        // hitung pengeluaran bulan ini
        $pengeluaran = Transaction::where('user_id', $user_id)
            ->where('tipe', 'pengeluaran')
            ->whereMonth('tanggal', date('m'))
            ->whereYear('tanggal', date('Y'))
            ->sum('jumlah');

        // hitung saldo
        $saldo = $pemasukan - $pengeluaran;

        // ambil target 3 terbaru
        $targets = Target::where('user_id', $user_id)->orderBy('id', 'desc')->take(3)->get();

        return view('dashboard', compact('pemasukan', 'pengeluaran', 'saldo', 'targets'));
    }
}
