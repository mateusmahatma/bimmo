@extends('layouts.main')

@section('container')
<div class="thread-detail-page mb-5">
    <div class="pagetitle mb-4">
        <h1 class="fw-bold mb-1">{{ __('Thread Detail') }}</h1>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('threads.index') }}">{{ __('Threads') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Thread Detail') }}</li>
            </ol>
        </nav>
    </div>

    {{-- Flash Messages --}}
{{-- Standarized with Toast --}}


    {{-- Main Thread --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex align-items-start gap-3 mb-3">
                <div class="thread-avatar">
                    @if($thread->user->profile_photo)
                        <img src="{{ route('storage.profile_photo', ['filename' => basename($thread->user->profile_photo)]) }}"
                            class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;" alt="Avatar">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                            style="width: 45px; height: 45px; font-size: 1.1rem; background: var(--bs-primary);">
                            {{ strtoupper(substr($thread->user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-body">{{ $thread->user->name }}</h6>
                    <small class="text-muted">{{ $thread->created_at->diffForHumans() }}</small>
                </div>
                
                @if(Auth::id() === $thread->id_user)
                    <div class="ms-auto">
                        <form action="{{ route('threads.destroy', $thread->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus thread ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link text-danger p-0" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <h4 class="fw-bold mb-3">{{ $thread->title }}</h4>
            <div class="thread-content text-body" style="white-space: pre-wrap; line-height: 1.6;">{!! nl2br(e($thread->body)) !!}</div>
            
            <hr class="my-4 opacity-50">
            
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-chat-dots text-primary"></i>
                <span class="fw-semibold">{{ $thread->comments->count() }} {{ __('Komentar') }}</span>
            </div>
        </div>
    </div>

    {{-- Comments List --}}
    <div class="comments-section">
        <h6 class="fw-bold mb-3 px-1">{{ __('Komentar') }}</h6>
        
        @if($thread->comments->isEmpty())
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body py-4 text-center text-muted">
                    <p class="mb-0 small">{{ __('Belum ada komentar. Berikan tanggapan pertama!') }}</p>
                </div>
            </div>
        @else
            <div class="d-flex flex-column gap-3 mb-4">
                @foreach($thread->comments as $comment)
                    <div class="card border-0 shadow-sm rounded-4 comment-card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <div class="comment-avatar">
                                    @if($comment->user->profile_photo)
                                        <img src="{{ route('storage.profile_photo', ['filename' => basename($comment->user->profile_photo)]) }}"
                                            class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;" alt="Avatar">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                            style="width: 30px; height: 30px; font-size: 0.8rem; background: var(--bs-secondary);">
                                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-semibold small">{{ $comment->user->name }}</span>
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $comment->created_at->diffForHumans() }}</span>
                                        @if(Auth::id() === $comment->id_user)
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 0.6rem;">{{ __('Anda') }}</span>
                                        @endif
                                    </div>
                                    <div class="comment-body small mt-1 text-body" style="white-space: pre-wrap;">{{ $comment->body }}</div>
                                </div>
                                
                                @if(Auth::id() === $comment->id_user)
                                    <form action="{{ route('threads.comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Hapus komentar ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0" title="Hapus">
                                            <i class="bi bi-trash small"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Comment Form --}}
    <div class="comment-form-container sticky-bottom-mobile mt-4">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-body p-3">
                <form action="{{ route('threads.comments.store', $thread->id) }}" method="POST" class="no-loader">
                    @csrf
                    <div class="input-group">
                        <textarea name="body" class="form-control border-0 rounded-3 shadow-none @error('body') is-invalid @enderror" 
                                  placeholder="{{ __('Tulis komentar...') }}" rows="1" style="resize: none;" required></textarea>
                        <button class="btn btn-primary rounded-pill ms-2 px-3 d-flex align-items-center justify-content-center" type="submit" style="width: 40px; height: 40px;">
                            <i class="bi bi-send-fill"></i>
                        </button>
                        @error('body')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
.thread-detail-page {
    max-width: 800px;
    margin: 0 auto;
}
.thread-content {
    font-size: 1.05rem;
}
.comment-card {
    background-color: var(--bs-tertiary-bg);
}

@media (max-width: 576px) {
    .sticky-bottom-mobile {
        position: fixed;
        bottom: 60px;
        left: 0;
        right: 0;
        z-index: 1030;
        padding: 10px 15px;
        background: linear-gradient(transparent, var(--bs-body-bg) 30%);
    }
    .thread-detail-page {
        padding-bottom: 80px;
    }
    .comment-card .card-body {
        padding: 0.75rem !important;
    }
}
</style>
@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.querySelector('.comment-form-container textarea');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            if (this.scrollHeight > 150) {
                this.style.overflowY = 'scroll';
                this.style.height = '150px';
            } else {
                this.style.overflowY = 'hidden';
            }
        });
    }
});
</script>
@endsection
