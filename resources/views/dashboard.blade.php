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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                <small><i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}</small>
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

        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Statistik Pengeluaran Kategori</h5>
                        @if($pengeluaranKategori->isEmpty())
                            <p class="text-muted text-center py-4 mb-0">Belum ada data pengeluaran untuk ditampilkan.</p>
                        @else
                            <div style="height: 300px; display: flex; justify-content: center;">
                                <canvas id="expenseChart"></canvas>
                            </div>
                        @endif
                    </div>
                </div>
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
                                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 36px; height: 36px;">
                                            <i class="bi {{ $target->ikon ?? 'bi-bullseye' }} text-success"></i>
                                        </div>
                                        <h6 class="fw-semibold mb-0 text-truncate">{{ $target->nama_target }}</h6>
                                    </div>
                                    <div class="progress mb-2" style="height: 6px;">
                                        <div class="progress-bar bg-success js-progress-bar" data-progress="{{ $persen }}" style="width: 0%"></div>
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
                                            <span class="input-group-text {{ $persen >= 100 ? 'bg-light text-muted' : 'bg-white' }} border-end-0 px-2">Rp</span>
                                            <input type="number" name="jumlah" min="1" {{ $persen >= 100 ? 'disabled' : 'required' }}
                                                class="form-control border-start-0 {{ $persen >= 100 ? 'bg-light' : '' }}" placeholder="{{ $persen >= 100 ? 'Target Tercapai' : 'Jumlah' }}">
                                            <button type="submit" class="btn btn-success px-3" {{ $persen >= 100 ? 'disabled' : '' }}>
                                                <i class="bi {{ $persen >= 100 ? 'bi-check-lg' : 'bi-plus-lg' }}"></i>
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="application/json" id="expenseChartData">@json($pengeluaranKategori)</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.js-progress-bar[data-progress]').forEach((el) => {
            const raw = parseInt(el.getAttribute('data-progress'), 10);
            if (Number.isNaN(raw)) return;
            const val = Math.max(0, Math.min(100, raw));
            el.style.width = val + '%';
        });

        const ctx = document.getElementById('expenseChart');
        if (ctx) {
            const dataEl = document.getElementById('expenseChartData');
            const chartData = dataEl ? JSON.parse(dataEl.textContent || '[]') : [];

            // Format angka ke format Rupiah
            const formatRupiah = (angka) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            };

            const labels = chartData.map(item => item.label);
            const data = chartData.map(item => item.total);

            const bgColors = [
                '#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#ff9f40', 
                '#c9cbcf', '#e83e8c', '#6f42c1', '#20c997'
            ];

            // Jika kategori lebih dari 10, array warna akan di-loop
            const colorsToUse = labels.map((_, index) => bgColors[index % bgColors.length]);

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colorsToUse,
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    family: "'Segoe UI', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif"
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += formatRupiah(context.parsed);
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
