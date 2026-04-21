<?php

namespace Database\Seeders;

use App\Models\DefaultCategory;
use Illuminate\Database\Seeder;

class DefaultCategorySeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['nama_kategori' => 'Gaji', 'ikon' => 'bi-wallet2', 'warna' => 'success'],
            ['nama_kategori' => 'Bonus', 'ikon' => 'bi-cash-stack', 'warna' => 'success'],
            ['nama_kategori' => 'Makan', 'ikon' => 'bi-cup-hot-fill', 'warna' => 'danger'],
            ['nama_kategori' => 'Transport', 'ikon' => 'bi-bus-front-fill', 'warna' => 'danger'],
        ];

        foreach ($defaults as $row) {
            DefaultCategory::query()->firstOrCreate(
                ['nama_kategori' => $row['nama_kategori']],
                $row
            );
        }
    }
}
