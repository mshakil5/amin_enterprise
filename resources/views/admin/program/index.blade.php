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
                  <th>Bill Generate</th>
                  <th>Vendor</th>
                  <th>Total Challan</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y')}}</td>
                    <td style="text-align: center">
                      @if (isset($data->client_id))
                      {{$data->client->name}}
                      @endif
                    </td>
                    <td style="text-align: center">{{$data->programid}}</td>
                    <td style="text-align: center">
                      @if (isset($data->mother_vassel_id))
                        {{$data->motherVassel->name}}
                      @endif
                    </td>
                    <td style="text-align: center">
                      @if ($data->lighter_vassel_id)
                        {{$data->lighterVassel->name}}
                      @endif
                    </td>
                    <td style="text-align: center">{{$data->consignmentno}}</td>
                    <td style="text-align: center">
                      <a href="{{route('billGenerating', $data->id)}}" type="button" class="btn btn-block btn-info btn-xs">Generate Bill</a>

                      @if ($data->bill_status == 1)
                        <a href="{{route('generatingBillShow', $data->id)}}" class="btn btn-block btn-success btn-xs">Bill Show </a>
                      @endif

                    </td>
                    <td style="text-align: center">
                      <a href="{{route('admin.programVendorList', $data->id)}}" type="button" class="btn btn-block btn-info btn-xs">Vendor</a>
                    </td>
                    <td style="text-align: center">
                      
                      <a class="btn btn-block btn-info btn-xs" href="{{route('admin.programDetail', $data->id)}}">
                        <span>Total Challan-{{$data->unique_challan_count}}</span>
                      </a>

                      @if ($data->generate_bill_count > 0)
                        <span class="btn btn-success btn-xs">{{$data->generate_bill_count}} </span>
                      @endif
                      @if ($data->not_generate_bill_count > 0)
                        <span class="btn btn-danger btn-xs">{{$data->not_generate_bill_count}} </span>
                      @endif

                    </td>
                    <td style="text-align: center">
                      
                        
                        <a class="btn btn-app"  href="{{route('admin.programEdit', $data->id)}}">
                            <i class="fas fa-edit" style="color: #2196f3;font-size:16px;"></i> Edit
                        </a>
                        {{-- <a class="btn btn-app" id="deleteBtn" rid="{{ $data->id }}">
                            <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>Delete
                        </a> --}}

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