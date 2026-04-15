@extends('layouts.app')

@section('title', 'Target Tabungan - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar')

    <div class="container py-4" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h4 class="fw-bold mb-0">Target Tabungan</h4>
            <a href="{{ route('target.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Buat Target
            </a>
        </div>
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

        @if ($targets->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-bullseye fs-1 d-block mb-2"></i>
                    <p class="mb-0">Belum ada target tabungan. Yuk buat target pertamamu!</p>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach ($targets as $target)
                    @php $persen = $target->progress; @endphp
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm h-100 overflow-hidden">
                            <div class="bg-success bg-opacity-10 d-flex align-items-center justify-content-center"
                                style="height: 180px;">
                                <i class="bi {{ $target->ikon ?? 'bi-bullseye' }} text-success" style="font-size: 3rem;"></i>
                            </div>

                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0 text-truncate me-2">{{ $target->nama_target }}</h6>
                                    <span
                                        class="badge bg-success bg-opacity-10 text-success flex-shrink-0">{{ $persen }}%</span>
                                </div>

                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-success js-progress-bar" role="progressbar"
                                        data-progress="{{ $persen }}" style="width: 0%"></div>
                                </div>

                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Terkumpul</span>
                                    <span class="fw-semibold text-dark">Rp
                                        {{ number_format($target->terkumpul, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between small text-muted mb-3">
                                    <span>Target</span>
                                    <span class="fw-semibold text-dark">Rp
                                        {{ number_format($target->target_nominal, 0, ',', '.') }}</span>
                                </div>

                                @if ($target->tanggal_target)
                                    <div class="small text-muted mb-3">
                                        <i class="bi bi-calendar3 me-1"></i> Deadline:
                                        {{ $target->tanggal_target->format('d M Y') }}
                                    </div>
                                @endif

                                <form action="{{ route('target.nabung', $target) }}" method="POST" class="mb-3">
                                    @csrf
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text {{ $persen >= 100 ? 'bg-light text-muted' : 'bg-white' }} border-end-0 px-2">Rp</span>
                                        <input type="number" name="jumlah" min="1" {{ $persen >= 100 ? 'disabled' : 'required' }}
                                            class="form-control border-start-0 {{ $persen >= 100 ? 'bg-light' : '' }}" placeholder="{{ $persen >= 100 ? 'Target Tercapai !' : 'Jumlah nabung' }}">
                                        <button type="submit" class="btn btn-success px-3" {{ $persen >= 100 ? 'disabled' : '' }}>
                                            <i class="bi {{ $persen >= 100 ? 'bi-check-lg' : 'bi-plus-lg' }} me-1"></i> {{ $persen >= 100 ? 'Selesai' : 'Nabung' }}
                                        </button>
                                    </div>
                                </form>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('target.edit', $target) }}"
                                        class="btn btn-outline-success btn-sm flex-fill">
                                        <i class="bi bi-pencil-square me-1"></i> Edit
                                    </a>
                                    <form action="{{ route('target.destroy', $target) }}" method="POST" class="flex-fill"
                                        onsubmit="return confirm('Yakin ingin menghapus target ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                            <i class="bi bi-trash3 me-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.js-progress-bar[data-progress]').forEach(function(el) {
            var raw = parseInt(el.getAttribute('data-progress'), 10);
            if (isNaN(raw)) return;
            if (raw < 0) raw = 0;
            if (raw > 100) raw = 100;
            el.style.width = raw + '%';
        });
    });
</script>
@endsection
