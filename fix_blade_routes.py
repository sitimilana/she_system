import re

# 1. Update routes/web.php
file_path_web = 'c:/laragon/www/she_sistem/routes/web.php'
with open(file_path_web, 'r', encoding='utf-8') as f:
    content_web = f.read()

pattern_routes = r"    Route::get\('/kepala-bagian/karyawan/\{id\}', \[KepalaBagianController::class, 'detailKaryawan'\]\)->name\('kabag\.karyawan\.detail'\);\n\s*Route::post\('/kepala-bagian/karyawan/\{id\}', \[KepalaBagianController::class, 'storeKaryawan'\]\)->name\('kabag\.karyawan\.store'\);\n"
content_web = re.sub(pattern_routes, '', content_web)

with open(file_path_web, 'w', encoding='utf-8') as f:
    f.write(content_web)

# 2. Update resources/views/kepala_bagian/kelola_karyawan.blade.php
file_path_blade = 'c:/laragon/www/she_sistem/resources/views/kepala_bagian/kelola_karyawan.blade.php'
with open(file_path_blade, 'r', encoding='utf-8') as f:
    content_blade = f.read()

# Hapus <th width="10%" class="text-center">Detail</th>
content_blade = content_blade.replace('<th width="10%" class="text-center">Detail</th>', '')
# Hapus <td> detail
pattern_td = r'<td class="text-center">\s*<a href="\{\{ route\(\'kabag\.karyawan\.detail\', \$user->id_user\) \}\}" class="btn btn-sm btn-light border text-primary" title="Lengkapi Profil">\s*<i class="bi bi-pencil-square"></i>\s*</a>\s*</td>'
content_blade = re.sub(pattern_td, '', content_blade)
# Update text-center pending if
content_blade = content_blade.replace("Illuminate\Support\Str::lower($user->karyawan->status_karyawan) == 'aktif'", "Illuminate\Support\Str::lower($user->karyawan->status_karyawan) == 'aktif' ? 'success' : (Illuminate\Support\Str::lower($user->karyawan->status_karyawan) == 'pending' ? 'warning text-dark'")
content_blade = content_blade.replace("Aktif</option>", "Aktif</option>\n                                                <option value=\"pending\">Pending</option>")

# We must refine the pending output
pattern_badge = r"@if\(\$user->karyawan && \$user->karyawan->status_karyawan\)\s*<span class=\"badge bg-.*?\{\{ \$user->karyawan->status_karyawan \}\}\s*</span>\s*@else"

custom_badge = """@if($user->karyawan && $user->karyawan->status_karyawan)
                                <span class="badge bg-{{ strtolower($user->karyawan->status_karyawan) == 'aktif' ? 'success' : (strtolower($user->karyawan->status_karyawan) == 'pending' ? 'warning text-dark' : 'danger') }} px-3 py-2 rounded-pill text-capitalize">
                                    {{ $user->karyawan->status_karyawan }}
                                </span>
                            @else"""
content_blade = re.sub(pattern_badge, custom_badge, content_blade, flags=re.DOTALL)

with open(file_path_blade, 'w', encoding='utf-8') as f:
    f.write(content_blade)

print("Blade and routes updated!")
