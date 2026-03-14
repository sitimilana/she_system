<!DOCTYPE html>
<html>
<head>
    <title>Login SHE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background:#8fa1c7;">
<div class="container vh-100 d-flex justify-content-center align-items-center">
<div class="card p-4 shadow" style="width:350px;border-radius:15px;">

    <h4 class="text-center mb-3">Login to your account</h4>

    {{-- Tampilkan pesan error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <input type="text" 
               name="username" 
               class="form-control mb-3 @error('username') is-invalid @enderror" 
               placeholder="Username"
               value="{{ old('username') }}">

        <input type="password" 
               name="password" 
               class="form-control mb-3" 
               placeholder="Password">

        <button type="submit" class="btn btn-danger w-100">Login</button>
    </form>

</div>
</div>
</body>
</html>
