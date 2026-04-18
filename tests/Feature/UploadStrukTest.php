<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UploadStrukTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_struk_requires_image(): void
    {
        $user = User::query()->create([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('transaksi.uploadStruk'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['image']);
    }

    public function test_upload_struk_rejects_non_image_file(): void
    {
        $user = User::query()->create([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('transaksi.uploadStruk'), [
            'image' => UploadedFile::fake()->create('nota.pdf', 10, 'application/pdf'),
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['image']);
    }

    public function test_upload_struk_rejects_too_large_image(): void
    {
        $user = User::query()->create([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('transaksi.uploadStruk'), [
            'image' => UploadedFile::fake()->image('struk.jpg')->size(6000), // KB
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['image']);
    }

    public function test_upload_struk_creates_expense_transaction_and_category(): void
    {
        $user = User::query()->create([
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Seed pemasukan supaya saldo cukup, dan ada saldo kategori pemasukan yang bisa dipotong.
        $kategoriPemasukan = Category::query()->create([
            'user_id' => $user->id,
            'nama_kategori' => 'Gaji',
            'ikon' => 'bi-wallet2',
            'warna' => 'success',
            'saldo' => 100000,
        ]);

        Transaction::query()->create([
            'user_id' => $user->id,
            'category_id' => $kategoriPemasukan->id,
            'judul' => 'Gaji',
            'tipe' => 'pemasukan',
            'jumlah' => 100000,
            'tanggal' => now()->toDateString(),
            'keterangan' => null,
        ]);

        config(['services.gemini.key' => 'fake-key']);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => '{"total": 50000}'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('transaksi.uploadStruk'), [
            'image' => UploadedFile::fake()->image('struk.jpg'),
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'total' => 50000,
        ]);

        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'nama_kategori' => 'Struk',
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'judul' => 'Struk',
            'tipe' => 'pengeluaran',
            'jumlah' => 50000,
        ]);

        $kategoriPemasukan->refresh();
        $this->assertSame(50000, (int) $kategoriPemasukan->saldo);
    }
}
