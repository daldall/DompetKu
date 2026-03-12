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

        // hitung pemasukan
        $pemasukan = 0;
        $trx_pemasukan = Transaction::where('user_id', $user_id)
        ->where('tipe', 'pemasukan')
        ->get();
        foreach($trx_pemasukan as $trx) {
            $pemasukan = $pemasukan + $trx->jumlah;
        }

        // hitung pengeluaran
        $pengeluaran = 0;
        $trx_pengeluaran = Transaction::where('user_id', $user_id)
        ->where('tipe', 'pengeluaran')
        ->get();
        foreach($trx_pengeluaran as $trx) {
            $pengeluaran = $pengeluaran + $trx->jumlah;
        }

        // hitung saldo
        $saldo = $pemasukan - $pengeluaran;

        // ambil target 3 terbaru
        $targets = Target::where('user_id', $user_id)->orderBy('id', 'desc')->take(3)->get();

        return view('dashboard', compact('pemasukan', 'pengeluaran', 'saldo', 'targets'));
    }
}
