@extends('layouts.main')

@section('container')
<div class="pagetitle mb-4">
    <h1 class="fw-bold mb-1">{{ __('My Profile') }}</h1>
    <nav>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Profile') }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-12">


        <!-- Foto Profil -->
        <div class="card mb-4">
            <div class="card-header">
                {{ __('Profile Photo') }}
            </div>
            <div class="card-body">
                @if(session('photo_status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('photo_status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->updatePhoto->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->updatePhoto->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        @if(auth()->user()->profile_photo)
                            <img src="{{ route('storage.profile_photo', ['filename' => basename(auth()->user()->profile_photo)]) }}" alt="Profile Photo" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                                <i class="bi bi-person-fill text-white fs-1"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <form action="{{ route('profil.updatePhoto') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="profile_photo" class="form-label">{{ __('Select Photo') }}</label>
                                <input class="form-control" type="file" id="profile_photo" name="profile_photo" accept="image/*" required>
                                <div class="form-text">Maksimum 2MB (JPG, PNG, GIF, SVG).</div>
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('Upload New Photo') }}</button>
                        </form>
                        
                        @if(auth()->user()->profile_photo)
                            <form action="{{ route('profil.deletePhoto') }}" method="POST" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus foto profil?')">{{ __('Remove Photo') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Settings -->
        {{-- <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ __('Subscription Details') }}</span>
                @if(auth()->user()->isSubscribed())
                    <span class="badge bg-success">{{ __('Active') }}</span>
                @elseif(auth()->user()->isOnTrial())
                    <span class="badge bg-info">{{ __('Trial Period') }}</span>
                @else
                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                @endif
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="mb-1 text-muted small text-uppercase fw-bold">{{ __('Current Status') }}</p>
                    @if(auth()->user()->isSubscribed())
                        <p class="mb-0">{{ __('You are currently subscribed. Ends on:') }} <strong>{{ auth()->user()->subscription_ends_at->format('d M Y') }}</strong> ({{ auth()->user()->getRemainingDays() }} {{ __('days') }} {{ __('left') }})</p>
                    @elseif(auth()->user()->isOnTrial())
                        <p class="mb-0">{{ __('You are on a 7-day free trial. Ends on:') }} <strong>{{ auth()->user()->trial_ends_at->format('d M Y') }}</strong> ({{ auth()->user()->getRemainingDays() }} {{ __('days') }} {{ __('left') }})</p>
                    @else
                        <p class="mb-0 text-danger font-italic">{{ __('Your access has expired. Please subscribe to continue using all features.') }}</p>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    @if(!auth()->user()->isSubscribed())
                        <button id="pay-button" class="btn btn-primary">
                            <i class="bi bi-qr-code-scan me-1"></i> {{ __('Subscribe Now') }} (Rp 49.000)
                        </button>
                    @elseif(auth()->user()->subscription_auto_renew)
                        <form action="{{ route('subscription.cancel') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('{{ __('Are you sure you want to cancel your subscription?') }}')">
                                {{ __('Cancel Subscription') }}
                            </button>
                        </form>
                    @else
                        <button class="btn btn-secondary" disabled>{{ __('Canceled (Access until') }} {{ auth()->user()->subscription_ends_at->format('d M Y') }})</button>
                    @endif
                </div>
            </div>
        </div> --}}

        <!-- Pengaturan Tema -->
        <div class="card mb-4">
            <div class="card-header">
                {{ __('Theme Settings') }}
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">{{ __('Choose your preferred theme for the application.') }}</p>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-check theme-option p-3 border rounded cursor-pointer" onclick="document.getElementById('theme_light').click()">
                            <input class="form-check-input" type="radio" name="theme_preference" id="theme_light" value="light">
                            <label class="form-check-label d-flex align-items-center cursor-pointer" for="theme_light">
                                <i class="bi bi-sun fs-4 me-2 text-warning"></i>
                                <span>{{ __('Light Mode') }}</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check theme-option p-3 border rounded cursor-pointer" onclick="document.getElementById('theme_dark').click()">
                            <input class="form-check-input" type="radio" name="theme_preference" id="theme_dark" value="dark">
                            <label class="form-check-label d-flex align-items-center cursor-pointer" for="theme_dark">
                                <i class="bi bi-moon-stars fs-4 me-2 text-primary"></i>
                                <span>{{ __('Dark Mode') }}</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check theme-option p-3 border rounded cursor-pointer" onclick="document.getElementById('theme_auto').click()">
                            <input class="form-check-input" type="radio" name="theme_preference" id="theme_auto" value="auto">
                            <label class="form-check-label d-flex align-items-center cursor-pointer" for="theme_auto">
                                <i class="bi bi-circle-half fs-4 me-2 text-secondary"></i>
                                <span>{{ __('Auto (System)') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengaturan Gaya Visual -->
        {{-- <div class="card mb-4 shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-palette2 me-2 text-primary"></i>{{ __('Gaya Visual') }}</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <p class="text-muted small mb-4">{{ __('Pilih gaya desain yang paling sesuai dengan karakter Anda.') }}</p>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-check style-option p-4 border rounded-4 cursor-pointer transition-all" onclick="document.getElementById('style_corporate').click()" style="border-width: 2px !important;">
                            <input class="form-check-input d-none" type="radio" name="style_preference" id="style_corporate" value="corporate">
                            <label class="form-check-label d-block cursor-pointer" for="style_corporate">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-box bg-secondary bg-opacity-10 text-secondary rounded-3 p-2 me-3">
                                        <i class="bi bi-briefcase fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Corporate</h6>
                                </div>
                                <p class="small text-muted mb-0">Tampilan profesional, bersih, dan terstruktur. Cocok untuk fokus maksimal.</p>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check style-option p-4 border rounded-4 cursor-pointer transition-all" onclick="document.getElementById('style_milenial').click()" style="border-width: 2px !important;">
                            <input class="form-check-input d-none" type="radio" name="style_preference" id="style_milenial" value="milenial">
                            <label class="form-check-label d-block cursor-pointer" for="style_milenial">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                                        <i class="bi bi-stars fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">Millennial & Gen Z</h6>
                                </div>
                                <p class="small text-muted mb-0">Estetika modern dengan gradasi, glassmorphism, dan visual dinamis.</p>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Ubah Nama -->
        <div class="card mb-4">
            <div class="card-header">
                {{ __('Display Name') }}
            </div>
            <div class="card-body">
                @if(session('name_status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('name_status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->updateName->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->updateName->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form action="{{ route('profil.updateName') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-0">
                        <label for="name" class="form-label">{{ __('Full Name') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text"
                                   class="form-control @error('name', 'updateName') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', auth()->user()->name) }}"
                                   placeholder="{{ __('Enter your full name') }}"
                                   minlength="2"
                                   maxlength="255"
                                   required>
                            <button type="submit" class="btn btn-primary">{{ __('Save Name') }}</button>
                        </div>
                        <div class="form-text text-muted">{{ __('This name will be displayed throughout the application.') }}</div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Informasi Pengguna -->
        <div class="card mb-4">
            <div class="card-header">
                {{ __('Account Information') }}
            </div>
            <div class="card-body">
                @if(session('email_status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('email_status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->updateEmail->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->updateEmail->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form action="{{ route('profil.updateEmail') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email Address') }}</label>
                        <div class="input-group">
                             <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}" required>
                             <button type="submit" class="btn btn-primary">{{ __('Save Email') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        

        <!-- WhatsApp Number -->
        {{-- <div class="card mb-4">
            <div class="card-header">
                WhatsApp Configuration
            </div>
            <div class="card-body">
                @if(session('phone_status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('phone_status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->updatePhoneNumber->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->updatePhoneNumber->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <form action="{{ route('profil.updatePhoneNumber') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="no_hp" class="form-label">WhatsApp Number (Format: 628...)</label>
                        <div class="input-group">
                             <input type="number" class="form-control" id="no_hp" name="no_hp" value="{{ auth()->user()->no_hp }}" placeholder="628123456789">
                             <button type="submit" class="btn btn-success">Save Number</button>
                        </div>
                        <small class="text-muted">Nomor ini akan digunakan untuk fitur pencatatan transaksi via WhatsApp.</small>
                    </div>
                </form>
            </div>
        </div> --}}

        <!-- Ganti Password -->
        <div class="card mb-4">
            <div class="card-header">
                {{ __('Change Password') }}
            </div>
            <div class="card-body">
                 @if(session('password_status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('password_status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->updatePassword->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->updatePassword->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <form action="{{ route('profil.updatePassword') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">{{ __('New Password') }}</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <small class="text-muted">{{ __('At least 3 characters (can be letters, numbers, or symbols).') }}</small>
                    </div>
                     <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                             <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning">{{ __('Save Password') }}</button>
                </form>
            </div>
        </div>

        

        
    </div>
</div>
@endsection

@push('scripts')
@if(config('services.midtrans.is_production'))
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@else
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endif
<script>
(function() {
    const initProfileFeatures = () => {
        // 1. Password Toggle
        document.querySelectorAll('.toggle-password').forEach(button => {
            // Remove existing listener to prevent duplicates if script re-runs
            button.replaceWith(button.cloneNode(true));
        });

        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.closest('.input-group').querySelector('input');
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            });
        });

        // 2. Subscription Snap
        const payButton = document.getElementById('pay-button');
        if (payButton && !payButton.dataset.listenerAttached) {
            payButton.dataset.listenerAttached = 'true';
            payButton.addEventListener('click', function () {
                payButton.disabled = true;
                payButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __('Initializing...') }}';
                
                fetch("{{ route('subscription.subscribe') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                })
                .then(async response => {
                    const data = await response.json().catch(() => null);
                    if (!response.ok || !data || !data.success) {
                        throw new Error(data?.message || `HTTP error! status: ${response.status}`);
                    }
                    return data;
                })
                .then(data => {
                    window.snap.pay(data.snap_token, {
                        onSuccess: () => window.location.reload(),
                        onPending: () => window.location.reload(),
                        onError: () => {
                            alert("{{ __('Payment failed!') }}");
                            window.location.reload();
                        },
                        onClose: () => {
                            payButton.disabled = false;
                            payButton.innerHTML = '<i class="bi bi-qr-code-scan me-1"></i> {{ __('Subscribe Now') }} (Rp 49.000)';
                        }
                    });
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("Error: " + error.message);
                    payButton.disabled = false;
                    payButton.innerHTML = '<i class="bi bi-qr-code-scan me-1"></i> {{ __('Subscribe Now') }} (Rp 49.000)';
                });
            });
        }

        // 3. Theme Switcher Logic
        const savedTheme = "{{ auth()->user()->skin ?? 'auto' }}";
        const radio = document.querySelector(`input[name="theme_preference"][value="${savedTheme}"]`);
        if (radio) {
            radio.checked = true;
            document.querySelectorAll('.theme-option').forEach(opt => opt.classList.remove('border-primary', 'bg-light-subtle'));
            radio.closest('.theme-option').classList.add('border-primary', 'bg-light-subtle');
        }

        document.querySelectorAll('input[name="theme_preference"]').forEach(input => {
            if (input.dataset.listenerAttached) return;
            input.dataset.listenerAttached = 'true';
            input.addEventListener('change', (e) => {
                const theme = e.target.value;
                if (window.applyTheme) {
                    window.applyTheme(theme);
                    document.querySelectorAll('.theme-option').forEach(opt => opt.classList.remove('border-primary', 'bg-light-subtle'));
                    e.target.closest('.theme-option').classList.add('border-primary', 'bg-light-subtle');
                }
            });
        });

        // 4. UI Style Switcher Logic
        const savedStyle = "{{ auth()->user()->ui_style ?? 'corporate' }}";
        const styleRadio = document.querySelector(`input[name="style_preference"][value="${savedStyle}"]`);
        if (styleRadio) {
            styleRadio.checked = true;
            document.querySelectorAll('.style-option').forEach(opt => opt.classList.remove('border-primary', 'bg-primary-subtle'));
            styleRadio.closest('.style-option').classList.add('border-primary', 'bg-primary-subtle');
        }

        document.querySelectorAll('input[name="style_preference"]').forEach(input => {
            if (input.dataset.listenerAttached) return;
            input.dataset.listenerAttached = 'true';
            input.addEventListener('change', (e) => {
                const style = e.target.value;
                document.querySelectorAll('.style-option').forEach(opt => opt.classList.remove('border-primary', 'bg-primary-subtle'));
                e.target.closest('.style-option').classList.add('border-primary', 'bg-primary-subtle');

                fetch("{{ route('user.update.ui-style') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ui_style: style })
                }).then(() => location.reload()).catch(err => {
                    console.error('Failed to sync style:', err);
                    alert('Gagal menyelaraskan gaya visual.');
                });
            });
        });
    };

    // Initialize immediately (for SPA and full-load)
    initProfileFeatures();
    
    // Fallback for redundant calls
    document.addEventListener('spa:page-loaded', initProfileFeatures);
})();
</script>
@endpush
