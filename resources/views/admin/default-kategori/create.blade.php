@extends('layouts.app')

@section('title', 'Admin - Tambah Kategori Default - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar-admin')

    <div class="container py-4" style="max-width: 650px;">
        <h4 class="fw-bold mb-1">Tambah Kategori Default</h4>
        <hr class="text-success mb-4" style="border-width: 3px; width: 80px;">

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
                <form action="{{ route('admin.default-kategori.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-tag"></i></span>
                            <input type="text" id="nama_kategori" name="nama_kategori" value="{{ old('nama_kategori') }}" required
                                class="form-control" placeholder="Contoh: Makan, Gaji, Transport">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Ikon</label>
                        <input type="hidden" name="ikon" id="ikon" value="{{ old('ikon', 'bi-tag-fill') }}" required>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach (['bi-cart-fill', 'bi-bag-fill', 'bi-basket-fill', 'bi-shop', 'bi-cup-hot-fill', 'bi-fuel-pump-fill', 'bi-bus-front-fill', 'bi-house-door-fill', 'bi-briefcase-fill', 'bi-cash-stack', 'bi-wallet-fill', 'bi-credit-card-fill', 'bi-piggy-bank-fill', 'bi-tag-fill'] as $icon)
                                <button type="button"
                                    class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center ikon-pilih {{ old('ikon', 'bi-tag-fill') === $icon ? 'btn-success text-white border-success' : '' }}"
                                    data-ikon="{{ $icon }}" style="width: 42px; height: 42px;">
                                    <i class="bi {{ $icon }} fs-5"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Kategori</label>
                        <input type="hidden" name="warna" id="warna" value="{{ old('warna', 'success') }}" required>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <button type="button" data-warna="success"
                                    class="w-100 text-start btn bg-white border rounded-3 shadow-sm warna-pilih {{ old('warna', 'success') === 'success' ? 'border-success ring-selected' : '' }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 42px; height: 42px; background-color: #19875410;">
                                            <i class="bi bi-arrow-down-circle-fill text-success fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Pemasukan</div>
                                            <small class="text-muted">Uang yang masuk (contoh: gajian)</small>
                                        </div>
                                        <i class="bi bi-check-circle-fill text-success ms-auto {{ old('warna', 'success') === 'success' ? '' : 'd-none' }}"></i>
                                    </div>
                                </button>
                            </div>
                            <div class="col-12 col-md-6">
                                <button type="button" data-warna="danger"
                                    class="w-100 text-start btn bg-white border rounded-3 shadow-sm warna-pilih {{ old('warna', 'success') === 'danger' ? 'border-danger ring-selected' : '' }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 42px; height: 42px; background-color: #dc354510;">
                                            <i class="bi bi-arrow-up-circle-fill text-danger fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">Pengeluaran</div>
                                            <small class="text-muted">Uang yang keluar (contoh: belanja)</small>
                                        </div>
                                        <i class="bi bi-check-circle-fill text-danger ms-auto {{ old('warna', 'success') === 'danger' ? '' : 'd-none' }}"></i>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> Simpan
                        </button>
                        <a href="{{ route('admin.default-kategori.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.ikon-pilih').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.ikon-pilih').forEach(b => {
                    b.classList.remove('btn-success', 'text-white', 'border-success');
                    b.classList.add('btn-outline-secondary');
                });
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-success', 'text-white', 'border-success');
                document.getElementById('ikon').value = btn.dataset.ikon;
            });
        });

        document.querySelectorAll('.warna-pilih').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.warna-pilih').forEach(b => {
                    b.classList.remove('border-success', 'border-danger', 'ring-selected');
                    const checkIcon = b.querySelector('.bi-check-circle-fill');
                    if (checkIcon) checkIcon.classList.add('d-none');
                });

                const warna = btn.dataset.warna;
                if (warna === 'success') {
                    btn.classList.add('border-success', 'ring-selected');
                } else {
                    btn.classList.add('border-danger', 'ring-selected');
                }

                const checkIcon = btn.querySelector('.bi-check-circle-fill');
                if (checkIcon) checkIcon.classList.remove('d-none');

                document.getElementById('warna').value = warna;
            });
        });
    </script>
@endsection
