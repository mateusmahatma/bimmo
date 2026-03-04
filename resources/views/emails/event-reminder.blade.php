<!DOCTYPE html>
<html>
<head>
    <title>Event Reminder</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #007bff;">Halo, {{ $event->user->name }}!</h2>
        <p>Anda memiliki agenda yang akan segera dimulai:</p>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h3 style="margin-top: 0;">{{ $event->title }}</h3>
            <p style="margin-bottom: 5px;"><strong>Waktu:</strong> {{ $event->start_at->format('d F Y, H:i') }}</p>
            @if($event->description)
                <p style="margin-bottom: 0;"><strong>Deskripsi:</strong> {{ $event->description }}</p>
            @endif
        </div>
        
        <p>Silakan cek dashboard BIMMO Anda untuk detail lebih lanjut.</p>
        
        <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 0.8rem; color: #777; text-align: center;">
            &copy; {{ date('Y') }} BIMMO - Budgeting & Financial Management
        </p>
    </div>
</body>
</html>
