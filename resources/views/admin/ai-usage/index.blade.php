@extends('layouts.app')

@section('title', 'Admin - AI Usage - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar-admin')

    <div class="container py-4" style="max-width: 1100px;">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h4 class="fw-bold mb-0">Monitoring AI (Gemini)</h4>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <hr class="text-success mb-4" style="border-width: 3px; width: 90px;">

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small fw-semibold">Total Calls</div>
                        <div class="fw-bold fs-5">{{ number_format($totalCalls) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small fw-semibold">Success</div>
                        <div class="fw-bold fs-5 text-success">{{ number_format($successCalls) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small fw-semibold">Failed</div>
                        <div class="fw-bold fs-5 text-danger">{{ number_format($failedCalls) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Calls per Hari (14 hari terakhir)</h5>
                        <div style="height: 260px;">
                            <canvas id="aiByDayChart"></canvas>
                        </div>
                        <script type="application/json" id="aiByDayData">@json($byDay)</script>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Ringkas per Fitur</h5>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>Fitur</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Success</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($byFeature as $row)
                                        <tr>
                                            <td class="fw-semibold">{{ $row->feature }}</td>
                                            <td class="text-end">{{ number_format($row->total) }}</td>
                                            <td class="text-end text-success">{{ number_format($row->success_total) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">Belum ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <h6 class="fw-bold mb-2">Top User (berdasarkan jumlah calls)</h6>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th>User</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($topUsers as $row)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $row->user->nama ?? 'User' }}</div>
                                                    <div class="text-muted small">{{ $row->user->email ?? '-' }}</div>
                                                </td>
                                                <td class="text-end fw-semibold">{{ number_format($row->total) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted py-3">Belum ada data.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Log Terbaru (metadata saja)</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="small text-muted">
                                        <th>User</th>
                                        <th>Fitur</th>
                                        <th>Model</th>
                                        <th>API</th>
                                        <th class="text-end">HTTP</th>
                                        <th class="text-end">Status</th>
                                        <th class="text-end">Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($latestLogs as $log)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $log->user->nama ?? 'User' }}</div>
                                                <div class="text-muted small">{{ $log->user->email ?? '-' }}</div>
                                            </td>
                                            <td class="fw-semibold">{{ $log->feature }}</td>
                                            <td class="text-muted">{{ $log->model ?? '-' }}</td>
                                            <td class="text-muted">{{ $log->api_version ?? '-' }}</td>
                                            <td class="text-end">{{ $log->status_code ?? '-' }}</td>
                                            <td class="text-end">
                                                <span class="badge text-bg-{{ $log->success ? 'success' : 'danger' }}">
                                                    {{ $log->success ? 'success' : ($log->error_type ?? 'failed') }}
                                                </span>
                                            </td>
                                            <td class="text-end text-muted">{{ $log->created_at?->format('d M Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">Belum ada log.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-4 mb-0">
                            <small>
                                <i class="bi bi-shield-check me-1"></i>
                                Privasi: sistem hanya menyimpan metadata (fitur/model/api/status), tidak menyimpan gambar struk, prompt, atau teks hasil OCR.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dataEl = document.getElementById('aiByDayData');
        const rows = dataEl ? JSON.parse(dataEl.textContent || '[]') : [];

        const labels = rows.map(r => r.day);
        const totals = rows.map(r => r.total);

        const ctx = document.getElementById('aiByDayChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'AI Calls',
                        data: totals,
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.15)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                    },
                },
                plugins: {
                    legend: { display: false },
                },
            },
        });
    });
</script>
@endsection
