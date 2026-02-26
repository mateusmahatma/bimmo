<!DOCTYPE html>
<html>
<head>
    <title>Feedback/Masukan Baru</title>
</head>
<body>
    <h2>Masukan Baru dari Pengguna</h2>
    <p><strong>Nama:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <hr>
    <p><strong>Deskripsi:</strong></p>
    <p style="white-space: pre-wrap;">{{ $description }}</p>
    <hr>
    <p><small>Dikirim pada {{ now()->format('d/m/Y H:i:s') }}</small></p>
</body>
</html>
