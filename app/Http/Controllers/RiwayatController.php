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
        $user_id = Auth::user()->id;

        $query = Transaction::with('category')->where('user_id', $user_id);

        // Filter tipe
        if ($request->tipe != null) {
            if ($request->tipe == 'pemasukan' || $request->tipe == 'pengeluaran') {
                $query = $query->where('tipe', $request->tipe);
            }
        }

        // Filter tanggal dari
        if ($request->tanggal_dari != null) {
            $query = $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        // Filter tanggal sampai
        if ($request->tanggal_sampai != null) {
            $query = $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        // Filter search
        if ($request->search != null) {
            $query = $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $query = $query->orderBy('tanggal', 'desc');

        // Untuk total
        $semua_transaksi = $query->get();
        $totalTransaksi = count($semua_transaksi);

        $totalPemasukan = 0;
        $totalPengeluaran = 0;
        foreach($semua_transaksi as $trx) {
            if ($trx->tipe == 'pemasukan') {
                $totalPemasukan = $totalPemasukan + $trx->jumlah;
            } else {
                $totalPengeluaran = $totalPengeluaran + $trx->jumlah;
            }
        }

        $transactions = $query->paginate(5);
        $transactions->appends($request->all());

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
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Bikin array nama bulan
        $namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $nama_pilih = $namaBulan[$bulan - 1];

        $namaFile = 'Transaksi_' . $nama_pilih . '_' . $tahun . '.xlsx';

        return Excel::download(new TransactionsExport($bulan, $tahun), $namaFile);
    }
}
