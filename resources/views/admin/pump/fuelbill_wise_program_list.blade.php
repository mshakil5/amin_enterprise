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
          <a href="{{route('admin.pump')}}" class="btn btn-secondary my-3">Back</a>

          
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

          <div class="card card-secondary mb-3">
            <div class="card-header">
              <h3 class="card-title">Change all challan fuel rate</h3>
            </div>
            <!-- /.card-header -->
              <!-- /.card-header -->
              <div class="card-body">
                <div class="ermsg"></div>
                <form id="rateChangeForm">
                  @csrf


                  <div class="row">
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label>Fuel Rate*</label>
                        <input type="number" name="fuel_rate" class="form-control" >
                        <input type="number" name="fuel_bill_id" class="form-control" value="{{ $pumpSequenceNumber->id ?? '' }}" hidden>
                      </div>
                    </div>
                  </div>

                  
                </form>
              </div>

              <!-- /.card-body -->
              <div class="card-footer">
                  <button type="button" id="frateBtn" class="btn btn-secondary">Submit</button>
              </div>
              <!-- /.card-footer -->
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


<section class="content">
  <div class="container-fluid">
    <div class="card card-secondary mb-3">
      <div class="card-header p-2">
        <ul class="nav nav-pills" id="tripTabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="mother-tab" data-toggle="pill" href="#mother" role="tab" aria-controls="mother" aria-selected="true">
              Trip List Mother Vassel Wise
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="all-tab" data-toggle="pill" href="#all" role="tab" aria-controls="all" aria-selected="false">
              All Trip 
            </a>
          </li>
          <li class="nav-item d-none">
            <a class="nav-link" id="example-tab" data-toggle="pill" href="#example" role="tab" aria-controls="example" aria-selected="false">
              example
            </a>
          </li>
        </ul>
      </div>

      <div class="card-body">
        <div class="tab-content" id="tripTabsContent">
          <div class="tab-pane fade show active" id="mother" role="tabpanel" aria-labelledby="mother-tab">


            {{-- <div class="table-responsive">
              <table class="table table-striped table-bordered datatable">
                
              </table>
            </div> --}}

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
                  <h4>Pump: {{ $pump->name ?? 'N/A' }}</h4>
                  <h5>Sequence Number: {{ $pumpSequenceNumber->unique_id ?? 'N/A' }}</h5>
                  <h5>Mother Vessel: {{ $motherVassel ?? 'N/A' }}</h5>
                </div>
                <div class="table-responsive">
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
                              <td style="text-align: center">{{$data->advancePayment->fuelqty ?? ""}} * {{$data->advancePayment->fuel_rate ?? ""}}</td>
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
                </div>

              @endforeach

              
            </div>
          </div>

          <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
            <div style="text-align: center; margin-bottom: 20px;">
                  <h4>Pump: {{ $pump->name ?? 'N/A' }}</h4>
                  <h5>Sequence Number: {{ $pumpSequenceNumber->unique_id ?? 'N/A' }}</h5>
                </div>

            <div class="table-responsive">
              <table class="table table-striped table-bordered datatable">
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
                  </tr>
                </thead>
                <tbody>
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

                    @foreach ($allTrips as $key => $data)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <label class="form-checkbox grid layout">
                                    <input type="checkbox" @if($data->generate_bill) checked @endif>
                                </label>
                            </td>

                            <td>{{ $data->advancePayment->petrolPump->name ?? '' }}</td>
                            <td>{{ $data->bill_no }}</td>
                            <td>{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y') }}</td>
                            <td>{{ $data->vendor->name }}</td>
                            <td>{{ $data->headerid }}</td>
                            <td>{{ strtoupper($data->truck_number) }}</td>
                            <td>{{ $data->challan_no }}</td>
                            <td>{{ $data->destination->name ?? '' }}</td>
                            <td>{{ $data->dest_qty }}</td>
                            <td>{{ $data->carrying_bill }}</td>
                            <td>{{ $data->line_charge }}</td>
                            <td>{{ $data->scale_fee }}</td>
                            <td>{{ $data->other_cost }}</td>
                            <td>{{ $data->advancePayment->cashamount ?? 0 }}</td>
                            <td>{{ $data->advancePayment->fuelqty ?? 0 }}</td>
                            <td>{{ $data->advancePayment->fuelamount ?? 0 }}</td>
                            <td>{{ $data->advancePayment->fueltoken ?? '' }}</td>
                            <td>{{ $data->advancePayment->petrolPump->name ?? '' }}</td>

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
                    </tr>
                    <tr>
                        <td colspan="10" style="text-align:right"><b>TOTAL:</b></td>
                        <td>{{ $totaldest_qty }}</td>
                        <td>{{ $totalcarrying_bill }}</td>
                        <td>{{ $totalline_charge }}</td>
                        <td>{{ $totalscale_fee }}</td>
                        <td>{{ $totalother_cost }}</td>
                        <td>{{ $totaladvance}}</td>
                        <td>{{ $totalfuelqty }}</td>
                        <td>{{ $totalfuelamount }}</td>
                        <td colspan="2"><b>Total Adv: {{ $totaladvance }}</b></td>
                    </tr>

                    <tr>
                        <td colspan="12"></td>
                        <td colspan="8" style="text-align:center">
                          
                        </td>
                    </tr>
                </tfoot>


              </table>
            </div>
          </div>

          <div class="tab-pane fade" id="example" role="tabpanel" aria-labelledby="example-tab">
            <div class="p-3">
              <h5>Example</h5>
              <p class="mb-0">Use this tab to show an example trip or instructions. Replace this content with real data or explanatory text as needed.</p>
            </div>
          </div>


        </div>
      </div>

    </div>
  </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0">Bill Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6 text-left">Total Carrying Bill</div>
                            <div class="col-6 text-right">{{ number_format($totals['total_carrying_bill'], 2) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-left">Total Scale Fee</div>
                            <div class="col-6 text-right">{{ number_format($totals['total_scale_fee'], 2) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-left">Total Cash Amount</div>
                            <div class="col-6 text-right">- {{ number_format($totals['total_cash_amount'], 2) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-left">Total Fuel Advance</div>
                            <div class="col-6 text-right">- {{ number_format($totals['total_fuel_amount'], 2) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-left">Bill Paid</div>
                            <div class="col-6 text-right">- {{ number_format($totals['total_paid'], 2) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6 text-left">Advance Adjust</div>
                            <div class="col-6 text-right">{{ number_format($totals['total_received'], 2) }}</div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6 text-left font-weight-bold">Total {{ $totals['label'] }}</div>
                            <div class="col-6 text-right font-weight-bold">{{ number_format(abs($totals['total_due']), 2) }}</div>
                        </div>
                        <div class="mt-3 small text-muted">
                            <strong>Calculation:</strong><br>
                            (Carrying Bill + Scale Fee) - (Cash Amount + Fuel Advance) + (Bill Paid - Advance Adjust) <br>
                            ({{ number_format($totals['total_carrying_bill'], 2) }} + {{ number_format($totals['total_scale_fee'], 2) }})
                            - ({{ number_format($totals['total_cash_amount'], 2) }} + {{ number_format($totals['total_fuel_amount'], 2) }}) + ({{ number_format($totals['total_paid'], 2) }} - {{ number_format($totals['total_received'], 2) }})
                            = {{ number_format($totals['total_due'], 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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


        $(document).on('click', '#frateBtn', function(e) {
            e.preventDefault();

            var formData = new FormData($('#rateChangeForm')[0]);
            

            $.ajax({
                url: '{{ route("change-program-fuel-rate") }}',
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
                    // console.error(xhr.responseText);
                },
                complete: function() {
                    $('#loader').hide();
                    $('#addBtn').attr('disabled', false);
                }
            });
        });
          


  });
</script>


@endsection