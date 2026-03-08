<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::id();

        $pemasukan   = Transaction::where('user_id', $user)
        ->where('tipe', 'pemasukan')
        ->sum('jumlah');

        $pengeluaran = Transaction::where('user_id', $user)
        ->where('tipe', 'pengeluaran')
        ->sum('jumlah');

        $saldo       = $pemasukan - $pengeluaran;
        $targets = Target::where('user_id', $user)
        ->latest()
        ->take(3)
        ->get();

        return view('dashboard', compact('pemasukan', 'pengeluaran', 'saldo', 'targets'));
    }
}
