@extends('admin.layouts.admin')

@section('content')


<!-- Main content -->
<section class="content mt-3" id="newBtnSection">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
          <a href="{{route('admin.addProgram')}}" class="btn btn-secondary my-3">Before Challan Receive</a>
          <a href="{{route('admin.afterPostProgram')}}" class="btn btn-secondary my-3">After Challan Receive</a>
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
              <h3 class="card-title">All Data</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Date</th>
                  <th>Client</th>
                  <th>Program ID</th>
                  <th>Mother Vassel</th>
                  <th>Lighter Vassel</th>
                  <th>Consignment No.</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y')}}</td>
                    <td style="text-align: center">{{$data->client->name}}</td>
                    <td style="text-align: center">{{$data->programid}}</td>
                    <td style="text-align: center">{{$data->motherVassel->name}}</td>
                    <td style="text-align: center">{{$data->lighterVassel->name}}</td>
                    <td style="text-align: center">{{$data->consignmentno}}</td>
                    <td style="text-align: center">{{$data->amount}}</td>
                    <td style="text-align: center">
                      
                    </td>
                    <td style="text-align: center">
                      
                        <a class="btn btn-app" href="{{route('admin.programDetail', $data->id)}}">
                          <i class="fa fa-eye" style="color: #32a842;font-size:16px;"></i> View
                        </a>
                        <a class="btn btn-app"  href="{{route('admin.programEdit', $data->id)}}">
                            <i class="fas fa-edit" style="color: #2196f3;font-size:16px;"></i> Edit
                        </a>
                        <a class="btn btn-app" id="deleteBtn" rid="{{ $data->id }}">
                            <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>Delete
                        </a>

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

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      //
      var url = "{{URL::to('/admin/program-delete')}}";

    //Delete
    $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Sure?')) return;
            codeid = $(this).attr('rid');
            info_url = url + '/'+codeid;
            $.ajax({
                url:info_url,
                method: "GET",
                type: "DELETE",
                data:{
                },
                success: function(d){
                    if(d.success) {
                        swal({
                            text: "Deleted",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error:function(d){
                    // console.log(d);
                }
            });
        });
      //Delete 
    
  });
</script>
@endsection