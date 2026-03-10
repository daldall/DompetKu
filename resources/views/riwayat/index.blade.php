@extends('layouts.app')

@section('title', 'Riwayat Transaksi - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
@include('includes.navbar')

<div class="container py-4" style="max-width: 960px;">

    <h4 class="fw-bold mb-1">Riwayat Transaksi</h4>
    <hr class="text-success mb-4" style="border-width:3px;width:60px;">

    {{-- Total transaksi --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body py-3">
                    <div class="rounded-circle bg-secondary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2"
                         style="width:40px;height:40px;">
                        <i class="bi bi-receipt text-secondary fs-5"></i>
                    </div>
                    <p class="text-muted small mb-1">Total Transaksi</p>
                    <h6 class="fw-bold mb-0">
                        {{ number_format($totalTransaksi,0,',','.') }}
                    </h6>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('riwayat.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Cari Judul</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control form-control-sm"
                            placeholder="Cari judul..."
                            value="{{ request('search') }}" >
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Tipe</label>
                        <select name="tipe" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="pemasukan" {{ request('tipe')==='pemasukan'?'selected':'' }}>
                                Pemasukan
                            </option>
                            <option value="pengeluaran" {{ request('tipe')==='pengeluaran'?'selected':'' }}>
                                Pengeluaran
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Dari</label>
                        <input
                            type="date"
                            name="tanggal_dari"
                            class="form-control form-control-sm"
                            value="{{ request('tanggal_dari') }}" >
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Sampai</label>
                        <input
                            type="date"
                            name="tanggal_sampai"
                            class="form-control form-control-sm"
                            value="{{ request('tanggal_sampai') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-success btn-sm flex-fill">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('riwayat.index') }}"
                           class="btn btn-outline-secondary btn-sm flex-fill">
                            <i class="bi bi-x-lg me-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
            <hr class="my-3">

            {{-- Export --}}
            <form action="{{ route('riwayat.export') }}" method="GET"
                  class="d-flex align-items-center gap-2 flex-wrap">
                @php
                    $namaBulan = [
                        1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',
                        5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',
                        9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'
                    ];
                @endphp
                <span class="small text-muted">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export
                </span>
                <select name="bulan" class="form-select form-select-sm" style="width:auto">
                    @foreach ($namaBulan as $num => $nama)
                        <option value="{{ $num }}" {{ now()->month==$num?'selected':'' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
                <select name="tahun" class="form-select form-select-sm" style="width:auto">
                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ now()->year==$y?'selected':'' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
                <button class="btn btn-outline-success btn-sm">
                    <i class="bi bi-download"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- List transaksi --}}
    @if ($transactions->isEmpty())
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-5 text-muted">
                <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                Belum ada transaksi.
            </div>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach ($transactions as $trx)
                @php $isPemasukan = $trx->tipe === 'pemasukan'; @endphp
                <div class="card border shadow-sm rounded-3">
                    <div class="card-body py-3">
                        <div class="d-flex gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0
                                {{ $isPemasukan ? 'bg-success' : 'bg-danger' }} bg-opacity-10"
                                style="width:42px;height:42px;">
                                <i class="bi {{ $isPemasukan ? 'bi-arrow-down-circle text-success' : 'bi-arrow-up-circle text-danger' }} fs-5"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between mb-1">
                                    <p class="fw-semibold mb-0">
                                        {{ $trx->judul }}
                                    </p>
                                  <span class="badge
                                        {{ $isPemasukan
                                            ? 'bg-success bg-opacity-10 text-success'
                                            : 'bg-danger bg-opacity-10 text-danger' }}">
                                        {{ ucfirst($trx->tipe) }}
                                    </span>
                                </div>
                                <div class="small text-muted mb-2">
                                    <i class="bi bi-tag me-1"></i>{{ $trx->category->nama_kategori }}
                                    <i class="bi bi-calendar3 ms-1 me-1"></i>{{ $trx->tanggal->format('d M Y') }}
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold {{ $isPemasukan ? 'text-success' : 'text-danger' }}">
                                        {{ $isPemasukan ? '+' : '-' }}Rp
                                        {{ number_format($trx->jumlah,0,',','.') }}
                                    </span>
                                    <a href="{{ route('transaksi.show',$trx) }}"
                                       class="btn btn-outline-success btn-sm">
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
