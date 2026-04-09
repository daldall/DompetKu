@extends('layouts.app')
@section('title', 'Register - DompetKu')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <img src="{{ asset('logo.png') }}" alt="DompetKu" height="72" class="mb-2">
                    <h1 class="fw-bold text-success">DompetKu</h1>
                    <p class="text-muted">Buat akun baru</p>
                </div>

                <div class="card border-0 shadow rounded-4">
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                                <small>{{ session('success') }}</small>
                                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
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

                        <form action="{{ route('register') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="nama" class="form-label fw-semibold">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                                    <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                                        class="form-control" placeholder="Masukkan nama lengkap">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                        class="form-control" placeholder="contoh@email.com">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                                    <input type="password" id="password" name="password" required class="form-control"
                                        placeholder="Minimal 6 karakter">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi
                                    Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required
                                        class="form-control" placeholder="Ulangi password">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success w-100 fw-semibold py-2">
                                <i class="bi bi-person-plus me-1"></i> Daftar
                            </button>
                        </form>

                        <p class="text-center text-muted small mt-4 mb-0">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="text-success fw-semibold text-decoration-none">Login di
                                sini</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
