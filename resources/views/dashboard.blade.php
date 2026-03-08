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

        @if ($targets->isNotEmpty())
            <div class="mt-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Target Tabungan</h5>
                    <a href="{{ route('target.index') }}" class="text-success text-decoration-none small fw-semibold">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="row g-3">
                    @foreach ($targets as $target)
                        @php $persen = $target->progress; @endphp
                        <div class="col-12 col-md-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        @if ($target->foto)
                                            <img src="{{ asset('storage/' . $target->foto) }}" alt=""
                                                class="rounded-circle flex-shrink-0"
                                                style="width: 36px; height: 36px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width: 36px; height: 36px;">
                                                <i class="bi bi-bullseye text-success"></i>
                                            </div>
                                        @endif
                                        <h6 class="fw-semibold mb-0 text-truncate">{{ $target->nama_target }}</h6>
                                    </div>
                                    <div class="progress mb-2" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: {{ $persen }}%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Rp
                                            {{ number_format($target->terkumpul, 0, ',', '.') }}</span>
                                        <span class="fw-semibold text-success">{{ $persen }}%</span>
                                    </div>
                                    <p class="text-muted small mb-3">
                                        Target: Rp {{ number_format($target->target_nominal, 0, ',', '.') }}
                                    </p>

                                    <form action="{{ route('target.nabung', $target) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="from" value="dashboard">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-end-0 px-2">Rp</span>
                                            <input type="number" name="jumlah" min="1" required
                                                class="form-control border-start-0" placeholder="Jumlah">
                                            <button type="submit" class="btn btn-success px-3">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
