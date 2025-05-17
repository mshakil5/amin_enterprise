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

              @php
                  
                  $reports = \App\Models\ProgramDetail::with('programDestination','programDestination.destinationSlabRate')->where('mother_vassel_id', $id)->whereNotNull('headerid')->get();
                    $reports = $reports->groupBy(function($item) {
                        return $item->created_at->format('Y-m-d');
                    });
              @endphp 

                <h3 class="text-center">{{$motherVesselName}}</h3>
                <table id="example3" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>view</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $date => $reportGroup)
                          <tr>
                            <td>{{ $date }}</td>
                            <td>
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#reportModal{{ $date }}">
                              View in Modal
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="reportModal{{ $date }}" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel{{ $date }}" aria-hidden="true">
                              <div class="modal-dialog modal-xl" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title" id="reportModalLabel{{ $date }}">Report for {{ $date }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                                </div>
                                <div class="modal-body">
                                <!-- Include the report details here -->
                                <table id="example1" class="table table-bordered table-striped">
                                  <thead>
                                  <tr>
                                  <th>Sl</th>
                                  <th>Vendor</th>
                                  <th>Truck Number</th>
                                  <th>Challan no</th>
                                  <th>Destination</th>
                                  <th>Qty</th>
                                  <th>Carring Bill</th>
                                  <th>Line Charge</th>
                                  <th>Scale fee</th>
                                  <th>Other Cost</th>
                                  <th>Advance</th>
                                  <th>Adv. Fuel</th>
                                  {{-- <th>Action</th> --}}
                                  </tr>
                                  </thead>
                                  <tbody>
                                    @php
                                      $totalfuelqty = 0;
                                      $totalcarrying_bill = 0;
                                      $totaladvance = 0;
                                      $totalother_cost = 0;
                                      $totalscale_fee = 0;
                                      $totalline_charge = 0;
                                      $totaldest_qty = 0;
                                    @endphp
                                  @foreach ($reportGroup as $key => $data)
                                  <tr>
                                    <td style="text-align: center">{{ $key + 1 }}</td>
                                    <td style="text-align: center">{{$data->vendor->name}}</td>
                                    <td style="text-align: center">{{strtoupper($data->truck_number)}}</td>
                                    <td style="text-align: center">{{$data->challan_no}}</td>
                                    <td style="text-align: center">{{$data->destination->name ?? ' '}}</td>
                                    <td style="text-align: center">{{$data->dest_qty}}</td>
                                    <td style="text-align: center">{{$data->carrying_bill}}</td>
                                    <td style="text-align: center">{{$data->line_charge}}</td>
                                    <td style="text-align: center">{{$data->scale_fee}}</td>
                                    <td style="text-align: center">{{$data->other_cost}}</td>
                                    <td style="text-align: center">{{$data->advance}}</td>
                                    <td style="text-align: center">{{$data->advancePayment->fuelqty}}</td>
                          
                                    @php
                                      $totalfuelqty += $data->advancePayment->fuelqty;
                                      $totalcarrying_bill += $data->carrying_bill;
                                      $totaladvance += $data->advance;
                                      $totalother_cost += $data->other_cost;
                                      $totalscale_fee += $data->scale_fee;
                                      $totalline_charge += $data->line_charge;
                                      $totaldest_qty += $data->dest_qty;
                                    @endphp
                          
                                  </tr>
                                  @endforeach
                                  
                                  </tbody>
                          
                                  <tfoot>
                                    <tr>
                                      <td style="text-align: center"></td>
                                      <td style="text-align: center"></td>
                                      <td style="text-align: center"></td>
                                      <td style="text-align: center"></td>
                                      <td style="text-align: center"></td>
                                      <td style="text-align: center">{{$totaldest_qty}}</td>
                                      <td style="text-align: center">{{$totalcarrying_bill}}</td>
                                      <td style="text-align: center">{{$totalline_charge}}</td>
                                      <td style="text-align: center">{{$totalscale_fee}}</td>
                                      <td style="text-align: center">{{$totalother_cost}}</td>
                                      <td style="text-align: center">{{$totaladvance}}</td>
                                      <td style="text-align: center">{{$totalfuelqty}}</td>
                                    </tr>
                                  </tfoot>
                                </table>
                                
                                </div>
                                <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                              </div>
                              </div>
                            </div>
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