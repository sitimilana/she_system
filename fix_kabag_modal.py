import re

file_path = 'c:/laragon/www/she_sistem/resources/views/kepala_bagian/kelola_karyawan.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

pattern = r'<!-- Modal Tambah Karyawan Baru -->.*?(?=@include\(\'auth\.logout\'\))'

new_modal = '''<!-- Modal Tambah Karyawan Baru -->
<div class="modal fade" id="modalTambahBaru" tabindex="-1" aria-labelledby="modalTambahBaruLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('kabag.karyawan.store_baru') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white border-bottom-0 rounded-top-4 py-3">
                    <h5 class="modal-title fw-bold" id="modalTambahBaruLabel">
                        <i class="bi bi-person-plus-fill me-2"></i>Tambah Karyawan Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4 bg-light">
                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 shadow-sm p-3 mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                                <strong class="mb-0">Gagal Menyimpan Data!</strong>
                            </div>
                            <ul class="mb-0 ps-3 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-4">
                        <!-- Left Column: Account Info -->
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-4 mt-1 text-primary border-bottom pb-2">
                                        <i class="bi bi-shield-lock me-2"></i>Informasi Akun
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-dark small">Username <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white text-muted"><i class="bi bi-person"></i></span>
                                            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="Cth: andi123">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-dark small">Password <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white text-muted"><i class="bi bi-key"></i></span>
                                            <input type="password" name="password" class="form-control" required minlength="6" placeholder="Minimal 6 karakter">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold text-dark small">Role Akses <span class="text-danger">*</span></label>
                                        <select name="role_id" class="form-select form-select-sm" required>
                                            <option value="" disabled selected>-- Pilih Role --</option>
                                            @foreach(App\Models\Role::all() as $role)
                                                <option value="{{ $role->role_id }}" {{ old('role_id') == $role->role_id ? 'selected' : '' }}>{{ $role->nama_role }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Biodata -->
                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-4 mt-1 text-primary border-bottom pb-2">
                                        <i class="bi bi-person-lines-fill me-2"></i>Biodata Karyawan
                                    </h6>
                                    
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold text-dark small">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" name="nama_lengkap" class="form-control form-control-sm" value="{{ old('nama_lengkap') }}" required placeholder="Nama lengkap sesuai KTP">
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold text-dark small">Kontak (No. HP)</label>
                                            <input type="text" name="no_hp" class="form-control form-control-sm" value="{{ old('no_hp') }}" placeholder="08xxxxxx">
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold text-dark small">Email Aktif</label>
                                            <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email') }}" placeholder="email@contoh.com">
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold text-dark small">Divisi/Jabatan</label>
                                            <select name="divisi" class="form-select form-select-sm">
                                                <option value="keuangan" {{ old('divisi') == 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                                                <option value="admin umum" {{ old('divisi') == 'admin umum' ? 'selected' : '' }}>Admin Umum</option>
                                                <option value="akademik" {{ old('divisi') == 'akademik' ? 'selected' : '' }}>Akademik</option>
                                                <option value="marketing" {{ old('divisi') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                                <option value="office boy" {{ old('divisi') == 'office boy' ? 'selected' : '' }}>Office Boy</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold text-dark small">Status Kerja</label>
                                            <select name="status_karyawan" class="form-select form-select-sm disabled" disabled>
                                                <option value="aktif" selected>Aktif</option>
                                            </select>
                                            <!-- Hidden input mapping it since the select is disabled but the controller hardcodes it anyways -->
                                        </div>
                                        
                                        <div class="col-12">
                                            <label class="form-label fw-semibold text-dark small">Alamat Domisili</label>
                                            <textarea name="alamat" class="form-control form-control-sm" rows="2" placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer border-0 px-4 py-3 bg-white rounded-bottom-4 justify-content-between">
                    <button type="button" class="btn btn-light rounded-3 px-4 fw-medium" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm fw-medium">
                        <i class="bi bi-save me-1"></i> Daftarkan Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

'''

new_content = re.sub(pattern, new_modal, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(new_content)
    print("Fixed modal ui!")
