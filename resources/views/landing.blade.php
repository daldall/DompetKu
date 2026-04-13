@extends('layouts.app')
@section('title', 'DompetKu - Kelola Keuangan Anda dengan Mudah')
@section('body-class', 'd-flex flex-column min-vh-100')

@section('content')
    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center text-success" href="/">
                <img src="{{ asset('logo.png') }}" alt="DompetKu" height="28" class="me-2">
                <span>DompetKu</span>
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('login') }}" class="btn btn-outline-success px-3">Login</a>
                <a href="{{ route('register') }}" class="btn btn-success px-3">Register</a>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="bg-light position-relative overflow-hidden">
        {{-- Background Icons --}}
        <div class="position-absolute w-100 h-100 top-0 start-0 pointer-events-none opacity-25" style="z-index: 0;">
            <i class="bi bi-wallet2 position-absolute text-success" style="font-size: 3rem; top: 10%; left: 5%; transform: rotate(-15deg);"></i>
            <i class="bi bi-piggy-bank position-absolute text-success" style="font-size: 4rem; top: 70%; left: 8%; transform: rotate(15deg);"></i>
            <i class="bi bi-coin position-absolute text-warning" style="font-size: 2.5rem; top: 25%; left: 45%; transform: rotate(-10deg);"></i>
            <i class="bi bi-cash-stack position-absolute text-success" style="font-size: 3.5rem; bottom: 15%; left: 35%; transform: rotate(10deg);"></i>
            <i class="bi bi-bank position-absolute text-success" style="font-size: 4rem; top: 15%; right: 10%; transform: rotate(10deg);"></i>
            <i class="bi bi-safe position-absolute text-secondary" style="font-size: 3.5rem; bottom: 20%; right: 8%; transform: rotate(-15deg);"></i>
            <i class="bi bi-credit-card position-absolute text-primary" style="font-size: 3rem; top: 60%; right: 40%; transform: rotate(-5deg);"></i>
            <i class="bi bi-currency-dollar position-absolute text-success opacity-50" style="font-size: 2rem; top: 40%; left: 20%; transform: rotate(20deg);"></i>
            <i class="bi bi-graph-up-arrow position-absolute text-success opacity-50" style="font-size: 2rem; top: 80%; right: 25%; transform: rotate(-15deg);"></i>
        </div>
        
        <div class="container py-5 position-relative" style="z-index: 1;">
            <div class="row align-items-center gy-4 py-4">
                <div class="col-lg-6">
                    <span class="badge bg-success bg-opacity-10 text-success fw-semibold px-3 py-2 mb-3 d-inline-block">
                        <i class="bi bi-stars me-1"></i> Website Keuangan Pribadi
                    </span>
                    <h1 class="display-4 fw-bold text-dark mb-3 lh-sm">
                        Kelola Keuangan<br>dengan <span class="text-success">Lebih Cerdas</span>
                    </h1>
                    <p class="text-secondary fs-5 mb-4 col-lg-10 pe-lg-4">
                        Catat pemasukan & pengeluaran, kelola kategori, dan pantau keuanganmu — semua dalam satu tempat.
                    </p>
                    <div class="d-flex gap-3 flex-wrap mb-4">
                        <a href="{{ route('register') }}" class="btn btn-success btn-lg px-4 shadow-sm">
                            <i class="bi bi-rocket-takeoff me-2"></i>Mulai Gratis
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-dark btn-lg px-4">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login
                        </a>
                    </div>
                    <div class="d-flex gap-4 pt-2">
                        <div>
                            <h5 class="fw-bold text-success mb-0">100%</h5>
                            <small class="text-muted">Gratis</small>
                        </div>
                        <div>
                            <h5 class="fw-bold text-success mb-0">Mudah</h5>
                            <small class="text-muted">Digunakan</small>
                        </div>
                        <div>
                            <h5 class="fw-bold text-success mb-0">Aman</h5>
                            <small class="text-muted">& Privat</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center d-none d-lg-block">
                    <img src="{{ asset('heroimage.jpeg') }}" alt="DompetKu" class="img-fluid rounded-4 shadow-lg w-100">
                </div>
            </div>
        </div>
    </section>

    {{-- Fitur --}}
    <section class="bg-white py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span
                    class="badge bg-success bg-opacity-10 text-success fw-semibold px-3 py-2 mb-3 d-inline-block">Fitur</span>
                <h2 class="fw-bold mb-2">Semua yang Kamu Butuhkan</h2>
                <p class="text-muted col-lg-6 mx-auto">Fitur lengkap untuk membantu mengelola keuangan pribadimu sehari-hari
                </p>
            </div>
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center">
                        <div
                            class="bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 p-3">
                            <i class="bi bi-arrow-left-right text-success fs-3"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Catat Transaksi</h6>
                        <p class="text-muted small mb-0">Input pemasukan & pengeluaran dengan cepat setiap hari.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center">
                        <div
                            class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 p-3">
                            <i class="bi bi-tags text-primary fs-3"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Kelola Kategori</h6>
                        <p class="text-muted small mb-0">Buat kategori sendiri untuk mengorganisir transaksi.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center">
                        <div
                            class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 p-3">
                            <i class="bi bi-graph-up-arrow text-warning fs-3"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Lihat Ringkasan</h6>
                        <p class="text-muted small mb-0">Dashboard ringkas untuk melihat saldo & statistik keuangan.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center">
                        <div
                            class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 p-3">
                            <i class="bi bi-bullseye  text-danger fs-3"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Target</h6>
                        <p class="text-muted small mb-0">Tentukan target uang untuk membeli barang atau mencapai kebutuhan
                            tertentu.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Cara Kerja --}}
    <section class="bg-light py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="badge bg-success bg-opacity-10 text-success fw-semibold px-3 py-2 mb-3 d-inline-block">Cara
                    Kerja</span>
                <h2 class="fw-bold mb-2">3 Langkah Mudah</h2>
                <p class="text-muted">Mulai kelola keuanganmu dalam hitungan menit</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="text-center">
                        <div
                            class="bg-success text-white fw-bold fs-4 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 p-3">
                            1
                        </div>
                        <h6 class="fw-bold mb-2">Daftar Akun</h6>
                        <p class="text-muted small">Buat akun gratis hanya dengan nama, email, dan password.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div
                            class="bg-success text-white fw-bold fs-4 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 p-3">
                            2
                        </div>
                        <h6 class="fw-bold mb-2">Buat Kategori</h6>
                        <p class="text-muted small">Tambahkan kategori seperti Makan, Transport, Gaji, dan lainnya.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div
                            class="bg-success text-white fw-bold fs-4 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 p-3">
                            3
                        </div>
                        <h6 class="fw-bold mb-2">Catat & Pantau</h6>
                        <p class="text-muted small">Mulai catat transaksi dan lihat ringkasan di dashboard.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="bg-white py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span
                    class="badge bg-success bg-opacity-10 text-success fw-semibold px-3 py-2 mb-3 d-inline-block">FAQ</span>
                <h2 class="fw-bold mb-2">Pertanyaan yang Sering Diajukan</h2>
                <p class="text-muted">Jawaban atas pertanyaan umum seputar DompetKu</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="false">
                                    <i class="bi bi-question-circle text-success me-2"></i> Apa itu DompetKu?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    DompetKu adalah aplikasi pencatatan keuangan berbasis web yang membantu pengguna
                                    mencatat pemasukan dan pengeluaran serta memantau kondisi keuangan secara mudah dan
                                    rapi.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false">
                                    <i class="bi bi-question-circle text-success me-2"></i> Apakah DompetKu gratis
                                    digunakan?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Ya, DompetKu dapat digunakan secara gratis untuk membantu pengguna mengelola keuangan
                                    pribadi tanpa biaya apapun.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false">
                                    <i class="bi bi-question-circle text-success me-2"></i> Apa saja fitur yang tersedia di
                                    DompetKu?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Fitur utama DompetKu meliputi pencatatan transaksi pemasukan dan pengeluaran, manajemen
                                    kategori transaksi, dashboard ringkasan keuangan,
                                    fitur target keuangan untuk membantu pengguna mencapai tujuan tertentu, serta riwayat
                                    transaksi untuk melihat catatan keuangan sebelumnya.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0 border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false">
                                    <i class="bi bi-question-circle text-success me-2"></i> Apakah data keuangan saya aman?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Data pengguna hanya dapat diakses setelah login, sehingga informasi keuangan tetap aman
                                    dan bersifat pribadi. Setiap pengguna hanya bisa melihat data miliknya sendiri.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false">
                                    <i class="bi bi-question-circle text-success me-2"></i> Bagaimana cara mulai
                                    menggunakan DompetKu?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted">
                                    Pengguna cukup membuat akun melalui halaman Register, kemudian login dan langsung bisa
                                    mulai membuat kategori serta mencatat transaksi keuangan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="bg-success py-5">
        <div class="container py-4 text-center">
            <h2 class="fw-bold text-white mb-3">Siap Mengelola Keuanganmu?</h2>
            <p class="text-white-50 mb-4 col-lg-5 mx-auto">
                Bergabung sekarang — gratis, tanpa iklan, dan langsung bisa digunakan.
            </p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 fw-semibold text-success shadow">
                <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
            </a>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="py-4 border-top bg-white mt-auto">
        <div class="container text-center">
            <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                <img src="{{ asset('logo.png') }}" alt="DompetKu" height="20">
                <span class="fw-semibold text-success small">DompetKu</span>
            </div>
            <p class="text-muted small mb-0">&copy; {{ date('Y') }} DompetKu. Kelola keuanganmu dengan mudah.</p>
        </div>
    </footer>
@endsection
