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
                  <th>Ghat</th>
                  <th>Consignment No.</th>    
                  <th>Log</th>
                  <th>Bill Generate</th>
                  <th>Vendor</th>
                  <th>Total Challan</th>
                  <th>Deleted</th>
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
                      {{$data->client->name ?? ""}}
                      @endif
                    </td>
                    <td style="text-align: center">{{$data->programid}}</td>
                    <td style="text-align: center">
                      @if (isset($data->mother_vassel_id))
                        {{$data->motherVassel->name ?? ""}}
                      @endif
                    </td>
                    <td style="text-align: center">
                      @if ($data->lighter_vassel_id)
                        {{$data->lighterVassel->name ?? ""}}
                      @endif
                    </td>
                    <td style="text-align: center">
                      @if ($data->ghat_id)
                        {{$data->ghat->name ?? ""}}
                      @endif
                    </td>
                    <td style="text-align: center">{{$data->consignmentno ?? ""}}</td>
                    <td style="text-align: center">
                      <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#logModal_{{ $data->id }}">
                        Log
                      </button>

                      <div class="modal fade" id="logModal_{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="logModalLabel_{{ $data->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                          <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                              <h5 class="modal-title" id="logModalLabel_{{ $data->id }}">Log Details (Program ID: {{ $data->id }})</h5>
                              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between">
                                  <span>Total Challan:</span>
                                  <strong>
                                    {{ ($data->generate_bill_count + $data->not_generate_bill_count) ?? 0 }}
                                  </strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                  <span>Bill Generated Challan:</span>
                                  <a href="{{ route('admin.programDetail', [$data->id, 'bill_generated']) }}" class="btn btn-warning btn-xs">
                                  <strong>{{ $data->generate_bill_count ?? 0 }}</strong>
                                  </a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                  <span>Bill Not Generated Challan:</span>
                                  <a href="{{ route('admin.programDetail', [$data->id, 'bill_not_generated']) }}" class="btn btn-warning btn-xs">
                                  <strong>{{ $data->not_generate_bill_count ?? 0 }}</strong>
                                  </a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                  <span>Total After Challan Posting:</span>
                                  <a href="{{ route('admin.programDetail', [$data->id, 'after_challan']) }}" class="btn btn-warning btn-xs">
                                  <strong>{{ $data->after_challan_posting_count ?? 0 }}</strong>
                                  </a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                  <span>Total Before Challan Posting:</span>
                                  <a href="{{ route('admin.programDetail', [$data->id, 'before_challan']) }}" class="btn btn-warning btn-xs">
                                  <strong>{{ $data->before_challan_count ?? 0 }}</strong>
                                  </a>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                  {{-- <a href="{{ route('challanPostingVendorReportshow') }}" class="btn btn-warning btn-xs">
                                  <strong>{{ $data->before_challan_count ?? 0 }}</strong>
                                  </a> --}}

                                    <span>Vendor Wise Challan Posting:</span>
                                  <form action="{{ route('challanPostingVendorReportshow') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="mv_id" value="{{$data->mother_vassel_id}}">
                                    <button type="submit" class="btn btn-warning btn-xs">Check</button>
                                  </form>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                  <span>Not twelve MT count:</span>
                                  <a href="{{ route('admin.programDetail', [$data->id, 'twelve_mt']) }}" class="btn btn-warning btn-xs">
                                  <strong>{{ $data->not_twelve_mt ?? 0 }}</strong>
                                  </a>
                                </li>
                                

                                <li class="list-group-item d-flex justify-content-between">
                                  <span>Total Petrol Pump:</span>
                                  <strong>{{ $data->pump_count ?? 0 }}</strong>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td style="text-align: center">
                      <a href="{{route('billGenerating', $data->id)}}" type="button" class="btn btn-block btn-info btn-xs">Generate Bill</a>

                      @if ($data->generate_bill_count > 0)
                          <a href="{{ route('bill.generated', $data->id) }}" class="badge badge-success" style="font-size: 12px;">
                              {{ $data->generate_bill_count }}
                          </a>
                      @endif
                      @if ($data->not_generate_bill_count > 0)
                          <a href="{{ route('bill.not.generated', $data->id) }}" class="badge badge-danger" style="font-size: 12px;">
                              {{ $data->not_generate_bill_count }}
                          </a>
                      @endif
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
                        <span class="btn btn-success btn-xs d-none">{{$data->generate_bill_count}} </span>
                      @endif
                      @if ($data->not_generate_bill_count > 0)
                        <span class="btn btn-danger btn-xs d-none">{{$data->not_generate_bill_count}} </span>
                      @endif

                    </td>
                    <td style="text-align: center">
                      <a class="btn btn-block btn-info btn-xs" href="{{route('admin.deletedProgramDetail', $data->id)}}">
                        <span>Deleted-{{$data->deleted_count }}</span>
                      </a>
                      @if ($data->deleted_count > 0)
                        <span class="btn btn-danger btn-xs">{{$data->deleted_count }} </span>
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