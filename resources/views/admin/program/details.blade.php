@extends('admin.layouts.admin')

@section('content')


<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">All Data</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Date</th>
                  <th>Vendor</th>
                  <th>Party Name</th>
                  <th>Truck Number</th>
                  <th>Challan no</th>
                  <th>Line Charge</th>
                  <th>Qty</th>
                  <th>Token fee</th>
                  <th>Contract Amount</th>
                  <th>Advance</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data->programDetail as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y')}}</td>
                    <td style="text-align: center">{{$data->vendor->name}}</td>
                    <td style="text-align: center">{{$data->party_name}}</td>
                    <td style="text-align: center">{{$data->truck_number}}</td>
                    <td style="text-align: center">{{$data->challan_no}}</td>
                    <td style="text-align: center">{{$data->line_charge}}</td>
                    <td style="text-align: center">{{$data->qty}}</td>
                    <td style="text-align: center">{{$data->token_fee}}</td>
                    <td style="text-align: center">{{$data->amount}}</td>

                    <td style="text-align: center">
                      <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addColorModal">Fuel </span>
                      <span class="badge badge-secondary" style="cursor: pointer;" data-toggle="modal" data-target="#addColorModal">Money</span>
                    </td>

                    <td style="text-align: center">
                      <a id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                      <a id="deleteBtn" rid="{{$data->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                    </td>
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
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
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

<script>
  $(document).ready(function () {

    
  });
</script>
@endsection