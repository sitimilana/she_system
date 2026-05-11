<p>Halo <strong>{{ $cuti->karyawan->nama }}</strong>,</p>

<p>Pengajuan <strong>{{ $cuti->jenis_cuti }}</strong> Anda ({{ $cuti->tanggal_mulai }} s.d {{ $cuti->tanggal_selesai }}) telah kami terima.</p>

<p style="background: #fff3cd; padding: 10px; border: 1px solid #ffeeba;">
    <strong>Peringatan Bentrok Jadwal:</strong><br>
    Pada tanggal tersebut, karyawan berikut juga mengajukan cuti/izin: <strong>{{ $listNama }}</strong>.
</p>

<p>Keputusan akhir tetap menunggu verifikasi dari Pimpinan. Silakan cek email secara berkala.</p>