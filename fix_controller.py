import re

# 1. Update KepalaBagianController
file_path_kabag = 'c:/laragon/www/she_sistem/app/Http/Controllers/KepalaBagianController.php'
with open(file_path_kabag, 'r', encoding='utf-8') as f:
    content_kabag = f.read()

# Hapus metode detailKaryawan dan storeKaryawan
pattern_methods = r'    public function detailKaryawan\(\$id\)\n\s*\{.*?(?=    public function cuti\(\))'
content_kabag = re.sub(pattern_methods, '', content_kabag, flags=re.DOTALL)

# Ubah 'status_karyawan' di store jadi 'pending'
content_kabag = content_kabag.replace("'status_karyawan' => 'aktif',", "'status_karyawan' => 'pending',")

with open(file_path_kabag, 'w', encoding='utf-8') as f:
    f.write(content_kabag)

# 2. Update PimpinanController
file_path_pim = 'c:/laragon/www/she_sistem/app/Http/Controllers/PimpinanController.php'
with open(file_path_pim, 'r', encoding='utf-8') as f:
    content_pim = f.read()

# Update approveKaryawan
approve_method_old = """    public function approveKaryawan($id)
    {
        $user = User::findOrFail($id);
        $user->status_akun = 'aktif';
        $user->save();

        return redirect()->route('pimpinan.karyawan_pending')->with('success', 'Status akun karyawan berhasil diaktifkan.');
    }"""
approve_method_new = """    public function approveKaryawan($id)
    {
        $user = User::with('karyawan')->findOrFail($id);
        $user->status_akun = 'aktif';
        $user->save();
        
        if ($user->karyawan) {
            $user->karyawan->status_karyawan = 'aktif';
            $user->karyawan->save();
        }

        return redirect()->route('pimpinan.karyawan_pending')->with('success', 'Akun & Profil karyawan berhasil diaktifkan dan siap lakukan presensi.');
    }"""

content_pim = content_pim.replace(approve_method_old, approve_method_new)

with open(file_path_pim, 'w', encoding='utf-8') as f:
    f.write(content_pim)

print("Controllers updated!")
