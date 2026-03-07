@extends('layouts.app')

@section('title', 'Detail Transaksi - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar')

    <div class="container py-4" style="max-width: 600px;">
        <h4 class="fw-bold mb-1">Detail Transaksi</h4>
        <hr class="text-success mb-4" style="border-width: 3px; width: 60px;">

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted fw-semibold d-block mb-1">Judul Transaksi</small>
                    <span class="fs-5 fw-bold">{{ $transaksi->judul }}</span>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted fw-semibold d-block mb-1">Tipe Transaksi</small>
                    @if ($transaksi->tipe === 'pemasukan')
                        <span class="badge bg-success bg-opacity-10 text-success fs-6">
                            <i class="bi bi-arrow-down-circle me-1"></i> Pemasukan
                        </span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger fs-6">
                            <i class="bi bi-arrow-up-circle me-1"></i> Pengeluaran
                        </span>
                    @endif
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted fw-semibold d-block mb-1">Kategori</small>
                    <span class="fw-semibold">
                        <i
                            class="bi {{ $transaksi->category->ikon ?? 'bi-tag' }} text-{{ $transaksi->category->warna ?? 'success' }} me-1"></i>
                        {{ $transaksi->category->nama_kategori }}
                    </span>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted fw-semibold d-block mb-1">Nominal</small>
                    <span class="fs-4 fw-bold {{ $transaksi->tipe === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format($transaksi->jumlah, 0, ',', '.') }}
                    </span>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted fw-semibold d-block mb-1">Tanggal</small>
                    <span class="fw-semibold">
                        <i class="bi bi-calendar text-success me-1"></i> {{ $transaksi->tanggal->format('d F Y') }}
                    </span>
                </div>

                <div class="mb-0">
                    <small class="text-muted fw-semibold d-block mb-1">Keterangan</small>
                    <span class="{{ $transaksi->keterangan ? '' : 'text-muted fst-italic' }}">
                        {{ $transaksi->keterangan ?? 'Tidak ada keterangan.' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('transaksi.edit', $transaksi) }}" class="btn btn-success">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <form action="{{ route('transaksi.destroy', $transaksi) }}" method="POST" class="ms-auto"
                onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash me-1"></i> Hapus
                </button>
            </form>
        </div>
    </div>
@endsection
