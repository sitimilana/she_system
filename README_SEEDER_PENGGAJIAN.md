# Panduan Menjalankan Seeder Penggajian & Penilaian Kinerja

## 📋 Deskripsi Seeder

Seeder ini akan membuat data dummy untuk:
- **Penggajian**: 5 bulan (Januari - Mei 2026)
- **Penilaian Kinerja**: 5 bulan (Januari - Mei 2026)
- Hanya untuk karyawan aktif yang sudah join sebelum atau pada bulan tersebut

### Fitur Utama:
✅ Otomatis cek `tanggal_masuk` karyawan - tidak buat data jika belum join  
✅ Hindari duplikasi data (cek jika sudah ada)  
✅ Hitung gaji berdasarkan data absensi yang ada  
✅ Generate random score penilaian (65-100)  
✅ Bonus & insentif otomatis berdasarkan skor penilaian  

---

## 🚀 Cara Menjalankan

### Option 1: Jalankan Seeder Langsung

```bash
php artisan db:seed --class=PenggajianPenilaianSeeder
```

### Option 2: Include di DatabaseSeeder (Optional)

Edit file `database/seeders/DatabaseSeeder.php`:

```php
public function run(): void
{
    // ... seeder lainnya ...
    
    // Tambahkan di bawah:
    $this->call(PenggajianPenilaianSeeder::class);
}
```

Kemudian jalankan:
```bash
php artisan db:seed
```

---

## 📊 Penjelasan Kalkulasi

### Penggajian
- **Gaji Pokok**: Rp 5.000.000 (standar)
- **Tunjangan Jabatan**: Rp 500.000 (standar)
- **Uang Makan**: Rp 20.000 × hari hadir (dari tabel absensi)
- **Insentif Kinerja**: 
  - Skor ≥ 90 → Rp 150.000
  - Skor ≥ 80 → Rp 100.000
  - Skor ≥ 70 → Rp 50.000
- **BPJS Potong**: Rp 231.000 (standar)
- **Potongan Absen**: Rp 70.000 × hari (alfa + izin + cuti >1 hari)

### Penilaian Kinerja
Score dihitung dari 5 dimensi:
1. **Disiplin**: 70-100
2. **Produktivitas**: 65-95
3. **Tanggung Jawab**: 70-100
4. **Sikap Kerja**: 65-95
5. **Loyalitas**: 70-100

**Total Skor** = Rata-rata ke-5 dimensi

---

## ✨ Contoh Output

Jika sukses, akan tampil:

```
🔄 Membuat data Penggajian dan Penilaian Kinerja...
📅 Period: Januari - Mei 2026
👥 Total Karyawan: 15

Processing Bulan: Januari
Processing Bulan: Februari
Processing Bulan: Maret
Processing Bulan: April
Processing Bulan: Mei

✅ Seeder Completed!
📊 Penggajian records created: 75
📈 Penilaian records created: 75
⏭️ Skipped (already exists): 0
```

---

## ⚠️ Catatan Penting

1. **Data Absensi Harus Ada Dulu**
   - Seeder ini menggunakan data dari tabel `absensi` untuk menghitung uang makan & potongan
   - Pastikan ada data absensi untuk bulan Januari-Mei 2026

2. **Tanggal Masuk Karyawan**
   - Seeder otomatis skip karyawan yang belum join pada bulan tersebut
   - Contoh: Jika karyawan join 15 Maret, maka tidak akan ada data untuk Januari & Februari

3. **Duplikasi**
   - Seeder dirancang tidak duplikasi
   - Jika jalankan 2x, akan skip otomatis untuk data yang sudah ada

4. **Penilai (dinilai_oleh)**
   - Otomatis ambil random dari user yang punya role "Pimpinan" atau "Kepala Bagian"

---

## 🔄 Jika Ingin Reset / Hapus Data

```bash
# Hapus semua data penggajian & penilaian
php artisan tinker

# Di tinker:
DB::table('penggajian')->delete();
DB::table('penilaian')->delete();

quit
```

Kemudian jalankan seeder lagi untuk data baru.

---

## 📝 Kustomisasi

Edit file `database/seeders/PenggajianPenilaianSeeder.php` untuk:
- Ubah konstanta gaji pokok & tunjangan
- Ubah range score penilaian
- Ubah logika bonus & insentif
- Tambahkan variasi gaji berdasarkan divisi/jabatan
