@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-8">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">After challan receive posting program</h3>
                    </div>
                    
                    
                    <div class="card-body">
                        <div class="ermsg"> </div>
                        <form action="{{route('challanPostingVendorReportshow')}}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label for="mv_id">Mother Vassel </label>
                                            <select name="mv_id" id="mv_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($mvassels as $mvassel)
                                              <option value="{{$mvassel->id}}"  @if ($mvassel->id == $mid) selected @endif>{{$mvassel->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="ghat_id">Ghat </label>
                                            <select name="ghat_id" id="ghat_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach (\App\Models\Ghat::where('status', 1)->orderby('id', 'DESC')->get() as $ghat)
                                              <option value="{{$ghat->id}}">{{$ghat->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>


                                        <div class="form-group col-md-6">
                                            <label>Action</label> <br>
                                            <button type="submit" class="btn btn-secondary">Check</button>
                                        </div>
                                        
                                    </div>
                                </div>

                            </div>
                            
                        </form>
                    </div>
                    <div class="card-footer"> </div>
                </div>
            </div>
        </div>
    </div>
</section>


@if (isset($data))

<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Mother Vassel wise challan-vendor statement</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              @php
              if ($mid) {
                $mvesselName = \App\Models\MotherVassel::where('id', $mid)->first()->name;        
              }
              @endphp

              <h3 class="text-center">{{$mvesselName}}</h3>
              


              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Vendor</th>
                  <th>Total Challan</th>
                  <th>Challan Received</th>
                  <th>Challan Not Received</th>
                </tr>
                </thead>
                @php
                    $total_challan = 0;
                    $total_challan_received = 0;
                    $total_challan_not_received = 0;
                @endphp
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">{{$data->vendor->name}}</td>
                    <td style="text-align: center"><a href="{{route('challanPostingReport',['mid'=>$mid, 'vid' => $data->vendor->id])}}" class="btn btn-xs btn-success">{{$data->total_records}}</a></td>
                    <td style="text-align: center">{{$data->challan_received}}</td>
                    <td style="text-align: center">{{$data->challan_not_received}}</td>
                  </tr>
                  @php
                      $total_challan += $data->total_records;
                      $total_challan_received += $data->challan_received;
                      $total_challan_not_received += $data->challan_not_received;
                  @endphp
                  @endforeach
                
                </tbody>

                <tfoot>
                  <tr>
                    <th style="text-align: center"></th>
                    <th style="text-align: center">Total: </th>
                    <th style="text-align: center">{{$total_challan}}</th>
                    <th style="text-align: center">{{$total_challan_received}}</th>
                    <th style="text-align: center">{{$total_challan_not_received}}</th>
                  </tr>
                </tfoot>
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
    
@endif



@endsection

@section('script')

<script>
    // $(function () {
    //   $("#example1").DataTable({
    //     "responsive": true,
    //     "autoWidth": false,
    //   });
    // });


</script>
@endsection
