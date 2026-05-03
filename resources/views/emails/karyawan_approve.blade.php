<!DOCTYPE html>
<html>
<head><title>Akun Aktif</title></head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h3 style="color: #4a5568;">Selamat, {{ $user->nama_lengkap }}!</h3>
        <p>Pengajuan akun Anda telah <b>DISETUJUI</b> oleh Pimpinan.</p>
        <p>Aplikasi Mobile sudah dapat diakses menggunakan kredensial berikut:</p>
        
        <div style="background-color: #f8fafc; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <p style="margin: 5px 0;"><b>Username :</b> {{ $user->username }}</p>
            <p style="margin: 5px 0;"><b>Password :</b> {{ $user->password_sementara }}</p>
        </div>

        <p>Silakan mencoba login. Jika ada kendala, hubungi Kepala Bagian Anda.</p>
        <p>Terima kasih.</p>
    </div>
</body>
</html>