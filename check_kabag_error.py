import re

file_path = 'c:/laragon/www/she_sistem/resources/views/kepala_bagian/kelola_karyawan.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

pattern = r'(<h2 class="fw-bold m-0" style="color: #1e293b;">Data Karyawan</h2>\s*<p class="text-muted m-0">Daftar staf dan karyawan di departemen Anda\.</p>\s*</div>\s*</div>)'

error_block = '''\\1

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4">
            <i class="bi bi-exclamation-octagon-fill me-2"></i> <strong>Terjadi Kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
'''

new_content = re.sub(pattern, error_block, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(new_content)
    print("Injected error and success alerts!")
