@extends('layouts.app')

@section('title', 'Dashboard - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar')

    <div class="container py-4" style="max-width: 900px;">
        <h4 class="fw-bold mb-1">Dashboard</h4>
        <hr class="text-success mb-4" style="border-width: 3px; width: 60px;">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                <small><i class="bi bi-check-circle me-1"></i> {{ session('success') }}</small>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <p class="text-muted mb-2 small fw-semibold">Total Pemasukan</p>
                        <h4 class="fw-bold text-success mb-0">Rp {{ number_format($pemasukan, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <p class="text-muted mb-2 small fw-semibold">Total Pengeluaran</p>
                        <h4 class="fw-bold text-danger mb-0">Rp {{ number_format($pengeluaran, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <p class="text-muted mb-2 small fw-semibold">Saldo Bersih</p>
                        <h4 class="fw-bold {{ $saldo >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                            Rp {{ number_format($saldo, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-6">
                <a href="{{ route('kategori.index') }}"
                    class="btn btn-light border shadow-sm w-100 py-4 d-flex flex-column align-items-center gap-2 text-decoration-none">
                    <i class="bi bi-journal-text fs-2 text-secondary"></i>
                    <span class="fw-semibold text-dark">Manage Kategori</span>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('transaksi.create') }}"
                    class="btn btn-light border shadow-sm w-100 py-4 d-flex flex-column align-items-center gap-2 text-decoration-none">
                    <i class="bi bi-cash-coin fs-2 text-secondary"></i>
                    <span class="fw-semibold text-dark">Buat Transaksi</span>
                </a>
            </div>
        </div>
    </div>
@endsection
