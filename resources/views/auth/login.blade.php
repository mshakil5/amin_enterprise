<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Admin Portal</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/admin/css/adminlte.min.css')}}">
  
  <style>
    .login-page { background-color: #f4f6f9; }
    .card-outline.card-secondary { border-top: 3px solid #6c757d; }
    .input-group-text { cursor: pointer; transition: color 0.2s; }
    .input-group-text:hover { color: #007bff; }
    .invalid-feedback { font-weight: 600; }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-secondary shadow-lg">
    <div class="card-header text-center py-4">
      <a href="#" class="h1"><b>Admin</b>Login</a>
    </div>
    <div class="card-body login-card-body">
      <p class="login-box-msg text-muted">Sign in to access your dashboard</p>

      @if (isset($message))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <small><strong>Error:</strong> {{ $message }}</small>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="input-group mb-3">
          <input id="email" type="email" 
                 class="form-control @error('email') is-invalid @enderror" 
                 name="email" 
                 value="{{ old('email') }}" 
                 placeholder="Email Address"
                 required autocomplete="email" autofocus>
          
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
          @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>

        <div class="input-group mb-3">
          <input id="password" type="password" 
                 class="form-control @error('password') is-invalid @enderror" 
                 name="password" 
                 placeholder="Password"
                 required autocomplete="current-password">
          
          <div class="input-group-append" id="togglePassword" style="cursor: pointer;">
            <div class="input-group-text">
              <span class="fas fa-eye" id="eyeIcon"></span>
            </div>
          </div>
          @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>

        <div class="row mt-4">
          <div class="col-12">
            <button type="submit" class="btn btn-secondary btn-block btn-flat">
                <i class="fas fa-sign-in-alt mr-2"></i> Sign In
            </button>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // Toggle Password Visibility
        $('#togglePassword').on('click', function() {
            const passwordField = $('#password');
            const icon = $(this).find('i');
            
            // Check the current type
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            
            // Change the type
            passwordField.attr('type', type);
            
            // Toggle the eye / eye-slash icon
            icon.toggleClass('fa-eye fa-eye-slash');
        });
    });
</script>

</body>
</html>