<p>Halo <strong>{{ $cuti->karyawan->nama }}</strong>,</p>

<p>Kami menginformasikan bahwa pengajuan <strong>{{ $cuti->jenis_cuti }}</strong> Anda:</p>
<ul>
    <li>Tanggal: {{ $cuti->tanggal_mulai }} s.d {{ $cuti->tanggal_selesai }}</li>
    <li>Alasan: {{ $cuti->alasan }}</li>
</ul>

<p>Status Pengajuan: <strong style="color: {{ $statusKeputusan == 'Disetujui' ? 'green' : 'red' }}">{{ $statusKeputusan }}</strong></p>

<p>Terima kasih.</p>