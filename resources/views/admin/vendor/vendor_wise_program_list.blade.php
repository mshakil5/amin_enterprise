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
          <a href="{{route('admin.vendor')}}" class="btn btn-secondary my-3">Back</a>

          
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
              <h3 class="card-title">Vendor trip list (Sequence wise)</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

              

              @php
                  $totalfuelamount = 0;
                  $totalfuelqty = 0;
                  $totalcarrying_bill = 0;
                  $totaladvance = 0;
                  $totalother_cost = 0;
                  $totalscale_fee = 0;
                  $totalline_charge = 0;
                  $totaldest_qty = 0;
              @endphp


              @foreach ($data as $motherVassel => $pdtl)

              <div style="text-align: center; margin-bottom: 20px;">
                <h4>Vendor: {{ $vendor->name ?? 'N/A' }}</h4>
                <h5>Sequence Number: {{ $vendorSequenceNumber->unique_id ?? 'N/A' }}</h5>
                <h5>Mother Vessel: {{ $motherVassel ?? 'N/A' }}</h5>
              </div>

              <table class="table table-bordered table-striped datatable">
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
                    <th>Qty</th>
                    <th>Carring Bill</th>
                    <th>Line Charge</th>
                    <th>Scale fee</th>
                    <th>Other Cost</th>
                    <th>Cash Advance</th>
                    <th>Fuel qty</th>
                    <th>Fuel Amount</th>
                    <th>Fuel token</th>
                    <th>Pump name</th>
                    {{-- <th>Action</th> --}}
                </tr>
                </thead>
                <tbody>
                    @foreach ($pdtl as $key => $data)
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
                            <label class="form-checkbox grid layout">
                              <input type="checkbox" class="petrol-checkbox custom-checkbox" 
                              data-pump-id="{{ $data->advancePayment->petrolPump->id ?? '' }}"
                              data-fuel-bills='@json($fuelBills)'
                              data-qty="{{ $data->advancePayment->fuelqty ?? '' }}"
                              data-program-detail-id="{{ $data->id }}" 
                              @if($data->fuel_bill_id) checked disabled @endif>
                            </label>
                        </td>
                        <td style="text-align: center">{{$data->bill_no}}</td>
                        <td style="text-align: center">{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y')}}</td>
                        <td style="text-align: center">{{$data->vendor->name}}</td>
                        <td style="text-align: center">{{$data->headerid}}</td>
                        <td style="text-align: center">{{strtoupper($data->truck_number)}}</td>
                        <td style="text-align: center">{{$data->challan_no}}</td>
                        <td style="text-align: center">{{$data->destination->name ?? ' '}}</td>
                        <td style="text-align: center">{{$data->dest_qty}}</td>
                        <td style="text-align: center">{{$data->carrying_bill}}</td>
                        <td style="text-align: center">{{$data->line_charge}}</td>
                        <td style="text-align: center">{{$data->scale_fee}}</td>
                        <td style="text-align: center">{{$data->other_cost}}</td>
                        <td style="text-align: center">{{$data->advancePayment->cashamount ?? ""}}</td>
                        <td style="text-align: center">{{$data->advancePayment->fuelqty ?? ""}}</td>
                        <td style="text-align: center">{{$data->advancePayment->fuelamount ?? ""}}</td>
                        <td style="text-align: center">{{$data->advancePayment->fueltoken ?? ""}}</td>
                        <td style="text-align: center">{{$data->advancePayment->petrolPump->name ?? ""}}</td>

                        @php
                            $totalfuelamount += $data->advancePayment->fuelamount ?? 0;
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
                        <td style="text-align: center"></td>
                        <td style="text-align: center"></td>
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
                        <td style="text-align: center">{{$totaladvance - $totalfuelamount}}</td>
                        <td style="text-align: center">{{$totalfuelqty}}</td>
                        <td style="text-align: center">{{$totalfuelamount}}</td>
                        <td style="text-align: center"><b>Total adv:</b></td>
                        <td style="text-align: center"><b>{{$totaladvance}}</b></td>
                    </tr>
                    <tr>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"  colspan="8">
                          <strong>Total Vendor's Payable: {{$totalcarrying_bill + $totalscale_fee - $totaladvance}}</strong>
                      </td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                  </tr>
                </tfoot>
              </table>



              @endforeach

              
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
<section class="content mt-3" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Vendor trip list (Sequence wise all data ) </h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

              

             



              <div style="text-align: center; margin-bottom: 20px;">
                <h4>Vendor: {{ $vendor->name ?? 'N/A' }}</h4>
                <h5>Sequence Number: {{ $vendorSequenceNumber->unique_id ?? 'N/A' }}</h5>
              </div>

              <table class="table table-bordered table-striped datatable">
                <thead>
                <tr>
                    <th>Sl</th>
                    <th>Petrol Pump</th>
                    <th>Date</th>
                    <th>Vendor</th>
                    <th>Header ID</th>
                    <th>Truck Number</th>
                    <th>Challan no</th>
                    <th>Mother Vessel</th>
                    <th>Destination</th>
                    <th>Qty</th>
                    <th>Carring Bill</th>
                    <th>Line Charge</th>
                    <th>Scale fee</th>
                    <th>Other Cost</th>
                    <th>Cash Advance</th>
                    <th>Fuel qty</th>
                    <th>Fuel Amount</th>
                    <th>Fuel token</th>
                    <th>Pump name</th>
                    {{-- <th>Action</th> --}}
                </tr>
                </thead>
                <tbody>

                     @php
                        $alltotalfuelamount = 0;
                        $alltotalfuelqty = 0;
                        $alltotalcarrying_bill = 0;
                        $alltotaladvance = 0;
                        $alltotalother_cost = 0;
                        $alltotalscale_fee = 0;
                        $alltotalline_charge = 0;
                        $alltotaldest_qty = 0;
                    @endphp


                    @foreach ($alldata as $key => $data)
                    <tr>
                        <td style="text-align: center">{{ $key + 1 }}</td>

                        @php
                            $fuelBills = $data->advancePayment->petrolPump ?? '' 
                                ? \App\Models\FuelBill::with('petrolPump:id,name') // eager load name
                                    ->where('petrol_pump_id', $data->advancePayment->petrolPump->id)
                                    ->get(['id', 'unique_id', 'qty', 'bill_number', 'petrol_pump_id'])
                                : collect();
                        @endphp
                        <td style="text-align: center">
                            <label class="form-checkbox grid layout">
                              <input type="checkbox" class="petrol-checkbox custom-checkbox" 
                              data-pump-id="{{ $data->advancePayment->petrolPump->id ?? '' }}"
                              data-fuel-bills='@json($fuelBills)'
                              data-qty="{{ $data->advancePayment->fuelqty ?? '' }}"
                              data-program-detail-id="{{ $data->id }}" 
                              @if($data->fuel_bill_id) checked disabled @endif>
                            </label>
                        </td>
                        <td style="text-align: center">{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y')}}</td>
                        <td style="text-align: center">{{$data->vendor->name ?? ''}}</td>
                        <td style="text-align: center">{{$data->headerid}}</td>
                        <td style="text-align: center">{{strtoupper($data->truck_number)}}</td>
                        <td style="text-align: center">{{$data->challan_no}}</td>
                        <td style="text-align: center">{{$data->motherVassel->name ?? ''}}</td>
                        <td style="text-align: center">{{$data->destination->name ?? ' '}}</td>
                        <td style="text-align: center">{{$data->dest_qty}}</td>
                        <td style="text-align: center">{{$data->carrying_bill}}</td>
                        <td style="text-align: center">{{$data->line_charge}}</td>
                        <td style="text-align: center">{{$data->scale_fee}}</td>
                        <td style="text-align: center">{{$data->other_cost}}</td>
                        <td style="text-align: center">{{$data->advancePayment->cashamount ?? ""}}</td>
                        <td style="text-align: center">{{$data->advancePayment->fuelqty ?? ""}}</td>
                        <td style="text-align: center">{{$data->advancePayment->fuelamount ?? ""}}</td>
                        <td style="text-align: center">{{$data->advancePayment->fueltoken ?? ""}}</td>
                        <td style="text-align: center">{{$data->advancePayment->petrolPump->name ?? ""}}</td>

                        @php
                            $alltotalfuelamount += $data->advancePayment->fuelamount ?? 0;
                            $alltotalfuelqty += $data->advancePayment->fuelqty ?? 0;
                            $alltotalcarrying_bill += $data->carrying_bill ?? 0;
                            $alltotaladvance += $data->advance ?? 0;
                            $alltotalother_cost += $data->other_cost ?? 0;
                            $alltotalscale_fee += $data->scale_fee ?? 0;
                            $alltotalline_charge += $data->line_charge ?? 0;
                            $alltotaldest_qty += $data->dest_qty ?? 0;
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
                        <td style="text-align: center"></td>
                        <td style="text-align: center"></td>
                        <td style="text-align: center">{{$alltotaldest_qty}}</td>
                        <td style="text-align: center">{{$alltotalcarrying_bill}}</td>
                        <td style="text-align: center">{{$alltotalline_charge}}</td>
                        <td style="text-align: center">{{$alltotalscale_fee}}</td>
                        <td style="text-align: center">{{$alltotalother_cost}}</td>
                        <td style="text-align: center">{{$alltotaladvance - $alltotalfuelamount}}</td>
                        <td style="text-align: center">{{$alltotalfuelqty}}</td>
                        <td style="text-align: center">{{$alltotalfuelamount}}</td>
                        <td style="text-align: center"><b>Total adv:</b></td>
                        <td style="text-align: center"><b>{{$alltotaladvance}}</b></td>
                    </tr>
                    <tr>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"  colspan="8">
                          <strong>Total Vendor's Payable: {{$alltotalcarrying_bill + $alltotalscale_fee - $alltotaladvance}}</strong>
                      </td>
                      <td style="text-align: center"></td>
                      <td style="text-align: center"></td>
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


@endsection
@section('script')


<script>
    $(function () {
        $('.datatable').each(function (index) {
            const table = $(this).DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                buttons: [
                    {
                        extend: 'copy',
                        footer: true,
                        title: 'Vendor Report',
                    },
                    {
                        extend: 'csv',
                        footer: true,
                        title: 'Vendor Report',
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true,
                        title: 'Vendor Report',
                    },
                    {
                        extend: 'pdf',
                        footer: true,
                        title: 'Vendor Report',
                    },
                    {
                        extend: 'print',
                        footer: true,
                        title: 'Vendor Report',
                    }
                ],
                lengthMenu: [[100, -1, 50, 25], [100, "All", 50, 25]]
            });

            // Append buttons to each table's wrapper
            table.buttons().container().appendTo($(this).closest('.dataTables_wrapper').find('.col-md-6:eq(0)'));
        });
    });
</script>


<script>
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
</script>



<script>
  $(document).ready(function () {


    //
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    //



          


  });
</script>



@endsection