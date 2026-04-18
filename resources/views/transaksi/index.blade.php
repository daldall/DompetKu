@extends('layouts.app')

@section('title', 'Transaksi - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar')

    <div class="container py-4" style="max-width: 900px;">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h4 class="fw-bold mb-0">Transaksi</h4>
            <a href="{{ route('transaksi.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Transaksi
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

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show py-2" role="alert">
                <small><i class="bi bi-exclamation-triangle me-1"></i> {{ session('warning') }}</small>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                       <p class="fw-semibold mb-0">Upload Struk (Otomatis)</p>
                      <small class="text-muted">Upload struk, langsung nyatet pengeluaran + total keisi otomatis.</small>  
                    </div>
                </div>

                <form class="mt-3" action="{{ route('transaksi.uploadStruk') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md">
                            <label for="image" class="form-label fw-semibold mb-1">Foto struk</label>
                            <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" required>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Format: JPG/PNG/WEBP</div>
                        </div>
                        <div class="col-12 col-md-auto">
                            <button type="submit" class="btn btn-success w-100" id="btnProsesStruk" data-require-preview="1">
                                <i class="bi bi-upload me-1"></i> Proses Struk
                            </button>
                        </div>
                    </div>

                    <div class="mt-3 d-none" id="strukPreviewWrap" aria-live="polite">
                        <p class="fw-semibold mb-2">Preview struk</p>
                        <div class="border rounded-3 p-2 bg-white">
                            <img id="strukPreviewImg" alt="Preview struk" class="img-fluid w-100" style="max-height: 420px; object-fit: contain;">
                        </div>
                        <div class="form-text">Pastikan struk terbaca, lalu klik "Proses Struk".</div>
                    </div>
                </form>
            </div>
        </div>

        @if ($transactions->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-cash-coin fs-1 d-block mb-2"></i>
                    <p class="mb-0">Belum ada transaksi. Silakan tambah transaksi baru.</p>
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
                                        <span><i
                                                class="bi bi-calendar3 me-1"></i>{{ $trx->tanggal->format('d M Y') }}</span>
                                        <span>&middot;</span>
                                        <span><i
                                                class="bi {{ $trx->category->ikon ?? 'bi-tag' }} text-{{ $trx->category->warna ?? 'success' }} me-1"></i>{{ $trx->category->nama_kategori }}</span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="fw-bold mb-0 {{ $isPemasukan ? 'text-success' : 'text-danger' }}">
                                            {{ $isPemasukan ? '+' : '-' }}Rp
                                            {{ number_format($trx->jumlah, 0, ',', '.') }}
                                        </p>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('transaksi.edit', $trx) }}"
                                                class="btn btn-outline-success btn-sm" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('transaksi.destroy', $trx) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
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

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('image');
            const previewWrap = document.getElementById('strukPreviewWrap');
            const previewImg = document.getElementById('strukPreviewImg');
            const submitBtn = document.getElementById('btnProsesStruk');

            if (!input || !previewWrap || !previewImg || !submitBtn) return;

            let objectUrl = null;

            const disableUntilPreview = submitBtn.dataset.requirePreview === '1';
            if (disableUntilPreview) {
                submitBtn.disabled = true;
            }

            const resetPreview = () => {
                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }
                previewImg.removeAttribute('src');
                previewWrap.classList.add('d-none');
                if (disableUntilPreview) {
                    submitBtn.disabled = true;
                }
            };

            input.addEventListener('change', function() {
                const file = input.files && input.files[0] ? input.files[0] : null;
                if (!file) {
                    resetPreview();
                    return;
                }

                if (!file.type || !file.type.startsWith('image/')) {
                    resetPreview();
                    return;
                }

                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                }

                objectUrl = URL.createObjectURL(file);
                previewImg.src = objectUrl;
                previewWrap.classList.remove('d-none');

                if (disableUntilPreview) {
                    submitBtn.disabled = false;
                }
            });
        });
    </script>
@endsection
