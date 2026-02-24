@extends('layouts.main')

@section('container')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">User Profile</h2>

        <!-- Informasi Pengguna -->
        <div class="card mb-4">
            <div class="card-header">
                Account Information
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
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                             <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}" required>
                             <button type="submit" class="btn btn-primary">Save Email</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- WhatsApp Number -->
        <div class="card mb-4">
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
        </div>

        <!-- Ganti Password -->
        <div class="card mb-4">
            <div class="card-header">
                Change Password
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
                        <label for="current_password" class="form-label">Current Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        <small class="text-muted">At least 3 characters (can be letters, numbers, or symbols).</small>
                    </div>
                     <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                             <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning">Save Password</button>
                </form>
            </div>
        </div>

        <!-- Foto Profil -->
        <div class="card mb-4">
            <div class="card-header">
                Profile Photo
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
                                <label for="profile_photo" class="form-label">Select Photo</label>
                                <input class="form-control" type="file" id="profile_photo" name="profile_photo" accept="image/*" required>
                                <div class="form-text">Maksimum 2MB (JPG, PNG, GIF, SVG).</div>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload New Photo</button>
                        </form>
                        
                        @if(auth()->user()->profile_photo)
                            <form action="{{ route('profil.deletePhoto') }}" method="POST" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus foto profil?')">Remove Photo</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Notification Configuration -->
        <div class="card mb-4">
            <div class="card-header">
                Notification Settings
            </div>
            <div class="card-body">
                @if(session('notification_status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('notification_status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->updateNotification->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->updateNotification->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('profil.updateNotification') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="daily_notification" name="daily_notification" value="1" {{ auth()->user()->daily_notification ? 'checked' : '' }}>
                        <label class="form-check-label" for="daily_notification">Enable Daily Transaction Reminder</label>
                    </div>

                    <div id="interval_settings" class="{{ auth()->user()->daily_notification ? '' : 'd-none' }}">
                        <div class="mb-3">
                            <label for="notification_interval" class="form-label">Reminder Interval (Minutes)</label>
                            <div class="input-group" style="max-width: 250px;">
                                <input type="number" class="form-control" id="notification_interval" name="notification_interval" value="{{ auth()->user()->notification_interval ?? 30 }}" min="1">
                                <span class="input-group-text">minutes</span>
                            </div>
                            <small class="text-muted">The notification will reappear every X minutes if you haven't logged any transactions today.</small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('daily_notification')?.addEventListener('change', function() {
        const intervalSettings = document.getElementById('interval_settings');
        if (this.checked) {
            intervalSettings.classList.remove('d-none');
        } else {
            intervalSettings.classList.add('d-none');
        }
    });

    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
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
</script>
@endpush
