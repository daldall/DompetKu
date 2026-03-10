@extends('layouts.app')

@section('title', 'Kategori - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar')

    <div class="container py-4" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h4 class="fw-bold mb-0">Kategori</h4>
            <a href="{{ route('kategori.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah
            </a>
        </div>
        <hr class="text-success mb-4" style="border-width: 3px; width: 60px;">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                <small><i class="bi bi-check-circle me-1"></i> {{ session('success') }}</small>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($categories->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-tags fs-1 d-block mb-2"></i>
                    <p class="mb-0">Belum ada kategori. Silakan tambah kategori baru.</p>
                </div>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach ($categories as $kategori)
                    <div class="card border shadow-sm rounded-3" style="border-color: #e9ecef !important;">
                        <div class="card-body d-flex align-items-center justify-content-between px-3 px-md-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-{{ $kategori->warna ?? 'success' }} bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 42px; height: 42px;">
                                    <i
                                        class="bi {{ $kategori->ikon ?? 'bi-tag-fill' }} text-{{ $kategori->warna ?? 'success' }} fs-5"></i>
                                </div>
                                <div>
                                    <span class="fw-semibold d-block">{{ $kategori->nama_kategori }}</span>
                                    @if ($kategori->warna === 'success')
                                        <small class="text-muted">Saldo: <span
                                                class="fw-semibold {{ $kategori->saldo >= 0 ? 'text-success' : 'text-danger' }}">Rp
                                                {{ number_format($kategori->saldo, 0, ',', '.') }}</span></small>
                                    @else
                                        <small class="text-muted">Total: <span class="fw-semibold text-danger">Rp
                                                {{ number_format($kategori->total_pengeluaran ?? 0, 0, ',', '.') }}</span></small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex gap-1 flex-shrink-0">
                                <a href="{{ route('kategori.edit', $kategori) }}" class="btn btn-outline-success btn-sm"
                                    title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('kategori.destroy', $kategori) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($categories->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif
    </div>
@endsection
