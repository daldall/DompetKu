@extends('layouts.app')

@section('title', 'Edit Transaksi - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar')

    <div class="container py-4" style="max-width: 600px;">
        <h4 class="fw-bold mb-1">Edit Transaksi</h4>
        <hr class="text-success mb-4" style="border-width: 3px; width: 60px;">

        @if (session('error'))
            <div class="alert alert-danger py-2">
                <small><i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}</small>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('transaksi.update', $transaksi) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label for="judul" class="form-label fw-semibold">Judul</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-pencil"></i></span>
                            <input type="text" id="judul" name="judul"
                                value="{{ old('judul', $transaksi->judul) }}" required maxlength="255" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tipe" class="form-label fw-semibold">Tipe Transaksi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-arrow-left-right"></i></span>
                            <select id="tipe" name="tipe" required class="form-select">
                                <option value="pemasukan"
                                    {{ old('tipe', $transaksi->tipe) === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                                <option value="pengeluaran"
                                    {{ old('tipe', $transaksi->tipe) === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold">Kategori</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-tag"></i></span>
                            <select id="category_id" name="category_id" required class="form-select">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" data-warna="{{ $cat->warna }}"
                                        data-saldo="{{ $cat->saldo }}"
                                        {{ old('category_id', $transaksi->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label fw-semibold">Jumlah (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-cash"></i></span>
                            <input type="number" id="jumlah" name="jumlah"
                                value="{{ old('jumlah', $transaksi->jumlah) }}" required min="1" max="999999999999"
                                class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal" class="form-label fw-semibold">Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                            <input type="date" id="tanggal" name="tanggal"
                                value="{{ old('tanggal', $transaksi->tanggal->format('Y-m-d')) }}" required max="{{ date('Y-m-d') }}"
                                class="form-control">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold">
                            Keterangan <span class="text-muted fw-normal">(opsional)</span>
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="3" maxlength="1000" class="form-control" placeholder="Catatan tambahan...">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> Update
                        </button>
                        <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Anggaran pengeluaran dihapus: tidak ada auto-fill jumlah berdasarkan kategori.
        });
    </script>
@endsection
