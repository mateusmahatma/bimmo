<!DOCTYPE html>
<html>
<head>
    <title>Password Baru - BIMMO</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">

    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="color: #012970;">BIMMO</h2>
        </div>
        
        <p>Halo,</p>
        
        <p>Kami telah menerima permintaan untuk mengatur ulang kata sandi akun BIMMO Anda.</p>
        
        <p>Silakan klik tombol di bawah ini untuk mengatur ulang kata sandi Anda:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('password.reset', ['token' => $token, 'email' => $email]) }}" style="background-color: #012970; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">Reset Password</a>
        </div>
        
        <p>Jika tombol di atas tidak berfungsi, salin dan tempel tautan berikut ke browser Anda:</p>
        <p style="word-break: break-all; color: #012970;">{{ route('password.reset', ['token' => $token, 'email' => $email]) }}</p>
        
        <p>Tautan ini akan kedaluwarsa dalam 60 menit.</p>
        
        <p style="margin-top: 30px; font-size: 12px; color: #666;">Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini.</p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        
        <p style="text-align: center; font-size: 12px; color: #999;">&copy; {{ date('Y') }} BIMMO. All rights reserved.</p>
    </div>

</body>
</html>
