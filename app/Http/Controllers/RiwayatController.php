<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('category')
            ->where('user_id', Auth::id())
            ->latest('tanggal');

        if ($request->filled('tipe') && in_array($request->tipe, ['pemasukan', 'pengeluaran'])) {
            $query->where('tipe', $request->tipe);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $totalTransaksi   = (clone $query)->count();
        $totalPemasukan   = (clone $query)->where('tipe', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = (clone $query)->where('tipe', 'pengeluaran')->sum('jumlah');

        $transactions = $query->paginate(15)->withQueryString();

        return view('riwayat.index', compact(
            'transactions',
            'totalTransaksi',
            'totalPemasukan',
            'totalPengeluaran'
        ));
    }
}
