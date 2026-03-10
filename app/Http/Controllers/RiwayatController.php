<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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

        $transactions = $query->paginate(5)->withQueryString();

        return view('riwayat.index', compact(
            'transactions',
            'totalTransaksi',
            'totalPemasukan',
            'totalPengeluaran'
        ));
    }

    public function export(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000|max:2100',
        ]);

        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $fileName = 'Transaksi_' . $namaBulan[$bulan] . '_' . $tahun . '.xlsx';

        return Excel::download(new TransactionsExport($bulan, $tahun), $fileName);
    }
}
