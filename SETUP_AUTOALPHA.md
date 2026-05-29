# Setup AutoAlpha (Absensi Otomatis Alfa)

Fitur autoalpha membutuhkan **Windows Task Scheduler** agar berjalan otomatis setiap hari jam 17:00.

## Langkah Setup (Windows)

### 1. Buka Task Scheduler
- Tekan **Win + R**
- Ketik: `taskschd.msc`
- Tekan **Enter**

### 2. Buat Task Baru
- Klik **Create Basic Task** (di panel kanan)
- Isi:
  - **Name**: `Laravel SHE System - AutoAlpha`
  - **Description**: `Menjalankan autoalpha absensi setiap menit`
  - Klik **Next**

### 3. Atur Trigger
- Pilih trigger: **Daily**
- Atur waktu: **17:00** (Jam 5 sore)
- Klik **Next**

### 4. Atur Action
- Pilih: **Start a program**
- Di field **Program/script**, isikan:
  ```
  C:\laragon\bin\php\php.exe
  ```
  
  ⚠️ **Catatan**: Sesuaikan versi PHP jika berbeda
  - Buka Explorer
  - Cek di: `C:\laragon\bin\php\` 
  - Lihat folder PHP yang ada (php8.3, php8.1, dst)

- Di field **Add arguments**, isikan:
  ```
  C:\laragon\www\she_sistem\artisan schedule:run
  ```

- Di field **Start in (optional)**, isikan:
  ```
  C:\laragon\www\she_sistem
  ```

### 5. Selesai
- Klik **Next** dan **Finish**
- Task akan langsung aktif

## Cara Verifikasi Berjalan

### Method 1: Manual Test
```bash
cd C:\laragon\www\she_sistem
php artisan schedule:run
```

Cek output di: `storage/logs/laravel.log`

### Method 2: Cek Log File
File: `C:\laragon\www\she_sistem\storage\logs\laravel.log`

Cari baris:
```
AutoAlpha: Selesai - X alfa dicatat, Y cuti terdeteksi
```

### Method 3: Cek Database
Setelah jam 17:00, buka tabel `absensi`:
```sql
SELECT * FROM absensi 
WHERE status = 'alfa' 
ORDER BY created_at DESC;
```

Seharusnya ada data baru dengan status `alfa` untuk karyawan yang tidak absen.

## Troubleshooting

### ❌ AutoAlpha Tidak Berjalan

**Kemungkinan 1: PHP Path Salah**
- Buka `C:\laragon\bin\php\`
- Cek versi PHP (php8.0, php8.1, php8.2, dst)
- Update path di Task Scheduler

**Kemungkinan 2: Task Tidak Aktif**
- Buka Task Scheduler
- Klik **Task Scheduler Library**
- Cari task `Laravel SHE System - AutoAlpha`
- Klik kanan → **Properties**
- Tab **General** → Centang **Run with highest privileges**
- Klik **OK**

**Kemungkinan 3: Status Cuti Tidak Tepat**
- Cek status cuti di database (harus: `approved`, `disetujui_hrd`, `disetujui_kabag`, atau `Disetujui`)
- Jika status berbeda, update di `AbsensiController::generateAlphaHarian()`

### ❌ Jika Laragon Offline saat Jam 17:00
- Autoalpha akan berjalan saat Laragon online lagi
- Data alfa akan tetap tercatat otomatis

## Command Manual (Debugging)

Jalankan langsung dari terminal:
```bash
cd C:\laragon\www\she_sistem
php artisan schedule:run
```

Atau jalankan command khusus:
```bash
php artisan absensi:auto-alpha
```

Lihat hasil di:
- Database tabel `absensi`
- Log file: `storage/logs/laravel.log`

---

**Catatan Penting:**
- Autoalpha hanya mencatat karyawan yang **AKTIF**
- Tidak akan mencatat jika karyawan sedang **CUTI** (status valid)
- Tidak akan mencatat hari **WEEKEND** dan **HARI LIBUR**
- Data akan disimpan otomatis ke database setiap hari setelah jam 17:00
