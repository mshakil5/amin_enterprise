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
          <a href="{{route('admin.allProgram')}}" class="btn btn-secondary my-3">Back</a>
          @if ($data->bill_status == 1)
          <a href="{{route('generatingBillShow', $data->id)}}" class="btn btn-secondary my-3">Bill Show </a>
          @else
          <a href="{{route('billGenerating', $data->id)}}" class="btn btn-secondary my-3 ">Generate Bill</a>
          @endif
          <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>

          <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modal-lg">
            Vendors Advance
          </button>

          <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#quantitymodal">
            Change Quantity
          </button>
          
      </div>
    </div>
  </div>
</section>
<!-- /.content -->



<section class="content pt-3" id="addThisFormContainer">
  <div class="container-fluid">
      <div class="row justify-content-md-center">
          <div class="col-md-12">
              <div class="card card-secondary">
                  <div class="card-header">
                      <h3 class="card-title" id="cardTitle">Create new challan number</h3>
                  </div>
                  <div class="card-body">
                      <div class="ermsg">
                          
                      </div>
                      
                      <form id="createThisForm">
                          @csrf

                          <div class="row">
                              <div class="col-sm-6">
                                  
                                  <div class="form-row">
                                      <div class="form-group col-md-4">
                                          <label for="client_id">Client </label>
                                          <p><b>{{$data->client->name }}</b></p>
                                          <input type="hidden" name="program_id" id="program_id" value="{{$data->id}}">
                                      </div>
                                      <div class="form-group col-md-4">
                                          <label for="date">Date <span style="color: red;">*</span></label>
                                          <input type="date" class="form-control" id="date" name="date" value="{{$data->date}}" readonly>
                                          <span id="productCodeError" class="text-danger"></span>
                                      </div>
                                      <div class="form-group col-md-4">
                                          <label for="consignmentno">Consignment Number</label>
                                          <input type="text" class="form-control" value="{{$data->consignmentno}}" readonly>
                                      </div>
                                      
      
                                      
                                  </div>
                              </div>

          
                              <div class="col-sm-6">
                                  
                                  <div class="form-row">

                                      
      
                                      <div class="form-group col-md-4">
                                          <label for="mother_vassel_id">Mother Vassel </label>
                                          <p><b>{{$data->motherVassel->name }}</b></p>
                                      </div>

                                      <div class="form-group col-md-4">
                                          <label>Ghat </label>

                                          
                                          <p><b>  @if (isset($data->ghat_id))
                                              {{\App\Models\Ghat::where('id', $data->ghat_id)->first()->name ?? ""}}
                                          @endif </b></p>
                                          
                                      </div>

                                      <div class="form-group col-md-4">
                                          <label>New Date <span class="text-danger">*</span></label>
                                          <input type="date" name="newDate" class="form-control" value="{{ date('Y-m-d') }}" id="newDate" required>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          
                          <table class="table table-bordered" id="programTable">
                              <thead>
                                  <tr>
                                      <th>Vendor</th>
                                      <th>Truck#</th>
                                      <th>Challan</th>
                                      <th>Cash Adv</th>
                                      <th>Fuel qty</th>
                                      <th>Fuel rate</th>
                                      <th>Fuel adv</th>
                                      <th>Fuel token</th>
                                      <th>Pump</th>
                                      <th>Total</th>
                                      <th>Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td>
                                          <select class="form-control" name="vendor_id[]" id="vendor_id">
                                              <option value="">Select Vendor</option>
                                              @foreach ($vendors as $vendor)
                                              <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                                              @endforeach
                                          </select>
                                      </td>
                                      <td>
                                          <input type="text" class="form-control" name="truck_number[]" >
                                      </td>
                                      <td>
                                          <input type="number" class="form-control" name="challan_no[]" >
                                      </td>
                                      <td>
                                          <input type="number" class="form-control cashamount" name="cashamount[]" >
                                      </td>
                                      <td>
                                          <input type="number" class="form-control fuelqty" name="fuelqty[]" >
                                      </td>
                                      <td>
                                          <input type="number" class="form-control fuel_rate" name="fuel_rate[]" value="102">
                                      </td>
                                      <td> 
                                          <input type="number" class="form-control fuel_amount" name="fuel_amount[]" readonly >
                                      </td>
                                      <td>
                                          <input type="number" class="form-control" name="fueltoken[]" >
                                      </td>
                                      <td>
                                          <select name="petrol_pump_id[]" id="petrol_pump_id[]" class="form-control" >
                                              <option value="">Select</option>
                                              @foreach ($pumps as $pump)
                                                  <option value="{{$pump->id}}">{{$pump->name}}</option>
                                              @endforeach
                                              </select>
                                      </td>
                                      <td>
                                          <input type="number" class="form-control totalamount" name="amount[]" value="" readonly>
                                      </td>
                                      <td>
                                          <button type="button" class="btn btn-success add-row"><i class="fas fa-plus"></i></button>
                                      </td>
                                  </tr>

                                  <tr>
                                      <td>
                                          <select class="form-control" name="vendor_id[]" id="vendor_id">
                                              <option value="">Select Vendor</option>
                                              @foreach ($vendors as $vendor)
                                              <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                                              @endforeach
                                          </select>
                                      </td>
                                      <td>
                                          <input type="text" class="form-control" name="truck_number[]" >
                                      </td>
                                      <td>
                                          <input type="number" class="form-control" name="challan_no[]" >
                                      </td>
                                      <td>
                                          <input type="number" class="form-control cashamount" name="cashamount[]" >
                                      </td>
                                      <td>
                                          <input type="number" class="form-control fuelqty" name="fuelqty[]" >
                                      </td>
                                      <td>
                                          <input type="number" class="form-control fuel_rate" name="fuel_rate[]" value="102">
                                      </td>
                                      <td> 
                                          <input type="number" class="form-control fuel_amount" name="fuel_amount[]" readonly >
                                      </td>
                                      <td>
                                          <input type="number" class="form-control" name="fueltoken[]" >
                                      </td>
                                      <td>
                                          <select name="petrol_pump_id[]" id="petrol_pump_id[]" class="form-control" >
                                              <option value="">Select</option>
                                              @foreach ($pumps as $pump)
                                                  <option value="{{$pump->id}}">{{$pump->name}}</option>
                                              @endforeach
                                              </select>
                                      </td>
                                      <td>
                                          <input type="number" class="form-control totalamount" name="amount[]" value="" readonly>
                                      </td>
                                      <td>
                                          <button type="button" class="btn btn-danger remove-row"><i class="fas fa-minus"></i></button>
                                      </td>
                                  </tr>


                              </tbody>
                          </table>
                          

                      </form>
                  </div>
                  <div class="card-footer">
                    <button type="submit" form="createThisForm" id="addBtn"  class="btn btn-secondary">Add more challan </button>
                        <div id="loader" style="display: none;">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </div>
                        
                    <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
                  </div>



                  
              </div>
          </div>
      </div>
  </div>
</section>


<!-- Main content -->
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
                        <th>Petrol Pump</th>
                        <th>Bill No</th>
                        <th>Date</th>
                        <th>Vendor</th>
                        <th>Header ID</th>
                        <th>Truck Number</th>
                        <th>Challan no</th>
                        <th>Destination</th>
                        <th>Previous Qty</th>
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
                                    <input type="checkbox" name="checkbox-checked" class="custom-checkbox"  @if ($data->generate_bill == 1) checked @endif  />
                                </label>

                            </td>

                            @php
                                $fuelBills = $data->advancePayment->petrolPump ?? '' 
                                    ? \App\Models\FuelBill::with('petrolPump:id,name') // eager load name
                                        ->where('petrol_pump_id', $data->advancePayment->petrolPump->id)
                                        ->get(['id', 'unique_id', 'qty', 'bill_number', 'petrol_pump_id'])
                                    : collect();
                            @endphp
                            <td style="text-align: center">
                              <div style="display: flex; align-items: center; gap: 6px; justify-content: center">
                                @if($data->fuel_bill_id)
                                    <form action="{{ route('fuel.bill.undo', $data->id) }}" method="POST" onsubmit="return confirm('Uncheck this item?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-danger p-1">
                                            <i class="fa fa-undo"></i>
                                        </button>
                                    </form>
                                @endif
                                <label class="form-checkbox grid layout mb-0">
                                  <input type="checkbox" class="petrol-checkbox custom-checkbox" 
                                  data-pump-id="{{ $data->advancePayment->petrolPump->id ?? '' }}"
                                  data-fuel-bills='@json($fuelBills)'
                                  data-qty="{{ $data->advancePayment->fuelqty ?? '' }}"
                                  data-program-detail-id="{{ $data->id }}" 
                                  @if($data->fuel_bill_id) checked disabled @endif>
                                </label>
                              </div>
                            </td>
                            <td style="text-align: center">{{$data->bill_no}}</td>
                            <td style="text-align: center">{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y')}}</td>
                            <td style="text-align: center">{{$data->vendor->name}}</td>
                            <td style="text-align: center">{{$data->headerid}}</td>
                            <td style="text-align: center">{{strtoupper($data->truck_number)}}</td>
                            <td style="text-align: center">{{$data->challan_no}}</td>
                            <td style="text-align: center">{{$data->destination->name ?? ' '}}</td>
                            <td style="text-align: center">{{$data->old_qty}}</td>
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
                            <td style="text-align: center"></td>
                        </tr>
                        <tr id="pump-form-row" style="display: none;">
                          <td colspan="21" style="text-align: center;">
                              <form id="pump-action-form" action="{{ route('petrol.pump.mark.qty') }}" method="POST" style="display: flex; justify-content: center; align-items: center;">
                                  @csrf
                                  <input type="hidden" name="petrol_pump_id" id="petrol_pump_id">
                                  <input type="hidden" name="total_qty" id="total_qty">
                                  <input type="hidden" id="program_detail_ids" name="program_detail_ids">
                          
                                  <select name="unique_id" id="unique-id-display" class="form-control" style="width: 350px; margin-right: 10px;" required>
                                      <option value="">Select Unique ID</option>
                                  </select>
                          
                                  <button type="submit" class="btn btn-primary">
                                      <i class="fas fa-check-circle"></i> Submit for Petrol Pump
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
<!-- /.content -->




<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-secondary">
          <h4 class="modal-title">Mother Vessel: {{$data->motherVassel->name}}</h4>
          
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

            
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label for="searchdate">Select Date</label>
                        <select class="form-control" name="searchdate" id="searchdate">
                            <option value="">Select Date</option>
                            @foreach ($dates as $date)
                                <option value="{{ $date->date }}">{{ $date->date }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <button type="button" id="dateBtn" class="btn btn-secondary" style="margin-top: 32px;">Submit</button>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-center" id="advTitle">
                    <h2>Vendor Advance Summary</h2>
                    <h4>Details of vendor advances for the selected date</h4>
                </div>
            </div>

            <div>
                <table id="example3" class="table table-bordered table-striped">
                    <thead class="bg-secondary">
                        <tr>
                            <th style="text-align: center">SL</th>
                            <th style="text-align: center">Vendor Name</th>
                            <th style="text-align: center">Count of Advance</th>
                            <th style="text-align: center">Cash Advance</th>
                            <th style="text-align: center">Fuel Qty</th>
                            <th style="text-align: center">Fuel Advance</th>
                            <th style="text-align: center">Total Advance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vlist as $key => $data)
                            <tr>
                                <td style="text-align: center">{{ $key + 1 }}</td>
                                <td style="text-align: center">{{$data->vendor->name}}</td>
                                <td style="text-align: center">{{$data->vendor_count}}</td>
                                <td style="text-align: center">{{$data->total_cashamount  ?? ""}}</td>
                                <td style="text-align: center">{{$data->total_fuelqty  ?? ""}}</td>
                                <td style="text-align: center">{{$data->total_fuelamount  ?? ""}}</td>
                                <td style="text-align: center">{{$data->total_amount  ?? ""}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="text-align: center"></th>
                            <th style="text-align: center"><b>Total</b></th>
                            <th style="text-align: center">{{ $vlist->sum('vendor_count') }}</th>
                            <th style="text-align: center">{{ $vlist->sum('total_cashamount') }}</th>
                            <th style="text-align: center">{{ $vlist->sum('total_fuelqty') }}</th>
                            <th style="text-align: center">{{ $vlist->sum('total_fuelamount') }}</th>
                            <th style="text-align: center">{{ $vlist->sum('total_amount') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<!-- /. qty change modal -->
<div class="modal fade" id="quantitymodal">
    <div class="modal-dialog modal-xs">
      <div class="modal-content">
        <div class="modal-header bg-secondary">
          <h4 class="modal-title">Change quantity</h4>
          
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>

        </div>
        <div class="modal-body">

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="newQty">Quantity</label>
                        <input type="number" name="newQty" id="newQty" value="12" class="form-control">
                    </div>
                </div>
                <div class="col-12">
                    <button type="button" id="qtyBtn" class="btn btn-secondary">Submit</button>
                    {{-- <button type="button" id="undoBtn" class="btn btn-secondary">Undo</button> --}}
                </div>
            </div>

        </div>
        
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.qty change modal -->

@endsection
@section('script')

<script>

$(document).ready(function () {

    let selectedRows = {}; // key = program_detail_id, value = checkbox data

    let selectedPumpId = null;

    $('.petrol-checkbox').on('change', function () {
        const checkbox = $(this);
        const programDetailId = checkbox.data('program-detail-id');
        const pumpId = checkbox.data('pump-id');
        const fuelBills = checkbox.data('fuel-bills');
        const qty = parseFloat(checkbox.data('qty')) || 0;

        if (this.checked) {
            // Ensure only one pump ID is selected
            if (!selectedPumpId) {
                selectedPumpId = pumpId;
            } else if (selectedPumpId !== pumpId) {
                alert('Only same petrol pump can be selected!');
                checkbox.prop('checked', false);
                return;
            }

            selectedRows[programDetailId] = {
                pumpId: pumpId,
                fuelBills: fuelBills,
                qty: qty
            };
        } else {
            delete selectedRows[programDetailId];

            if (Object.keys(selectedRows).length === 0) {
                selectedPumpId = null;
            }
        }

        // Update UI
        const selectedCount = Object.keys(selectedRows).length;
        if (selectedCount > 0) {
            $('#pump-form-row').show();

            $('#petrol_pump_id').val(selectedPumpId);

            // Use fuelBills from the last checked checkbox (this is optional logic)
            let optionsHtml = `<option value="">Select Unique ID</option>`;
            fuelBills.forEach(fb => {
                optionsHtml += `<option value="${fb.unique_id}">
                    ${fb.unique_id} - ${fb.petrol_pump.name} - ${fb.qty}L - Bill#${fb.bill_number}
                </option>`;
            });
            $('#unique-id-display').html(optionsHtml);

            let totalQty = 0;
            const selectedIds = [];

            Object.keys(selectedRows).forEach(id => {
                totalQty += selectedRows[id].qty;
                selectedIds.push(id);
            });

            $('#total_qty').val(totalQty);
            $('#program_detail_ids').val(JSON.stringify(selectedIds));
            console.log(selectedIds);
        } else {
            $('#pump-form-row').hide();
            $('#unique-id-display').empty();
            $('#total_qty').val('');
            $('#program_detail_ids').val('');
        }
    });

    $('#example1').on('draw.dt', function () {
        $('.petrol-checkbox').each(function () {
            const programId = $(this).data('program-detail-id');
            if (selectedRows[programId]) {
                $(this).prop('checked', true);
            }
        });
    });
});

</script>

{{-- <script>
  $(document).ready(function () {
      let selectedPumpId = null;

      $('.petrol-checkbox').on('change', function () {
            const currentPumpId = $(this).data('pump-id');
            const fuelBills = $(this).data('fuel-bills'); 
            console.log(fuelBills);

            if (this.checked) {
                if (!selectedPumpId) {
                    selectedPumpId = currentPumpId;
                }

                if (selectedPumpId !== currentPumpId) {
                    alert('Only same petrol pump can be selected!');
                    $(this).prop('checked', false);
                    return;
                }
            } else {
                if ($('.petrol-checkbox:checked').length === 0) {
                    selectedPumpId = null;
                }
            }

            const checkedBoxes = $('.petrol-checkbox:checked');
            if (checkedBoxes.length > 0) {
                $('#pump-form-row').show();
                $('#petrol_pump_id').val(selectedPumpId);

                let optionsHtml = `<option value="">Select Unique ID</option>`;
                fuelBills.forEach(fb => {
                    optionsHtml += `<option value="${fb.unique_id}">
                        ${fb.unique_id} - ${fb.petrol_pump.name} - ${fb.qty}L - Bill#${fb.bill_number}
                    </option>`;
                });
                $('#unique-id-display').html(optionsHtml);

                let totalQty = 0;
                selectedProgramDetailIds = [];
                checkedBoxes.each(function () {
                    totalQty += parseFloat($(this).data('qty')) || 0;
                    const progId = $(this).data('program-detail-id');
                    if (progId) selectedProgramDetailIds.push(progId);
                });

                $('#total_qty').val(totalQty);
                $('#program_detail_ids').val(JSON.stringify(selectedProgramDetailIds));
            } else {
                $('#pump-form-row').hide();
                $('#unique-id-display').empty();
                $('#total_qty').val('');
                $('#program_detail_ids').val('');
            }
        });

  });
</script> --}}
  
<script>
    $(function () {
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


    });
</script>



  <!-- Dynamic Row Script -->
  <script>
    $(document).ready(function() {
      // calculation
      
      function updateSummary() {
          
              var itemTotalAmount = 0;
              var totalVatAmount = 0;
  
              $('#programTable tbody tr').each(function() {
                  var fuelqty = parseFloat($(this).find('input.fuelqty').val()) || 0;
                  var fuel_rate = parseFloat($(this).find('input.fuel_rate').val()) || 0;
                  var cashamount = parseFloat($(this).find('input.cashamount').val()) || 0;
  
                  var totalPrice = (fuelqty * fuel_rate).toFixed(2);
                  var totaladvance = (parseFloat(totalPrice) + parseFloat(cashamount)).toFixed(2);
  
                  $(this).find('input.fuel_amount').val(totalPrice);
                  $(this).find('input.totalamount').val(totaladvance);
  
                  itemTotalAmount += parseFloat(totaladvance) || 0;
              });
  
              // $('#item_total_amount').val(itemTotalAmount.toFixed(2) || '0.00');
          }
  
  
  
  
        $(document).on('click', '.add-row', function() {
            let newRow = `
                          <tr>
                              <td>
                                  <select class="form-control" name="vendor_id[]" id="vendor_id">
                                      <option value="">Select Vendor</option>
                                      @foreach ($vendors as $vendor)
                                      <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                                      @endforeach
                                  </select>
                              </td>
                              <td>
                                  <input type="text" class="form-control" name="truck_number[]" >
                              </td>
                              <td>
                                  <input type="number" class="form-control" name="challan_no[]" >
                              </td>
                              <td>
                                  <input type="number" class="form-control cashamount" name="cashamount[]" >
                              </td>
                              <td>
                                  <input type="number" class="form-control fuelqty" name="fuelqty[]" >
                              </td>
                              <td>
                                  <input type="number" class="form-control fuel_rate" name="fuel_rate[]" value="102">
                              </td>
                              <td> 
                                  <input type="number" class="form-control fuel_amount" name="fuel_amount[]" readonly >
                              </td>
                              <td>
                                  <input type="number" class="form-control" name="fueltoken[]" >
                              </td>
                              <td>
                                  <select name="petrol_pump_id[]" id="petrol_pump_id[]" class="form-control" >
                                      <option value="">Select</option>
                                      @foreach ($pumps as $pump)
                                          <option value="{{$pump->id}}">{{$pump->name}}</option>
                                      @endforeach
                                      </select>
                              </td>
                              <td>
                                  <input type="number" class="form-control totalamount" name="amount[]" value="" readonly>
                              </td>
                              <td>
                                  <button type="button" class="btn btn-danger remove-row"><i class="fas fa-minus"></i></button>
                              </td>
                          </tr>`;
  
            $('#programTable tbody').append(newRow);
        });
  
  
          $(document).on('click', '.remove-row', function() {
              $(this).closest('tr').remove();
              updateSummary();
          });
  
          $(document).on('input', '#programTable input.fuelqty, #programTable input.fuel_rate, #programTable input.cashamount', function() {
              updateSummary();
          });
  
  
        
    });
  </script>


<script>
  $(document).ready(function () {

    
    $("#addThisFormContainer").hide();
      $("#newBtn").click(function(){
          $("#newBtn").hide(100);
          $("#addThisFormContainer").show(300);

      });
      $("#FormCloseBtn").click(function(){
          $("#addThisFormContainer").hide(200);
          $("#newBtn").show(100);
      });


    //
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    //


    // dateBtn search
    $('#dateBtn').click(function() {
        var selectedDate = $('#searchdate').val();
        var program_id = $('#program_id').val();
        // console.log(selectedDate,  program_id );
        if (selectedDate) {
            $.ajax({
                url: '{{ route("getAdvancePayments") }}',
                method: 'POST',
                data: { date: selectedDate, program_id: program_id },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response) 
                    $('#advTitle').html(`
                        <h2>Vendor Advance Summary</h2>
                        <h4>Mother Vessel: ${response.program.mother_vassel.name}</h4>
                        <h4>Details of vendor advances for the selected date: ${selectedDate}</h4>
                    `);

                    // Destroy the old DataTable instance
                    if ($.fn.DataTable.isDataTable('#example3')) {
                        $('#example3').DataTable().destroy();
                    }


                    // Process the response and update the table
                    var tbody = $('#example3 tbody');
                    tbody.empty();
                    $.each(response.data, function(index, payment) {
                        var row = `<tr>
                            <td style="text-align: center">${index + 1}</td>
                            <td style="text-align: center">${payment.vendor?.name ?? ''}</td>
                            <td style="text-align: center">${payment.vendor_count}</td>
                            <td style="text-align: center">${payment.total_cashamount}</td>
                            <td style="text-align: center">${payment.total_fuelqty}</td>
                            <td style="text-align: center">${payment.total_fuelamount}</td>
                            <td style="text-align: center">${payment.total_amount}</td>
                        </tr>`;
                        tbody.append(row);
                    });

                    var tfoot = $('#example3 tfoot');
                    tfoot.empty();
                    var totalRow = `<tr>
                        <th style="text-align: center"></th>
                        <th style="text-align: center"><b>Total</b></th>
                        <th style="text-align: center">${response.data.reduce((sum, payment) => sum + payment.vendor_count, 0)}</th>
                        <th style="text-align: center">${response.data.reduce((sum, payment) => sum + payment.total_cashamount, 0)}</th>
                        <th style="text-align: center">${response.data.reduce((sum, payment) => sum + payment.total_fuelqty, 0)}</th>
                        <th style="text-align: center">${response.data.reduce((sum, payment) => sum + payment.total_fuelamount, 0)}</th>
                        <th style="text-align: center">${response.data.reduce((sum, payment) => sum + payment.total_amount, 0)}</th>
                    </tr>`;
                    tfoot.append(totalRow);


                    // Re-initialize DataTable
                    $('#example3').DataTable({
                        "responsive": true,
                        "lengthChange": false,
                        "autoWidth": false,
                        "destroy": true,
                        "buttons": [
                            {
                                extend: 'copy',
                                title: 'Vendor Advance Summary'
                            },
                            {
                                extend: 'csv',
                                title: 'Vendor Advance Summary',
                                footer: true,
                            },
                            {
                                extend: 'excel',
                                title: 'Vendor Advance Summary',
                                footer: true
                            },
                            {
                                extend: 'pdf',
                                title: `Mother Vessel: ${response.program.mother_vassel.name}`,
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
                                title: `Mother Vessel: ${response.program.mother_vassel.name}`,
                                customize: function (win) {
                                    $(win.document.body).prepend(
                                        '<h1 style="text-align:center;">Vendor Advance Summary</h1>'
                                    );
                                }
                            }
                        ],
                        "lengthMenu": [[100, "All", 50, 25], [100, "All", 50, 25]]
                    }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');




                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseJSON.message);
                }
            });
        }
    });

    // change qty
    $('#qtyBtn').click(function() {

        $(this).attr('disabled', true);
          $('#loader').show();
        
        var newQty = $('#newQty').val();
        var program_id = $('#program_id').val();

        if (!newQty) {
            alert('Quantity is required');
            return;
        }

        $.ajax({
            url: '{{ route("changeQuantity") }}',
            method: 'POST',
            data: { newQty: newQty, program_id: program_id },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                
                $(this).attr('disabled', false);
                $('#loader').hide();
                console.log(response);

                if (response.status == 200) {
                    alert('Quantity updated successfully');
                    location.reload();
                } else {
                    alert('Failed to update quantity');
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseJSON.message);
            }
        });



    });

    $('#undoBtn').click(function() {
            $(this).attr('disabled', true);
            $('#loader').show();
        var program_id = $('#program_id').val();

        $.ajax({
            url: '{{ route("undoChangeQuantity") }}',
            method: 'POST',
            data: {program_id: program_id},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status == 200) {
                    alert('Quantity updated successfully');
                    location.reload();
                } else {
                    alert('Failed to update quantity');
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseJSON.message);
            }
        });



    });
    


          


  });
</script>

<!-- Create Program Start -->
<script>
  $(document).ready(function() {
      $(document).on('click', '#addBtn', function(e) {
          e.preventDefault();

          $(this).attr('disabled', true);
          $('#loader').show();

          var formData = new FormData($('#createThisForm')[0]);

          $.ajax({
              url: '{{ route("addMoreChallan") }}',
              method: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              cache: false,
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response) {
                  if (response.status == 400) {
                      $(".ermsg").html(response.message);
                  } else {
                      $(".ermsg").html(response.message);
                      window.setTimeout(function(){location.reload()},2000)
                  }
              },
              error: function(xhr, status, error) {
                  console.log(xhr.responseJSON.message);
              },
              complete: function() {
                  $('#loader').hide();
                  $('#addBtn').attr('disabled', false);
              }
          });
      });

  });
</script>
<!-- Create Program End -->

@endsection