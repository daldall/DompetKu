@extends('layouts.app')

@section('title', 'Profile - DompetKu')
@section('body-class', 'd-flex flex-column min-vh-100 bg-light')

@section('content')
    @include('includes.navbar')

    <div class="container py-4" style="max-width: 600px;">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h4 class="fw-bold mb-0">Profile</h4>
            <a href="{{ route('profile.edit') }}" class="btn btn-success btn-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit Profile
            </a>
        </div>
        <hr class="text-success mb-4" style="border-width: 3px; width: 60px;">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                <small><i class="bi bi-check-circle me-1"></i> {{ session('success') }}</small>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-4 text-center">
                <div class="mb-3">
                    @if ($user->foto)
                        <img src="{{ $user->foto }}" alt="Foto Profil"
                            class="rounded-circle border border-3 border-success"
                            style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center border border-3 border-success"
                            style="width: 100px; height: 100px;">
                            <i class="bi bi-person-fill text-success" style="font-size: 2.5rem;"></i>
                        </div>
                    @endif
                </div>

                <h5 class="fw-bold mb-1">{{ $user->nama }}</h5>
                <p class="text-muted mb-0">
                    <i class="bi bi-envelope me-1"></i> {{ $user->email }}
                </p>

                @if ($user->bio)
                    <hr class="my-3">
                    <p class="text-muted small mb-0">{{ $user->bio }}</p>
                @endif
            </div>
        </div>
    </div>
@endsection
