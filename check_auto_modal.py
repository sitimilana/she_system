import re

file_path = 'c:/laragon/www/she_sistem/resources/views/kepala_bagian/kelola_karyawan.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

script_block = '''<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
@if($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('modalTambahBaru'));
        myModal.show();
    });
</script>
@endif
'''

new_content = content.replace('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>', script_block)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(new_content)
    print("Auto modal script injected!")
