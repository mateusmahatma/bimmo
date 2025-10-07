<head>
    <title>Ubah Password</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

@extends('layouts.main')
@section('container')

<nav id="navbar-example2" class="navbar navbar-light bg-light px-3">
    <a class="navbar-brand" href="/ubah-password">Ubah Password</a>
</nav>

<div class="card-header">
    <div class="card-body">
        @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form method="post" action="/ubah-password">
            @csrf
            <div class="mt-3 mb-3">
                <label for="current_password" class="form-label">Password Sekarang:</label>
                <input type="password" name="current_password" id="current_password" class="form-control" required>
                <span class="password-toggle-icon" data-toggle="current_password"></span>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">Password Baru:</label>
                <input type="password" name="new_password" id="new_password" class="form-control" required>
                <input type="checkbox" id="toggle-password" />
                <label for="toggle-password">Lihat Password Baru</label>
            </div>

            <button type="submit" class="btn btn-warning">Simpan</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/ubah-password.js') }}"></script>
@endsection