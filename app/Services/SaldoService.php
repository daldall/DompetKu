<?php

namespace App\Services;

use App\Models\Category;

class SaldoService
{
    /**
     * Proses saldo saat transaksi baru dibuat.
     * - Pemasukan: tambah saldo ke kategori pemasukan.
     * - Pengeluaran: kurangi saldo dari total saldo pemasukan user.
     * Return false jika saldo tidak cukup.
     */
    public function prosesTransaksiBaru(int $userId, string $tipe, int $categoryId, int $jumlah): bool
    {
        $kategori = Category::findOrFail($categoryId);

        if ($tipe === 'pemasukan') {
            $kategori->increment('saldo', $jumlah);
            return true;
        }

        // Pengeluaran: cek total saldo pemasukan user
        if (!Category::cukupSaldo($userId, $jumlah)) {
            return false;
        }

        Category::kurangiSaldoPemasukan($userId, $jumlah);
        return true;
    }

    /**
     * Kembalikan saldo dari transaksi yang akan diubah/dihapus.
     * - Pemasukan: kurangi saldo kategori pemasukan.
     * - Pengeluaran: kembalikan saldo ke kategori pemasukan user.
     */
    public function kembalikanSaldo(int $userId, string $tipe, int $categoryId, int $jumlah): void
    {
        $kategori = Category::findOrFail($categoryId);

        if ($tipe === 'pemasukan') {
            $kategori->decrement('saldo', $jumlah);
        } else {
            // Kembalikan ke saldo pemasukan
            Category::kembalikanSaldoPemasukan($userId, $jumlah);
        }
    }

    /**
     * Proses update transaksi (kembalikan lama + terapkan baru).
     * Return false jika saldo tidak cukup.
     */
    public function prosesUpdateTransaksi(
        int $userId,
        string $tipeLama, int $categoryIdLama, int $jumlahLama,
        string $tipeBaru, int $categoryIdBaru, int $jumlahBaru
    ): bool {
        // Kembalikan saldo lama
        $this->kembalikanSaldo($userId, $tipeLama, $categoryIdLama, $jumlahLama);

        // Terapkan saldo baru
        if (!$this->prosesTransaksiBaru($userId, $tipeBaru, $categoryIdBaru, $jumlahBaru)) {
            // Rollback: kembalikan saldo lama ke semula
            $this->prosesTransaksiBaru($userId, $tipeLama, $categoryIdLama, $jumlahLama);
            return false;
        }

        return true;
    }
}
