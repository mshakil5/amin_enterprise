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
          <a href="{{route('challanPostingVendorReport')}}" class="btn btn-secondary my-3">Back</a>

          
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
                <h4 class="text-center">{{$vendor->name}}</h4>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-warning">
                        {{ session('error') }}
                    </div>
                @endif

                @php
                    $totalcarrying_bill = $data->sum('carrying_bill');
                    $totaladvance = $data->sum('advance');  
                    $totalDue = $totalcarrying_bill - $totaladvance - $duePaymentTransaction;    
                @endphp

                @if ($duePaymentTransaction != null && $duePaymentTransaction > 0)
                  <button type="button" class="btn btn-success mb-3">
                    Due Payment Paid: {{ number_format($duePaymentTransaction, 2) }}
                </button>
                @endif
                @if ($totalDue > 0)
                <button type="button" class="btn btn-warning mb-3" data-toggle="modal" data-target="#duePaymentModal">
                    Due Payment: {{ number_format($totalDue, 2) }}
                </button>
                @endif

                <form action="{{ route('due.payment.store') }}" method="POST">
                  @csrf
                  <div class="modal fade" id="duePaymentModal" tabindex="-1" role="dialog" aria-labelledby="duePaymentModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header bg-warning">
                          <h5 class="modal-title">Due Payment</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <h4>Total Due: <strong>{{ number_format($totalDue, 2) }} Tk</strong></h4>
                          <input type="text" name="comment" class="form-control mb-3" placeholder="Enter comment" required>
                          <input type="hidden" name="due_amount" value="{{ $totalDue }}">
                          <input type="hidden" name="mother_vessel_id" value="{{ $mid }}">
                          <input type="hidden" name="vendor_id" value="{{ $vid }}">
                          <input type="hidden" name="client_id" value="{{ optional($data->first())->client_id }}">
                          <p>Note: This due payment from vendors wallet. </p>
                          <p>Note: Vendors avaiable balance: <b>{{$vendor->balance}}TK</b></p>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-warning">Pay</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>                

              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Date</th>
                  <th>Vendor</th>
                  <th>Header ID</th>
                  <th>Truck Number</th>
                  <th>Challan no</th>
                  <th>Ghat</th>
                  <th>Destination</th>
                  <th>Qty</th>
                  <th>Carring Bill</th>
                  <th>Line Charge</th>
                  <th>Scale fee</th>
                  <th>Other Cost</th>
                  <th>Advance</th>
                  <th>Adv. Fuel</th>
                  <th>Action</th>
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
                  @foreach ($data as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y')}}</td>
                    <td style="text-align: center">{{$data->vendor->name}}</td>
                    <td style="text-align: center">{{$data->headerid}}</td>
                    <td style="text-align: center">{{strtoupper($data->truck_number)}}</td>
                    <td style="text-align: center">{{$data->challan_no}}</td>
                    <td style="text-align: center">{{$data->ghat->name ?? ' '}}</td>
                    <td style="text-align: center">{{$data->destination->name ?? ' '}}</td>
                    <td style="text-align: center">{{$data->dest_qty}}</td>
                    <td style="text-align: center">{{$data->carrying_bill}}</td>
                    <td style="text-align: center">{{$data->line_charge}}</td>
                    <td style="text-align: center">{{$data->scale_fee}}</td>
                    <td style="text-align: center">{{$data->other_cost}}</td>
                    <td style="text-align: center">{{$data->advance}}</td>
                    <td style="text-align: center">{{$data->advancePayment->fuelqty}}</td>
                    <td style="text-align: center">
                      <a href="{{route('admin.programDetailsEdit', $data->id)}}" class="btn btn-info btn-xs view-btn">Edit</a>
                        <form action="{{ route('programDetails.delete', $data->id) }}" method="POST" style="display: inline;" class="d-none">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                        </form>
                    </td>

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
                        <td style="text-align: center"></td>
                        <td style="text-align: center"></td>
                        <th style="text-align: center">Total:</th>
                        <th style="text-align: center">{{$totaldest_qty}}</th>
                        <th style="text-align: center">{{$totalcarrying_bill}}</th>
                        <th style="text-align: center">{{$totalline_charge}}</th>
                        <th style="text-align: center">{{$totalscale_fee}}</th>
                        <th style="text-align: center">{{$totalother_cost}}</th>
                        <th style="text-align: center">{{$totaladvance}}</th>
                        <th style="text-align: center">{{$totalfuelqty}}</th>
                        <td style="text-align: center"></td>
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



<!-- Main content -->
@if (isset($missingHeaderIds) && $missingHeaderIds->count() > 0)  
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-danger">
            <div class="card-header">
              <h3 class="card-title">Without Header Ids</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <h3 class="text-center">{{$motherVesselName}}</h3>
                <h4 class="text-center">{{$vendor->name}}</h4>


              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Date</th>
                  <th>Vendor</th>
                  <th>Header ID</th>
                  <th>Truck Number</th>
                  <th>Challan no</th>
                  <th>Ghat</th>
                  <th>Destination</th>
                  <th>Qty</th>
                  <th>Carring Bill</th>
                  <th>Line Charge</th>
                  <th>Scale fee</th>
                  <th>Other Cost</th>
                  <th>Advance</th>
                  <th>Adv. Fuel</th>
                  <th>Action</th>
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
                  @foreach ($missingHeaderIds as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y')}}</td>
                    <td style="text-align: center">{{$data->vendor->name}}</td>
                    <td style="text-align: center">{{$data->headerid}}</td>
                    <td style="text-align: center">{{strtoupper($data->truck_number)}}</td>
                    <td style="text-align: center">{{$data->challan_no}}</td>
                    <td style="text-align: center">{{$data->ghat->name ?? ' '}}</td>
                    <td style="text-align: center">{{$data->destination->name ?? ' '}}</td>
                    <td style="text-align: center">{{$data->dest_qty}}</td>
                    <td style="text-align: center">{{$data->carrying_bill}}</td>
                    <td style="text-align: center">{{$data->line_charge}}</td>
                    <td style="text-align: center">{{$data->scale_fee}}</td>
                    <td style="text-align: center">{{$data->other_cost}}</td>
                    <td style="text-align: center">{{$data->advance}}</td>
                    <td style="text-align: center">{{$data->advancePayment->fuelqty}}</td>

                    <td style="text-align: center">
                      <a href="{{route('admin.programDetailsEdit', $data->id)}}" class="btn btn-info btn-xs view-btn">Edit</a>
                      <form action="{{ route('programDetails.delete', $data->id) }}" method="POST" style="display: inline;">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure you want to delete this record?')">Delete</button>
                      </form>
                    </td>
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
                        <td style="text-align: center"></td>
                        <td style="text-align: center"></td>
                        <th style="text-align: center">Total:</th>
                        <th style="text-align: center">{{$totaldest_qty}}</th>
                        <th style="text-align: center">{{$totalcarrying_bill}}</th>
                        <th style="text-align: center">{{$totalline_charge}}</th>
                        <th style="text-align: center">{{$totalscale_fee}}</th>
                        <th style="text-align: center">{{$totalother_cost}}</th>
                        <th style="text-align: center">{{$totaladvance}}</th>
                        <th style="text-align: center">{{$totalfuelqty}}</th>
                        <td style="text-align: center"></td>
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
@endif  
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