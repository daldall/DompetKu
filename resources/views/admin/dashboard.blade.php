@extends('layouts.app')

@section('title', 'Admin Dashboard - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar-admin')

    <div class="container py-4" style="max-width: 1000px;">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h4 class="fw-bold mb-0">Admin Dashboard</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-people me-1"></i> Users
                </a>
                <a href="{{ route('admin.ai-usage.index') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-cpu me-1"></i> AI Usage
                </a>
            </div>
        </div>
        <hr class="text-success mb-4" style="border-width: 3px; width: 80px;">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                <small><i class="bi bi-check-circle me-1"></i> {{ session('success') }}</small>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4 mb-4">
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <p class="text-muted mb-2 small fw-semibold">Total Users</p>
                        <h4 class="fw-bold mb-0">{{ number_format($totalUsers) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <p class="text-muted mb-2 small fw-semibold">Total Transaksi</p>
                        <h4 class="fw-bold mb-0">{{ number_format($totalTransactions) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <p class="text-muted mb-2 small fw-semibold">Total Pemasukan</p>
                        <h5 class="fw-bold text-success mb-0">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <p class="text-muted mb-2 small fw-semibold">AI Calls (Hari Ini)</p>
                        <h4 class="fw-bold mb-0">{{ number_format($aiCallsToday) }}</h4>
                        <small class="text-muted">Total: {{ number_format($totalAiCalls) }}</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">User Terbaru</h5>
                            <a class="small text-success text-decoration-none fw-semibold" href="{{ route('admin.users.index') }}">
                                Lihat semua <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        @if ($latestUsers->isEmpty())
                            <p class="text-muted mb-0">Belum ada user.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Daftar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($latestUsers as $u)
                                            <tr>
                                                <td class="fw-semibold">{{ $u->nama }}</td>
                                                <td class="text-muted">{{ $u->email }}</td>
                                                <td>
                                                    <span class="badge text-bg-{{ $u->role === 'admin' ? 'success' : 'secondary' }}">{{ $u->role }}</span>
                                                </td>
                                                <td class="text-muted">{{ $u->created_at?->format('d M Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">Monitoring AI (Top User)</h5>
                            <a class="small text-success text-decoration-none fw-semibold" href="{{ route('admin.ai-usage.index') }}">
                                Detail <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>

                        @if ($topAiUsers->isEmpty())
                            <p class="text-muted mb-0">Belum ada penggunaan AI.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th>User</th>
                                            <th class="text-end">Total Calls</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topAiUsers as $row)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $row->user->nama ?? 'User' }}</div>
                                                    <div class="text-muted small">{{ $row->user->email ?? '-' }}</div>
                                                </td>
                                                <td class="text-end fw-semibold">{{ number_format($row->total_calls) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="mt-3 small text-muted">
                            Kategori default tersedia: <span class="fw-semibold">{{ number_format($defaultKategoriCount) }}</span>
                            (<a class="text-success text-decoration-none fw-semibold" href="{{ route('admin.default-kategori.index') }}">kelola</a>)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
