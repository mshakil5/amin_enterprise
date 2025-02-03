@extends('admin.layouts.admin')

@section('content')

<style>
  .form-checkbox {
      font-family: system-ui, sans-serif;
      font-size: 2rem;
      font-weight: bold;
      line-height: 1.1;
      display: grid;
      grid-template-columns: 1em auto;
      gap: 0.5em;
    }

    .custom-checkbox {
      height: 30px;
    }
</style>
<!-- Main content -->
<section class="content mt-3" id="newBtnSection">
  <div class="container-fluid">
    <div class="row">
      <div class="col-6">
        
          {{-- <a href="{{route('challanPostingVendorReport')}}" class="btn btn-secondary my-3">Back</a> --}}

          
      </div>
    </div>
  </div>
</section>
<!-- /.content -->


<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Mother Vassel: {{$motherVesselName}}</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <h3 class="text-center">{{$motherVesselName}}</h3>
                <table id="example3" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $month => $value)
                        <tr>
                            <td>{{ $month }}</td>
                            <td>{{ $value }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>


            
                
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->



@endsection


@section('script')

<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"],
        "lengthMenu": [[100, "All", 50, 25], [100, "All", 50, 25]]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

      
    $("#example3").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": [
        {
        extend: 'copy',
        title: 'Vendor Advance Summary'
        },
        {
        extend: 'csv',
        title: 'Vendor Advance Summary'
        },
        {
        extend: 'excel',
        title: 'Vendor Advance Summary'
        },
        {
        extend: 'pdf',
        title: 'Mother Vessel: {{$motherVesselName}}',
        customize: function (doc) {
          doc.content.splice(0, 0, {
            text: 'Vendor Advance Summary',
            style: 'header',
            alignment: 'center'
          });
        }
        },
        {
        extend: 'print',
        title: 'Mother Vessel: {{$motherVesselName}}',
        customize: function (win) {
          $(win.document.body).prepend(
            '<h1 style="text-align:center;">Vendor Advance Summary</h1>'
          );
        }
        }
      ],
      "lengthMenu": [[100, "All", 50, 25], [100, "All", 50, 25]]
    }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');

      
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });
    });
</script>



@endsection