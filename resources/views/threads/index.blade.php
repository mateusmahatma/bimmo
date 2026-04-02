@extends('layouts.main')

@section('title', __('Threads'))

@section('container')
<div class="threads-page">

    {{-- Page Header --}}
    {{-- <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-chat-left-text-fill me-2 text-primary"></i>{{ __('Threads') }}</h4>
            <small class="text-muted">{{ __('Diskusi bersama pengguna lain') }}</small>
        </div>
        <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#formBuatThread" aria-expanded="false">
            <i class="bi bi-plus-lg me-1"></i>{{ __('Buat Thread') }}
        </button>
    </div> --}}

    <div class="pagetitle mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="fw-bold mb-1">{{ __('Threads') }}</h1>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Threads') }}</li>
                    </ol>
                </nav>
            </div>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addThreadModal">
                <i class="bi bi-chat-left-text me-1"></i>{{ __('Buat Thread') }}
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 py-2 mb-3" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Add Thread Modal -->
    <div class="modal fade" id="addThreadModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>{{ __('Thread Baru') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('threads.store') }}" method="POST" class="no-loader">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold small">{{ __('Judul Thread') }}</label>
                            <input type="text" name="title" id="title"
                                class="form-control rounded-3 @error('title') is-invalid @enderror"
                                placeholder="{{ __('Tulis judul thread yang menarik...') }}"
                                value="{{ old('title') }}" maxlength="255" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="body" class="form-label fw-semibold small">{{ __('Isi Thread') }}</label>
                            <textarea name="body" id="body" rows="4"
                                class="form-control rounded-3 @error('body') is-invalid @enderror"
                                placeholder="{{ __('Ceritakan sesuatu, tanya, atau berbagi tips keuangan...') }}"
                                maxlength="5000" required>{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-send me-1"></i>{{ __('Kirim') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Thread List --}}
    @if($threads->isEmpty())
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="bi bi-chat-square-dots" style="font-size: 4rem; color: var(--bs-secondary-color); opacity: 0.4;"></i>
            </div>
            <h6 class="text-muted">{{ __('Belum ada thread.') }}</h6>
            <p class="text-muted small">{{ __('Jadilah yang pertama memulai diskusi!') }}</p>
            <button class="btn btn-primary btn-sm rounded-pill px-4 mt-1"
                data-bs-toggle="modal" data-bs-target="#addThreadModal">
                <i class="bi bi-plus-lg me-1"></i>{{ __('Buat Thread Pertama') }}
            </button>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($threads as $thread)
            <a href="{{ route('threads.show', $thread->id) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 thread-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start gap-3">
                            {{-- Avatar --}}
                            <div class="thread-avatar flex-shrink-0">
                                @if($thread->user->profile_photo)
                                    <img src="{{ route('storage.profile_photo', ['filename' => basename($thread->user->profile_photo)]) }}"
                                        class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;" alt="Avatar">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                        style="width: 40px; height: 40px; font-size: 1rem; background: var(--bs-primary);">
                                        {{ strtoupper(substr($thread->user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <span class="fw-semibold small text-body">{{ $thread->user->name }}</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-clock me-1"></i>{{ $thread->created_at->diffForHumans() }}
                                    </span>
                                    @if(Auth::id() === $thread->id_user)
                                        <span class="badge bg-primary-subtle text-primary rounded-pill" style="font-size: 0.65rem;">{{ __('Anda') }}</span>
                                    @endif
                                </div>
                                <h6 class="fw-bold mb-1 text-body thread-title">{{ $thread->title }}</h6>
                                <p class="text-muted mb-2 small thread-body-preview">{{ Str::limit($thread->body, 120) }}</p>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-muted small">
                                        <i class="bi bi-chat-dots me-1"></i>
                                        {{ $thread->comments->count() }} {{ __('komentar') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $threads->links() }}
        </div>
    @endif
</div>

@push('css')
<style>
.thread-card {
    transition: box-shadow 0.2s ease, transform 0.15s ease;
    cursor: pointer;
}
.thread-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.12) !important;
    transform: translateY(-1px);
}
.thread-title {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.thread-body-preview {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
@media (max-width: 576px) {
    .thread-avatar .rounded-circle,
    .thread-avatar div.rounded-circle {
        width: 34px !important;
        height: 34px !important;
        font-size: 0.85rem !important;
    }
    .threads-page h4 {
        font-size: 1.1rem;
    }
}
</style>
@endpush
@endsection
