@extends('layouts.app')

@section('title', 'Tambah Transaksi - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar')

    <div class="container py-4" style="max-width: 600px;">
        <h4 class="fw-bold mb-1">Tambah Transaksi</h4>
        <hr class="text-success mb-4" style="border-width: 3px; width: 60px;">

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
                <form action="{{ route('transaksi.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="judul" class="form-label fw-semibold">Judul</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-pencil"></i></span>
                            <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required
                                class="form-control" placeholder="Contoh: Gaji Bulanan">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tipe" class="form-label fw-semibold">Tipe Transaksi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-arrow-left-right"></i></span>
                            <select id="tipe" name="tipe" required class="form-select">
                                <option value="" disabled {{ old('tipe') ? '' : 'selected' }}>Pilih tipe</option>
                                <option value="pemasukan" {{ old('tipe') === 'pemasukan' ? 'selected' : '' }}>Pemasukan
                                </option>
                                <option value="pengeluaran" {{ old('tipe') === 'pengeluaran' ? 'selected' : '' }}>
                                    Pengeluaran</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold">Kategori</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-tag"></i></span>
                            <select id="category_id" name="category_id" required class="form-select">
                                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Pilih kategori
                                </option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id') == $cat->id ? 'selected' : '' }}>
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
                            <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" required
                                min="1" class="form-control" placeholder="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal" class="form-label fw-semibold">Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                            <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                                required class="form-control">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold">
                            Keterangan <span class="text-muted fw-normal">(opsional)</span>
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="3" class="form-control" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> Simpan
                        </button>
                        <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
