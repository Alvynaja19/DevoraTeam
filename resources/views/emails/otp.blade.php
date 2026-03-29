<!DOCTYPE html>
<html>
<head>
    <title>Reset Password OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="background-color: #ffffff; padding: 20px; border-radius: 5px; max-width: 600px; margin: 0 auto; text-align: center;">
        <h2 style="color: #333;">Halo, {{ $name }}!</h2>
        <p style="color: #555;">Kami menerima permintaan untuk melakukan reset password pada akun Anda di DevoraTeam.</p>
        <p style="color: #555;">Berikut adalah kode OTP untuk melanjutkan proses reset password Anda:</p>
        
        <div style="margin: 20px 0;">
            <span style="font-size: 32px; font-weight: bold; background-color: #f0f0f0; padding: 10px 20px; border-radius: 5px; letter-spacing: 5px; color: #3b82f6;">
                {{ $otp }}
            </span>
        </div>

        <p style="color: #555;">Kode OTP ini hanya berlaku selama <strong>10 Menit</strong>.</p>
        <p style="color: #555; margin-top: 30px; font-size: 12px;">Jika Anda merasa tidak melakukan permintaan ini, abaikan email ini dan akun Anda akan tetap aman.</p>
    </div>
</body>
</html>
