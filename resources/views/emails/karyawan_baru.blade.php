<!DOCTYPE html>
<html>
<head><title>Informasi Akun Baru</title></head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">

    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h3 style="color: #4a5568;">Halo, {{ $user->nama_lengkap }}!</h3>
        
        <p>Akun aplikasi Anda telah didaftarkan oleh Kepala Bagian.</p>
        <p>Berikut adalah informasi kredensial login Anda:</p>
        
        <div style="background-color: #f8fafc; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <p style="margin: 5px 0;"><b>Username :</b> {{ $user->username }}</p>
            <p style="margin: 5px 0;"><b>Password :</b> {{ $passwordAsli }}</p>
        </div>

        <p style="color: #dc3545; font-weight: bold; padding: 10px; border-left: 4px solid #dc3545; background-color: #f8d7da;">
            PENTING: Saat ini status akun Anda masih PENDING. Anda belum bisa melakukan login.
        </p>
        
        <p>Sistem akan mengirimkan email pemberitahuan kembali jika Pimpinan telah menyetujui akun Anda.</p>
        <p>Terima kasih.</p>
    </div>

</body>
</html>