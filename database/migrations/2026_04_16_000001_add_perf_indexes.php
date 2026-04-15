<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'tipe'], 'transactions_user_tipe_idx');
            $table->index(['user_id', 'tipe', 'tanggal'], 'transactions_user_tipe_tanggal_idx');
            $table->index(['user_id', 'tanggal', 'id'], 'transactions_user_tanggal_id_idx');
            $table->index(['user_id', 'category_id', 'tipe'], 'transactions_user_category_tipe_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['user_id', 'nama_kategori'], 'categories_user_nama_idx');
            $table->index(['user_id', 'warna', 'saldo'], 'categories_user_warna_saldo_idx');
        });

        Schema::table('targets', function (Blueprint $table) {
            $table->index(['user_id', 'id'], 'targets_user_id_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_user_tipe_idx');
            $table->dropIndex('transactions_user_tipe_tanggal_idx');
            $table->dropIndex('transactions_user_tanggal_id_idx');
            $table->dropIndex('transactions_user_category_tipe_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_user_nama_idx');
            $table->dropIndex('categories_user_warna_saldo_idx');
        });

        Schema::table('targets', function (Blueprint $table) {
            $table->dropIndex('targets_user_id_id_idx');
        });
    }
};
