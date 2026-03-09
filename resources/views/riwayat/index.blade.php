@extends('layouts.app')
@section('title', 'Riwayat Transaksi - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')
@section('content')
    @include('includes.navbar')

    <div class="container py-4" style="max-width: 960px;">
        <h4 class="fw-bold mb-1">Riwayat Transaksi</h4>
        <hr class="text-success mb-4" style="border-width: 3px; width: 60px;">

        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3 px-2">
                        <div class="rounded-circle bg-secondary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-receipt text-secondary fs-5"></i>
                        </div>
                        <p class="text-muted small mb-1">Total Transaksi</p>
                        <h6 class="fw-bold mb-0">{{ number_format($totalTransaksi, 0, ',', '.') }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3 px-2">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-arrow-down-circle text-success fs-5"></i>
                        </div>
                        <p class="text-muted small mb-1">Pemasukan</p>
                        <h6 class="fw-bold text-success mb-0">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h6>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-3 px-2">
                        <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-arrow-up-circle text-danger fs-5"></i>
                        </div>
                        <p class="text-muted small mb-1">Pengeluaran</p>
                        <h6 class="fw-bold text-danger mb-0">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body py-3 px-3 px-md-4">
                <form action="{{ route('riwayat.index') }}" method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-3">
                            <label class="form-label small text-muted mb-1">Cari Judul</label>
                            <input type="text" name="search" class="form-control form-control-sm"
                                placeholder="Cari judul..." value="{{ request('search') }}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small text-muted mb-1">Tipe</label>
                            <select name="tipe" class="form-select form-select-sm">
                                <option value="">Semua</option>
                                <option value="pemasukan" {{ request('tipe') === 'pemasukan' ? 'selected' : '' }}>Pemasukan
                                </option>
                                <option value="pengeluaran" {{ request('tipe') === 'pengeluaran' ? 'selected' : '' }}>
                                    Pengeluaran</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small text-muted mb-1">Dari</label>
                            <input type="date" name="tanggal_dari" class="form-control form-control-sm"
                                value="{{ request('tanggal_dari') }}">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label small text-muted mb-1">Sampai</label>
                            <input type="date" name="tanggal_sampai" class="form-control form-control-sm"
                                value="{{ request('tanggal_sampai') }}">
                        </div>
                        <div class="col-6 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-sm flex-fill">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('riwayat.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                                <i class="bi bi-x-lg me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($transactions->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                    <p class="mb-0">Belum ada transaksi.</p>
                </div>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach ($transactions as $trx)
                    @php $isPemasukan = $trx->tipe === 'pemasukan'; @endphp
                    <div class="card border shadow-sm rounded-3" style="border-color: #e9ecef !important;">
                        <div class="card-body px-3 px-md-4 py-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $isPemasukan ? 'bg-success' : 'bg-danger' }} bg-opacity-10"
                                    style="width: 42px; height: 42px;">
                                    <i
                                        class="bi {{ $isPemasukan ? 'bi-arrow-down-circle text-success' : 'bi-arrow-up-circle text-danger' }} fs-5"></i>
                                </div>

                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <p class="fw-semibold mb-0 text-truncate me-2">{{ $trx->judul }}</p>
                                        <span
                                            class="badge {{ $isPemasukan ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} flex-shrink-0">
                                            {{ ucfirst($trx->tipe) }}
                                        </span>
                                    </div>

                                    <div class="d-flex flex-wrap align-items-center gap-1 text-muted small mb-2">
                                        <span><i class="bi bi-tag me-1"></i>{{ $trx->category->nama_kategori }}</span>
                                        <span>&middot;</span>
                                        <span><i
                                                class="bi bi-calendar3 me-1"></i>{{ $trx->tanggal->format('d M Y') }}</span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="fw-bold mb-0 {{ $isPemasukan ? 'text-success' : 'text-danger' }}">
                                            {{ $isPemasukan ? '+' : '-' }}Rp
                                            {{ number_format($trx->jumlah, 0, ',', '.') }}
                                        </p>
                                        <a href="{{ route('transaksi.show', $trx) }}"
                                            class="btn btn-outline-success btn-sm" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($transactions->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $transactions->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif
    </div>
@endsection
