<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_kategori',
        'ikon',
        'warna',
        'saldo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Hitung total saldo semua kategori pemasukan milik user.
     */
    public static function totalSaldoPemasukan(int $userId): int
    {
        return static::where('user_id', $userId)
            ->where('warna', 'success')
            ->sum('saldo');
    }

    /**
     * Cek apakah total saldo pemasukan user cukup.
     */
    public static function cukupSaldo(int $userId, int $jumlah): bool
    {
        return static::totalSaldoPemasukan($userId) >= $jumlah;
    }

    /**
     * Kurangi saldo dari kategori pemasukan user (distribusi otomatis).
     */
    public static function kurangiSaldoPemasukan(int $userId, int $jumlah): void
    {
        $sisa = $jumlah;
        $kategoriList = static::where('user_id', $userId)
            ->where('warna', 'success')
            ->where('saldo', '>', 0)
            ->orderBy('saldo', 'desc')
            ->get();

        foreach ($kategoriList as $kat) {
            if ($sisa <= 0) break;
            $potong = min($kat->saldo, $sisa);
            $kat->decrement('saldo', $potong);
            $sisa -= $potong;
        }
    }

    /**
     * Kembalikan saldo ke kategori pemasukan pertama milik user.
     */
    public static function kembalikanSaldoPemasukan(int $userId, int $jumlah): void
    {
        $kategori = static::where('user_id', $userId)
            ->where('warna', 'success')
            ->orderBy('id')
            ->first();

        if ($kategori) {
            $kategori->increment('saldo', $jumlah);
        }
    }
}
