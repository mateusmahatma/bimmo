@extends('layouts.main')

@section('title', __('Budget Period'))

@push('css')
<link href="{{ asset('css/anggaran.css') }}?v={{ filemtime(public_path('css/anggaran.css')) }}" rel="stylesheet">
@endpush

@section('container')

<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('Budget Period') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Budget Period') }}</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-dashboard border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark" style="font-size: 1.1rem; letter-spacing: -0.01em;">{{ __('Daftar Periode Anggaran') }}</h5>
                        <p class="text-muted small mb-0 mt-1">{{ __('Create a period to be used during the budget monitoring process.') }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#periodeAnggaranModal" style="padding: 2px 10px; font-size: 0.75rem;">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('Add Budget Period Data') }}
                        </button>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-secondary small text-uppercase fw-bold py-3">{{ __('Name Period') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">{{ __('Start Date') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center">{{ __('Completion Date') }}</th>
                                    <th class="text-secondary small text-uppercase fw-bold py-3 text-center" style="width: 10%;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($periods as $p)
                                <tr>
                                    <td class="fw-bold">
                                        <a class="text-decoration-none" href="{{ route('anggaran.detail', $p->id_periode_anggaran) }}">
                                            {{ $p->nama_periode }}
                                        </a>
                                    </td>
                                    <td class="text-center text-muted small">{{ optional($p->tanggal_mulai)->format('Y-m-d') }}</td>
                                    <td class="text-center text-muted small">{{ optional($p->tanggal_selesai)->format('Y-m-d') }}</td>
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('anggaran.destroy', $p->id_periode_anggaran) }}" onsubmit="return confirm('Hapus periode ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" style="padding: 2px 10px; font-size: 0.75rem;">
                                                <i class="bi bi-trash me-1"></i> {{ __('Hapus') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-calendar2-x fs-1 opacity-25"></i>
                                            <p class="mt-2 mb-0">{{ __('There is no budget period yet.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($periods->hasPages())
                    <div class="mt-3">
                        {!! $periods->links('pagination::bootstrap-5') !!}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal: Tambah Periode Anggaran -->
<div class="modal fade" id="periodeAnggaranModal" tabindex="-1" aria-labelledby="periodeAnggaranModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('anggaran.store') }}" autocomplete="off">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="periodeAnggaranModalLabel">{{ __('Tambah Periode Anggaran') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4">
                    <div class="mb-3">
                        <label class="form-label fw-medium small text-uppercase text-muted required" for="nama_periode">{{ __('Nama Periode') }}</label>
                        <input type="text" id="nama_periode" class="form-control" name="nama_periode" placeholder="{{ __('Contoh: April 2026') }}" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-uppercase text-muted required" for="tanggal_mulai">{{ __('Tanggal Mulai') }}</label>
                            <input type="date" id="tanggal_mulai" class="form-control" name="tanggal_mulai" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium small text-uppercase text-muted required" for="tanggal_selesai">{{ __('Tanggal Selesai') }}</label>
                            <input type="date" id="tanggal_selesai" class="form-control" name="tanggal_selesai" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-primary px-4">{{ __('Simpan') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection