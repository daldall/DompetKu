@extends('layouts.app')

@section('title', 'Buat Target - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar')

    <div class="container py-4" style="max-width: 600px;">
        <h4 class="fw-bold mb-1">Buat Target Baru</h4>
        <hr class="text-success mb-4" style="border-width: 3px; width: 60px;">

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
                <form action="{{ route('target.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="nama_target" class="form-label fw-semibold">Nama Target</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-bullseye"></i></span>
                            <input type="text" id="nama_target" name="nama_target" value="{{ old('nama_target') }}"
                                required class="form-control" placeholder="Contoh: Beli Laptop Baru">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="target_nominal" class="form-label fw-semibold">Target Nominal (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-cash-stack"></i></span>
                            <input type="number" id="target_nominal" name="target_nominal"
                                value="{{ old('target_nominal') }}" required min="1" class="form-control"
                                placeholder="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="terkumpul" class="form-label fw-semibold">
                            Jumlah Terkumpul (Rp) <span class="text-muted fw-normal">(opsional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-wallet2"></i></span>
                            <input type="number" id="terkumpul" name="terkumpul" value="{{ old('terkumpul', 0) }}"
                                min="0" class="form-control" placeholder="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_target" class="form-label fw-semibold">
                            Deadline <span class="text-muted fw-normal">(opsional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                            <input type="date" id="tanggal_target" name="tanggal_target"
                                value="{{ old('tanggal_target') }}" class="form-control">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="foto" class="form-label fw-semibold">
                            Foto Target <span class="text-muted fw-normal">(opsional)</span>
                        </label>
                        <input type="file" id="foto" name="foto" accept="image/*" class="form-control">
                        <div class="form-text">Format: JPG, PNG, WebP. Maks 2MB.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> Simpan
                        </button>
                        <a href="{{ route('target.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
