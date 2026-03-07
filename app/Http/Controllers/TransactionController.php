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
            ->where('user_id', Auth::id())
            ->latest('tanggal')
            ->get();

        return view('transaksi.index', compact('transactions'));
    }

    public function create()
    {
        $categories = Category::where('user_id', Auth::id())->get();

        return view('transaksi.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'       => 'required|string|max:255',
            'tipe'        => 'required|in:pemasukan,pengeluaran',
            'category_id' => 'required|exists:categories,id',
            'jumlah'      => 'required|integer|min:1',
            'tanggal'     => 'required|date',
            'keterangan'  => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();
        Transaction::create($data);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function show(Transaction $transaksi)
    {
        if ($transaksi->user_id !== Auth::id()) abort(403);

        $transaksi->load('category');

        return view('transaksi.show', compact('transaksi'));
    }

    public function edit(Transaction $transaksi)
    {
        if ($transaksi->user_id !== Auth::id()) abort(403);

        $categories = Category::where('user_id', Auth::id())->get();

        return view('transaksi.edit', compact('transaksi', 'categories'));
    }

    public function update(Request $request, Transaction $transaksi)
    {
        if ($transaksi->user_id !== Auth::id()) abort(403);

        $data = $request->validate([
            'judul'       => 'required|string|max:255',
            'tipe'        => 'required|in:pemasukan,pengeluaran',
            'category_id' => 'required|exists:categories,id',
            'jumlah'      => 'required|integer|min:1',
            'tanggal'     => 'required|date',
            'keterangan'  => 'nullable|string',
        ]);

        $transaksi->update($data);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaksi)
    {
        if ($transaksi->user_id !== Auth::id()) abort(403);

        $transaksi->delete();

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
