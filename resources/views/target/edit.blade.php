@extends('layouts.app')

@section('title', 'Edit Target - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar')

    <div class="container py-4" style="max-width: 600px;">
        <h4 class="fw-bold mb-1">Edit Target</h4>
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
                <form action="{{ route('target.update', $target) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label for="nama_target" class="form-label fw-semibold">Nama Target</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-bullseye"></i></span>
                            <input type="text" id="nama_target" name="nama_target"
                                value="{{ old('nama_target', $target->nama_target) }}" required class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="target_nominal" class="form-label fw-semibold">Target Nominal (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-cash-stack"></i></span>
                            <input type="number" id="target_nominal" name="target_nominal"
                                value="{{ old('target_nominal', $target->target_nominal) }}" required min="1"
                                class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="terkumpul" class="form-label fw-semibold">Jumlah Terkumpul (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-wallet2"></i></span>
                            <input type="number" id="terkumpul" name="terkumpul"
                                value="{{ old('terkumpul', $target->terkumpul) }}" min="0" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_target" class="form-label fw-semibold">
                            Deadline <span class="text-muted fw-normal">(opsional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-calendar"></i></span>
                            <input type="date" id="tanggal_target" name="tanggal_target"
                                value="{{ old('tanggal_target', $target->tanggal_target?->format('Y-m-d')) }}"
                                class="form-control">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="foto" class="form-label fw-semibold">
                            Foto Target <span class="text-muted fw-normal">(opsional)</span>
                        </label>
                        @if ($target->foto)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $target->foto) }}" alt="{{ $target->nama_target }}"
                                    class="rounded" style="height: 100px; object-fit: cover;">
                            </div>
                        @endif
                        <input type="file" id="foto" name="foto" accept="image/*" class="form-control">
                        <div class="form-text">Kosongkan jika tidak ingin mengubah foto. Maks 2MB.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> Perbarui
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
