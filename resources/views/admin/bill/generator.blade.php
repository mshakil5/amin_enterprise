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
            <a href="{{route('admin.programDetail', $programId)}}" class="btn btn-secondary my-3">Back</a>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Vendor Ledger</h3>
                        
                        <div class="card-tools">
                            <a href="{{ route('export.template') }}" class="btn btn-tool">
                                <i class="fas fa-envelope"></i>
                            </a>
                      </div>
                    </div>
                    
                    <div class="card-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form action="{{route('billGeneratingStore')}}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="upload">Uploads </label>
                                            <input type="file" name="file" required>
                                            <input type="hidden" name="programId" value="{{$programId}}" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Action</label> <br>
                                            <button type="submit" class="btn btn-secondary">Upload</button>
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

<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Mother Vassel: {{$data->motherVassel->name}}</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

              @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
              @endif

              @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
              @endif

              @if($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach($errors->all() as $error)
                              {{ $error }} <br>
                          @endforeach
                      </ul>
                  </div>
              @endif

            <div style="overflow-x:auto;">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Bill Status</th>
                        <th>Bill No</th>
                        <th>Date</th>
                        <th>Vendor</th>
                        <th>Header ID</th>
                        <th>Truck Number</th>
                        <th>Challan no</th>
                        <th>Destination</th>
                        <th>Qty</th>
                        <th>Carring Bill</th>
                        <th>Advance</th>
                        <th>Fuel qty</th>
                        <th>Fuel token</th>
                        <th>Fuel Amount</th>
                        <th>Pump name</th>
                        <th>Line Charge</th>
                        <th>Scale fee</th>
                        <th>Other Cost</th>
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
                        @foreach ($data->programDetail as $key => $data)
                        <tr>
                            <td style="text-align: center">{{ $key + 1 }}</td>
                            <td style="text-align: center">

                                <label class="form-checkbox  grid layout">
                                    <input type="checkbox" class="custom-checkbox generate-bill-checkbox" name="checkbox-checked" class="custom-checkbox" data-program-detail-id="{{ $data->id }}"  @if ($data->generate_bill == 1) checked disabled @endif  />
                                </label>

                            </td>

                            @php
                                $fuelBills = $data->advancePayment->petrolPump ?? '' 
                                    ? \App\Models\FuelBill::with('petrolPump:id,name') // eager load name
                                        ->where('petrol_pump_id', $data->advancePayment->petrolPump->id)
                                        ->get(['id', 'unique_id', 'qty', 'bill_number', 'petrol_pump_id'])
                                    : collect();
                            @endphp
                            <td style="text-align: center">{{$data->bill_no}}</td>
                            <td style="text-align: center">{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y')}}</td>
                            <td style="text-align: center">{{$data->vendor->name}}</td>
                            <td style="text-align: center">{{$data->headerid}}</td>
                            <td style="text-align: center">{{strtoupper($data->truck_number)}}</td>
                            <td style="text-align: center">{{$data->challan_no}}</td>
                            <td style="text-align: center">{{$data->destination->name ?? ' '}}</td>
                            <td style="text-align: center">{{$data->dest_qty}}</td>
                            <td style="text-align: center">{{$data->carrying_bill}}</td>
                            <td style="text-align: center">{{$data->advancePayment->cashamount ?? ""}}</td>
                            <td style="text-align: center">{{$data->advancePayment->fuelqty ?? ""}}</td>
                            <td style="text-align: center">{{$data->advancePayment->fueltoken ?? ""}}</td>
                            <td style="text-align: center">{{$data->advancePayment->fuelamount ?? ""}}</td>
                            <td style="text-align: center">{{$data->advancePayment->petrolPump->name ?? ""}}</td>
                            <td style="text-align: center">{{$data->line_charge}}</td>
                            <td style="text-align: center">{{$data->scale_fee}}</td>
                            <td style="text-align: center">{{$data->other_cost}}</td>

                            @php
                                $totalfuelqty += $data->advancePayment->fuelqty ?? 0;
                                $totalcarrying_bill += $data->carrying_bill ?? 0;
                                $totaladvance += $data->advance ?? 0;
                                $totalother_cost += $data->other_cost ?? 0;
                                $totalscale_fee += $data->scale_fee ?? 0;
                                $totalline_charge += $data->line_charge ?? 0;
                                $totaldest_qty += $data->dest_qty ?? 0;
                            @endphp

                        </tr>
                        @endforeach
                    
                    </tbody>

                    <tfoot>
                        <tr>
                            <td style="text-align: center"></td>
                            <td style="text-align: center"></td>
                            <td style="text-align: center"></td>
                            <td style="text-align: center" colspan="2"><small>Total qty: </small>{{$totaldest_qty}}</td>
                            <td style="text-align: center" colspan="2"><small>Carring Bill: </small>{{$totalcarrying_bill}}</td>
                            <td style="text-align: center" colspan="2"><small>Total Advance:</small>{{$totaladvance}}</td>
                            <td style="text-align: center"><small>Fuel qty: </small>{{$totalfuelqty}}</td>
                            <td style="text-align: center"><small>Line Charge: </small>{{$totalline_charge}}</td>
                            <td style="text-align: center"><small>Scale fee: </small>{{$totalscale_fee}}</td>
                            <td style="text-align: center"><small>Other Cost: </small>{{$totalother_cost}}</td>
                            <td style="text-align: center"></td>
                            <td style="text-align: center"></td>
                            <td style="text-align: center"></td>
                            <td style="text-align: center"></td>
                            <td style="text-align: center"></td>
                            <td style="text-align: center"></td>
                            <td style="text-align: center"></td>
                        </tr>
                        <tr id="pump-form-row" style="display: none;">
                            <td colspan="15" style="text-align: center;">
                                <form id="pump-action-form" action="{{ route('bill.generate') }}" method="POST" style="display: flex; justify-content: center; align-items: center;">
                                    @csrf
                                    <input type="hidden" name="selected_ids" id="selected_ids">
                                    <input type="text" name="bill_no" id="bill_no-display" class="form-control" placeholder="Enter Bill No" style="width: 350px; margin-right: 10px;" required>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check-circle"></i> Submit Bill
                                    </button>
                                </form>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
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


@endsection

@section('script')
<script>
    $(document).ready(function () {
        let selectedBillRows = {};

        $('.generate-bill-checkbox').on('change', function () {
            const id = $(this).data('program-detail-id');

            if (this.checked) {
                selectedBillRows[id] = true;
            } else {
                delete selectedBillRows[id];
            }

            const selectedIds = Object.keys(selectedBillRows);

            if (selectedIds.length >= 1) {
                $('#pump-form-row').show();
                $('#selected_ids').val(JSON.stringify(selectedIds));
            } else {
                $('#pump-form-row').hide();
                $('#selected_ids').val('');
                $('#bill_no-display').val('');
            }
        });
    });

    setTimeout(function() {
        let alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 3000);

    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": [
        "copy", 
        "csv", 
        "excel", 
        {
          extend: 'pdf',
          customize: function (doc) {
            doc.content.splice(0, 0, {
              text: 'Program details',
              style: 'header',
              alignment: 'center'
            });
          },
          filename: 'Program_Details'
        }, 
        "print"
      ],
      "lengthMenu": [[100, "All", 50, 25], [100, "All", 50, 25]]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
@endsection
