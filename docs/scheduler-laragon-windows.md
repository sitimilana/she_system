# Scheduler Laragon / Windows

Panduan ini dipakai agar proses `alpha` berjalan otomatis di backend tanpa perlu karyawan membuka aplikasi.

## Yang sudah ada di kode

- Scheduler Laravel sudah dipasang di `app/Console/Kernel.php`.
- Scheduler dipanggil setiap menit.
- Logika generate alpha ada di `AbsensiController::generateAlphaHarian()`.

## Cara aktivasi di Windows / Laragon

### Opsi paling mudah: Task Scheduler

1. Buka **Task Scheduler** di Windows.
2. Pilih **Create Basic Task**.
3. Beri nama misalnya `Laravel Schedule`.
4. Pilih trigger **Daily**.
5. Atur supaya task berjalan **every 1 minute**.
6. Pada bagian action, pilih **Start a program**.
7. Arahkan ke file:
   - `C:\laragon\www\she_sistem\scheduler-run.bat`
8. Selesai dan simpan task.

### Kalau ingin langsung tanpa file `.bat`

Isi action bisa langsung ke:

- Program/script: `C:\laragon\bin\php\php.exe`
- Add arguments: `artisan schedule:run`
- Start in: `C:\laragon\www\she_sistem`

Catatan: jika `php.exe` tidak ada di path tersebut, sesuaikan dengan versi PHP Laragon yang dipakai.

## Cara cek berhasil atau tidak

1. Jalankan manual sekali:
   - `php artisan schedule:run`
2. Lihat apakah file log terbentuk di:
   - `storage/logs/scheduler.log`
3. Cek tabel `absensi` setelah jam presensi selesai.

## Penting

- `php artisan schedule:run` bukan dijalankan terus di terminal secara manual.
- Yang harus aktif adalah task scheduler Windows yang memanggil perintah itu otomatis.
- Kalau scheduler tidak jalan, data alfa tidak akan dibuat.
- Jika server sempat offline saat jam 17:00, data alfa akan dibuat saat scheduler aktif lagi dan memproses tanggal yang terlewat.