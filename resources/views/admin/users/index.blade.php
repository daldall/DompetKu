@extends('layouts.app')

@section('title', 'Admin - Users - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar-admin')

    <div class="container py-4" style="max-width: 1000px;">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h4 class="fw-bold mb-0">Manajemen User</h4>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <hr class="text-success mb-4" style="border-width: 3px; width: 80px;">

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small fw-semibold">Total User</div>
                        <div class="fw-bold fs-5">{{ number_format($users->total()) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small fw-semibold">Total Transaksi Global</div>
                        <div class="fw-bold fs-5">{{ number_format($totalTransactionsGlobal) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small fw-semibold">Total AI Calls Global</div>
                        <div class="fw-bold fs-5">{{ number_format($totalAiCallsGlobal) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="small text-muted">
                                <th class="ps-3">Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th class="text-end">Transaksi</th>
                                <th class="text-end pe-3">Daftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $u)
                                <tr>
                                    <td class="ps-3 fw-semibold">{{ $u->nama }}</td>
                                    <td class="text-muted">{{ $u->email }}</td>
                                    <td>
                                        <span class="badge text-bg-{{ $u->role === 'admin' ? 'success' : 'secondary' }}">{{ $u->role }}</span>
                                    </td>
                                    <td class="text-end">{{ number_format($u->transactions_count) }}</td>
                                    <td class="text-end pe-3 text-muted">{{ $u->created_at?->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada user.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="d-flex justify-content-center my-3">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

        <div class="alert alert-info mt-4 mb-0">
            <small>
                <i class="bi bi-shield-check me-1"></i>
                Privasi: halaman ini hanya menampilkan metadata (nama/email/jumlah transaksi), bukan isi transaksi.
            </small>
        </div>
    </div>
@endsection
