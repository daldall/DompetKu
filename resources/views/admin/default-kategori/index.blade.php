@extends('layouts.app')

@section('title', 'Admin - Kategori Default - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar-admin')

    <div class="container py-4" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h4 class="fw-bold mb-0">Kategori Default</h4>
            <a href="{{ route('admin.default-kategori.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah
            </a>
        </div>
        <hr class="text-success mb-4" style="border-width: 3px; width: 80px;">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                <small><i class="bi bi-check-circle me-1"></i> {{ session('success') }}</small>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th class="ps-3">Kategori</th>
                                <th>Tipe</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $kategori)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi {{ $kategori->ikon }} text-{{ $kategori->warna }}"></i>
                                            <span class="fw-semibold">{{ $kategori->nama_kategori }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge text-bg-{{ $kategori->warna === 'success' ? 'success' : 'danger' }}">
                                            {{ $kategori->warna === 'success' ? 'Pemasukan' : 'Pengeluaran' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <form action="{{ route('admin.default-kategori.destroy', $kategori) }}" method="POST"
                                            onsubmit="return confirm('Hapus kategori default ini? (Tidak mengubah kategori user yang sudah terlanjur dibuat)')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Belum ada kategori default.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($categories->hasPages())
                    <div class="d-flex justify-content-center my-3">
                        {{ $categories->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="alert alert-info mt-4 mb-0">
            <small>
                <i class="bi bi-info-circle me-1"></i>
                Kategori default akan dicopy otomatis saat user baru registrasi.
            </small>
        </div>
    </div>
@endsection
