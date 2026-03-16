<!DOCTYPE html>
<html>
<head>
<title>Dashboard Kepala Bagian</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
background:#e5e5e5;
}

.sidebar{
width:250px;
min-height:100vh;
background:#8fa1c7;
position:fixed;
padding:20px;
}

.sidebar img{
width:120px;
display:block;
margin:auto;
margin-bottom:30px;
}

.sidebar .nav-link{
color:black;
font-size:18px;
margin-bottom:10px;
}

.sidebar .nav-link:hover{
background:rgba(255,255,255,0.3);
border-radius:10px;
}

.content{
margin-left:260px;
padding:40px;
}

.card-dashboard{
background:#8fa1c7;
border-radius:20px;
padding:25px;
box-shadow:0px 5px 10px rgba(0,0,0,0.2);
}

.card-penilaian{
background:white;
border-radius:15px;
padding:15px;
margin-bottom:15px;
box-shadow:0px 5px 10px rgba(0,0,0,0.2);
}

.card-small{
background:#8fa1c7;
border-radius:20px;
padding:30px;
text-align:center;
box-shadow:0px 5px 10px rgba(0,0,0,0.2);
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

<img src="{{ asset('images/logoshe.png') }}">

<ul class="nav flex-column">

<li class="nav-item">
<a href="{{ route('kabag.dashboard') }}" class="nav-link">
<i class="bi bi-house"></i> Home
</a>
</li>

<li class="nav-item">
<a href="{{ route('kabag.karyawan') }}" class="nav-link">
<i class="bi bi-person"></i> Kelola Karyawan
</a>
</li>

<li class="nav-item">
<a href="{{ route('kabag.penilaian') }}" class="nav-link">
<i class="bi bi-star"></i> Penilaian Kinerja
</a>
</li>

<li class="nav-item">
<a href="{{ route('kabag.gaji') }}" class="nav-link">
<i class="bi bi-cash-stack"></i> Manajemen Gaji
</a>
</li>

<li class="nav-item mt-4">
<a href="{{ route('logout') }}" class="nav-link" onclick="confirmLogout(event)">
<i class="bi bi-box-arrow-right"></i> Logout
</a>
</li>

</ul>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
@csrf
</form>

</div>


<!-- CONTENT -->

<div class="content">

<h1 class="mb-4"><b>Dashboard Kepala Bagian</b></h1>


<!-- PENILAIAN -->

<div class="card-dashboard mb-4">

<div class="d-flex justify-content-between mb-3">

<h4><b>Penilaian Kinerja Perbulan</b></h4>

<select class="form-select w-auto">
<option>Januari</option>
<option>Februari</option>
<option>Maret</option>
<option>April</option>
<option>Mei</option>
<option>Juni</option>
<option>Juli</option>
<option>Agustus</option>
<option>September</option>
<option>Oktober</option>
<option>November</option>
<option>Desember</option>
</select>

</div>


@foreach($penilaian as $p)

<div class="card-penilaian d-flex justify-content-between align-items-center">

<div>

<p><b>Nama :</b> {{ $p->nama ?? '-' }}</p>

<p><b>Tanggal Mulai-Selesai :</b>
{{ $p->tanggal_mulai ?? '-' }} -
{{ $p->tanggal_selesai ?? '-' }}
</p>

<p><b>Status :</b> {{ $p->status ?? '-' }}</p>

</div>

<button class="btn btn-danger">Detail</button>

</div>

@endforeach


</div>


<!-- BOTTOM -->

<div class="row">

<div class="col-md-3">

<div class="card-small">

<h5><b>Jumlah Karyawan</b></h5>

<h1>{{ $jumlahKaryawan }}</h1>

</div>

</div>


<div class="col-md-9">

<div class="card-dashboard">

<h5><b>Data Karyawan</b></h5>

<table class="table">

<thead>
<tr>
<th>No</th>
<th>Nama</th>
</tr>
</thead>

<tbody>

@foreach($karyawan as $k)

<tr>
<td>{{ $loop->iteration }}</td>
<td>{{ $k->nama }}</td>
</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>

</div>

<!-- Modal Konfirmasi Logout -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><b>Konfirmasi Logout</b></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin logout?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger" onclick="document.getElementById('logout-form').submit()">Ya, Logout</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmLogout(e) {
    e.preventDefault();
    var modal = new bootstrap.Modal(document.getElementById('logoutModal'));
    modal.show();
}
</script>

</body>
</html>