@extends('layouts.app')

@section('title', 'Edit Profile - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar')

    <div class="container py-4" style="max-width: 600px;">
        <h4 class="fw-bold mb-1">Edit Profile</h4>
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

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-camera me-2 text-success"></i> Foto Profil
                </h6>
                <div class="d-flex align-items-center gap-3">
                    @if ($user->foto)
                        <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto"
                            class="rounded-circle border border-2 border-success"
                            style="width: 70px; height: 70px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center border border-2 border-success"
                            style="width: 70px; height: 70px;">
                            <i class="bi bi-person-fill text-success fs-2"></i>
                        </div>
                    @endif
                    <form action="{{ route('profile.foto') }}" method="POST" enctype="multipart/form-data"
                        class="flex-grow-1">
                        @csrf
                        <input type="file" name="foto" accept="image/*" class="form-control form-control-sm mb-2"
                            required>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-upload me-1"></i> Upload Foto
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-person-gear me-2 text-success"></i> Informasi Profil
                </h6>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label for="nama" class="form-label small fw-semibold">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required
                            class="form-control" placeholder="Nama lengkap">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            required class="form-control" placeholder="Email">
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label small fw-semibold">
                            Bio <span class="text-muted fw-normal">(opsional)</span>
                        </label>
                        <textarea id="bio" name="bio" rows="3" class="form-control"
                            placeholder="Ceritakan sedikit tentang dirimu...">{{ old('bio', $user->bio) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <a href="{{ route('profile') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection
