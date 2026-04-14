<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Amin Enterprise | Road Transport</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css')}}">
  
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('assets/admin/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('assets/admin/css/OverlayScrollbars.min.css')}}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('assets/admin/datatables/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/admin/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/admin/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
  <!-- Select2 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />

<style>
    /* =============================================
       SELECT2 STYLING FIXES
       ============================================= */
    
    /* Base Select2 Container */
    .select2-container {
        width: 100% !important;
        display: inline-block;
    }
    
    /* Single Selection Box */
    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        height: 31px !important;
        padding: 0 10px;
        background-color: #fff;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    /* Selected Value Text */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        padding-right: 20px;
        line-height: 29px !important;
        font-size: 13px;
        color: #495057;
    }
    
    /* Dropdown Arrow */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 29px !important;
        width: 20px;
        right: 6px;
        top: 1px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #495057 transparent transparent transparent;
        border-width: 5px 4px 0 4px;
        margin-left: -4px;
        margin-top: -2px;
    }
    
    /* Hover State */
    .select2-container--default:hover .select2-selection--single {
        border-color: #adb5bd;
    }
    
    /* Focus State */
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        outline: none;
    }
    
    /* Placeholder Text */
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
        font-size: 13px;
    }
    
    /* =============================================
       DROPDOWN STYLING
       ============================================= */
    
    .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        margin-top: 1px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        overflow-x: hidden;
    }
    
    .select2-dropdown--above {
        margin-top: -1px;
        margin-bottom: 1px;
        box-shadow: 0 -3px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* Search Box */
    .select2-container--default .select2-search--dropdown {
        padding: 8px;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 4px 10px;
        font-size: 13px;
        height: 31px;
        outline: none;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    /* Dropdown Options */
    .select2-container--default .select2-results__option {
        padding: 6px 12px;
        font-size: 13px;
        color: #495057;
        margin: 0;
    }
    
    .select2-container--default .select2-results__option:hover,
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #28a745;
        color: #fff;
    }
    
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #e9ecef;
        color: #495057;
    }
    
    /* Results Group */
    .select2-container--default .select2-results__group {
        padding: 6px 12px;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    /* =============================================
       OTHER STYLING
       ============================================= */
    
    .info-box .info-box-number {
        font-size: 16px !important;
    }
    
    #income-form-card {
        border-left: 4px solid #28a745;
        transition: all 0.3s ease;
    }
    
    #income-form-card.edit-mode {
        border-left-color: #ffc107;
    }
    
    .form-control-sm:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .btn-action {
        padding: 2px 8px;
        font-size: 11px;
        margin-right: 3px;
    }
    
    /* Form group spacing */
    #income-form .form-group {
        margin-bottom: 10px;
    }
    
    /* Label spacing */
    #income-form label {
        margin-bottom: 4px;
    }
</style>

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{ asset('logo.png')}}" alt="logo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      
      <li class="nav-item d-none d-sm-inline-block">
        <a class="dropdown-item" href="{{ route('logout') }}"
            onclick="event.preventDefault();
                          document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
      </li>
      
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('avatar5.png')}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{Auth::user()->name}}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      @include('admin.inc.sidebar')
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    

    <!-- Main content -->
    @yield('content')
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; {{date('Y')}} <a href="https://www.mentosoftware.co.uk/">Mento Software</a>.</strong>
    All rights reserved.
 
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->


<!-- jQuery -->
<script src="{{ asset('assets/admin/js/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('assets/admin/js/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('assets/admin/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets/admin/js/adminlte.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
{{-- <script src="{{ asset('assets/admin/js/dashboard.js')}}"></script> --}}
<!-- DataTables  & Plugins -->
<script src="{{ asset('assets/admin/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables/dataTables.bootstrap4.min.js')}}"></script>

<script src="{{ asset('assets/admin/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>


<script src="{{ asset('assets/admin/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables/jszip/jszip.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables/pdfmake/vfs_fonts.js')}}"></script>

<script src="{{ asset('assets/admin/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
<script>
  // page schroll top
  function pagetop() {
          window.scrollTo({
              top: 130,
              behavior: 'smooth',
          });
      }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>

@yield('script')

<script>
  $(document).ready(function() {
      $('.select2').select2({
          placeholder: "Select an option",
          allowClear: true
      });
  });
</script>

</body>
</html>
