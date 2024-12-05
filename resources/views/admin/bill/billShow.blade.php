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
      <div class="col-2">
          <a href="{{route('admin.allProgram')}}" class="btn btn-secondary my-3">Back</a>
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
              <h3 class="card-title">Mother Vassel: {{$programId->motherVassel->name}}</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>sl</th>
                    <th>header_id</th>
                    <th>date</th>
                    <th>truck_number</th>
                    <th>destination</th>
                    <th>from_location</th>
                    <th>to_location</th>
                    <th>shipping_method</th>
                    <th>challan_qty</th>
                    <th>Bill Status</th>

                    
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">{{$data->header_id}}</td>
                    <td style="text-align: center">{{$data->date}}</td>
                    <td style="text-align: center">{{$data->truck_number}}</td>
                    <td style="text-align: center">{{$data->destination}}</td>
                    <td style="text-align: center">{{$data->from_location }}</td>
                    <td style="text-align: center">{{$data->to_location}}</td>
                    <td style="text-align: center">{{$data->shipping_method}}</td>
                    <td style="text-align: center">{{$data->challan_qty}}</td>
                    <td style="text-align: center">
                        <label class="form-checkbox  grid layout">
                            <input type="checkbox" name="checkbox-checked" class="custom-checkbox"  @if ($data->billing_status == 1) checked @endif  />
                        </label>
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


@endsection