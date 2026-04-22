@extends('layouts.app')

@section('title', 'Tambah Transaksi - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar')

    <div class="container py-4" style="max-width: 600px;">
        <h4 class="fw-bold mb-1">Tambah Transaksi</h4>
        <hr class="text-success mb-4" style="border-width: 3px; width: 60px;">

        @if (session('error'))
            <div class="alert alert-danger py-2">
                <small><i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}</small>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="mb-4">
                    <p class="fw-semibold mb-1">Scan Struk (AI) <span class="text-muted fw-normal">(opsional)</span></p>
                    <small class="text-muted">Upload struk untuk isi otomatis tipe, jumlah, dan keterangan.</small>

                    <div class="mt-3">
                        <label for="struk_image" class="form-label fw-semibold mb-1">Gambar struk</label>
                        <input class="form-control" type="file" id="struk_image" accept="image/jpeg,image/png,image/webp,image/avif,image/heic,image/heif">
                        <div class="form-text">Format: JPG/JPEG/PNG/WEBP/AVIF/HEIC (maks 5MB)</div>
                        <div class="small mt-2 d-none" id="scanStrukStatus" aria-live="polite">
                            <span id="scanStrukSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                            <span id="scanStrukStatusText"></span>
                        </div>
                    </div>

                    <div class="mt-3 d-none" id="scanStrukPreviewWrap" aria-live="polite">
                        <p class="fw-semibold mb-2">Preview struk</p>
                        <div class="border rounded-3 p-2 bg-white">
                            <img id="scanStrukPreviewImg" alt="Preview struk" class="img-fluid w-100" style="max-height: 420px; object-fit: contain;">
                        </div>
                    </div>
                </div>

                <form action="{{ route('transaksi.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="judul" class="form-label fw-semibold">Judul</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-pencil"></i></span>
                            <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required maxlength="255"
                                class="form-control" placeholder="Contoh: Gaji Bulanan">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tipe" class="form-label fw-semibold">Tipe Transaksi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-arrow-left-right"></i></span>
                            <select id="tipe" name="tipe" required class="form-select">
                                <option value="" disabled {{ old('tipe') ? '' : 'selected' }}>Pilih tipe</option>
                                <option value="pemasukan" {{ old('tipe') === 'pemasukan' ? 'selected' : '' }}>Pemasukan
                                </option>
                                <option value="pengeluaran" {{ old('tipe') === 'pengeluaran' ? 'selected' : '' }}>
                                    Pengeluaran</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold">Kategori</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-tag"></i></span>
                            <select id="category_id" name="category_id" required class="form-select">
                                <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>Pilih kategori
                                </option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" data-warna="{{ $cat->warna }}"
                                        data-saldo="{{ $cat->saldo }}"
                                        {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label fw-semibold">Jumlah (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-cash"></i></span>
                            <input type="number" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" required
                                min="1" max="999999999999" class="form-control" placeholder="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal" class="form-label fw-semibold">Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                            <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                                required max="{{ date('Y-m-d') }}" class="form-control">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold">
                            Keterangan <span class="text-muted fw-normal">(opsional)</span>
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="3" maxlength="1000" class="form-control" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> Simpan
                        </button>
                        <a href="{{ route('transaksi.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scanInput = document.getElementById('struk_image');
            const scanStatus = document.getElementById('scanStrukStatus');
            const scanSpinner = document.getElementById('scanStrukSpinner');
            const scanStatusText = document.getElementById('scanStrukStatusText');
            const scanPreviewWrap = document.getElementById('scanStrukPreviewWrap');
            const scanPreviewImg = document.getElementById('scanStrukPreviewImg');
            const tipeSelect = document.getElementById('tipe');
            const judulInput = document.getElementById('judul');
            const jumlahInput = document.getElementById('jumlah');
            const keteranganInput = document.getElementById('keterangan');

            let scanObjectUrl = null;
            let currentAbortController = null;

            const setStatus = (text, kind) => {
                if (!scanStatus) return;
                scanStatus.classList.remove('d-none', 'text-muted', 'text-success', 'text-danger');
                if (kind === 'success') scanStatus.classList.add('text-success');
                else if (kind === 'error') scanStatus.classList.add('text-danger');
                else scanStatus.classList.add('text-muted');

                const isLoading = kind === 'loading';
                if (scanSpinner) scanSpinner.classList.toggle('d-none', !isLoading);
                if (scanStatusText) scanStatusText.textContent = text;
                else scanStatus.textContent = text;
            };

            const resetScanUi = () => {
                if (scanObjectUrl) {
                    URL.revokeObjectURL(scanObjectUrl);
                    scanObjectUrl = null;
                }
                if (scanPreviewImg) scanPreviewImg.removeAttribute('src');
                if (scanPreviewWrap) scanPreviewWrap.classList.add('d-none');
                if (scanStatus) {
                    if (scanStatusText) scanStatusText.textContent = '';
                    else scanStatus.textContent = '';
                    scanStatus.classList.add('d-none');
                }
                if (scanSpinner) scanSpinner.classList.add('d-none');
            };

            if (scanInput) {
                scanInput.addEventListener('change', async function() {
                    const file = scanInput.files && scanInput.files[0] ? scanInput.files[0] : null;
                    if (!file) {
                        resetScanUi();
                        return;
                    }

                    if (!file.type || !file.type.startsWith('image/')) {
                        resetScanUi();
                        setStatus('File harus berupa gambar.', 'error');
                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        resetScanUi();
                        setStatus('Ukuran gambar maksimal 5MB.', 'error');
                        return;
                    }

                    if (currentAbortController) {
                        currentAbortController.abort();
                    }
                    currentAbortController = new AbortController();

                    if (scanObjectUrl) {
                        URL.revokeObjectURL(scanObjectUrl);
                    }
                    scanObjectUrl = URL.createObjectURL(file);
                    if (scanPreviewImg) scanPreviewImg.src = scanObjectUrl;
                    if (scanPreviewWrap) scanPreviewWrap.classList.remove('d-none');

                    setStatus('Sedang membaca struk dengan AI...', 'loading');

                    const formData = new FormData();
                    formData.append('image', file);

                    try {
                        const res = await fetch("{{ route('transaksi.scanStruk') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Accept': 'application/json'
                            },
                            body: formData,
                            signal: currentAbortController.signal,
                        });

                        const data = await res.json().catch(() => null);
                        if (!res.ok || !data) {
                            const firstError = (payload) => {
                                const errors = payload && payload.errors ? payload.errors : null;
                                if (!errors || typeof errors !== 'object') return null;
                                const keys = Object.keys(errors);
                                for (const key of keys) {
                                    const arr = errors[key];
                                    if (Array.isArray(arr) && arr.length > 0) return String(arr[0]);
                                }
                                return null;
                            };

                            const msg = (data && data.message ? data.message : null) || firstError(data) ||
                                (res.status === 413 ? 'Ukuran upload terlalu besar. Coba gunakan gambar lebih kecil.' : null) ||
                                'Gagal membaca struk.';
                            setStatus(msg, 'error');
                            return;
                        }

                        if (tipeSelect && data.tipe) {
                            tipeSelect.value = data.tipe;
                        }
                        if (jumlahInput && typeof data.jumlah === 'number') {
                            jumlahInput.value = data.jumlah;
                        }
                        if (keteranganInput && typeof data.keterangan === 'string') {
                            if (!keteranganInput.value || keteranganInput.value.trim() === '') {
                                keteranganInput.value = data.keterangan;
                            }
                        }
                        if (judulInput && typeof data.judul === 'string') {
                            if (!judulInput.value || judulInput.value.trim() === '') {
                                judulInput.value = data.judul;
                            }
                        }

                        setStatus('Struk terbaca. Form sudah terisi otomatis.', 'success');
                    } catch (e) {
                        if (e && e.name === 'AbortError') {
                            return;
                        }
                        setStatus('Gagal membaca struk. Coba lagi.', 'error');
                    }
                });
            }

            // Anggaran pengeluaran dihapus: tidak ada auto-fill jumlah berdasarkan kategori.
        });
    </script>
@endsection
