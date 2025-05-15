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

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Trip list (Sequence wise)</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

              <div style="text-align: center; margin-bottom: 20px;">
                <h4>Pump: {{ $pump->name ?? 'N/A' }}</h4>
                <h5>Sequence Number: {{ $pumpSequenceNumber->unique_id ?? 'N/A' }}</h5>
                <h5>Mother Vessel: {{ $motherVassel->name ?? 'N/A' }}</h5>
              </div>

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
                                $totalfuelamount = 0;
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
                        <td style="text-align: center">{{$data->truck_number}}</td>
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
                        <td style="text-align: center">{{$totaladvance}}</td>
                        <td style="text-align: center">{{$totalfuelqty}}</td>
                        <td style="text-align: center">{{$totalfuelamount}}</td>
                        <td style="text-align: center"></td>
                        <td style="text-align: center"></td>
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
      const topTitle = `Mother Vessel: {{ $motherVassel->name ?? 'N/A' }}\nVendor: {{ $vendor->name ?? 'N/A' }}\nSequence Number: {{ $vendorSequenceNumber->unique_id ?? 'N/A' }}`;
      const bottomFooter = `Total Vendor's Payable: {{ number_format($totalcarrying_bill + $totalscale_fee - $totaladvance, 2) }}`;

      $("#example1").DataTable({
        responsive: true,
        lengthChange: false,
        autoWidth: false,
        buttons: [
          {
            extend: 'copy',
            footer: true,
            title: 'Vendor Report',
            messageTop: topTitle,
            messageBottom: bottomFooter,
          },
          {
            extend: 'csv',
            footer: true,
            title: 'Vendor Report',
            messageTop: topTitle,
            messageBottom: bottomFooter,
          },
          {
            extend: 'excelHtml5',
            footer: true,
            title: 'Vendor Report',
            messageTop: function () {
              return 'Mother Vessel: {{ $motherVassel->name ?? "N/A" }}\n' +' | '+
                    'Vendor: {{ $vendor->name ?? "N/A" }}\n'  +' | '+
                    'Sequence Number: {{ $vendorSequenceNumber->unique_id ?? "N/A" }}';
            },
            messageBottom: bottomFooter,
          },

          {
            extend: 'pdf',
            footer: true,
            title: 'Vendor Report',
            messageTop: topTitle,
            customize: function (doc) {
              // Insert title in the center manually for PDF
              doc.content.splice(0, 0, {
                alignment: 'center',
                margin: [0, 0, 0, 12],
                text: [
                  { text: 'Mother Vessel: {{ $motherVassel->name ?? "N/A" }}\n', fontSize: 12 },
                  { text: 'Vendor: {{ $vendor->name ?? "N/A" }}\n', fontSize: 12 },
                  { text: 'Sequence Number: {{ $vendorSequenceNumber->unique_id ?? "N/A" }}\n\n', fontSize: 12 }
                ]
              });

              // Add footer manually
              doc.content.push({
                alignment: 'center',
                margin: [0, 20, 0, 0],
                text: [
                  { text: 'Total Fuel Amount: {{ $totalfuelamount }} | Total Fuel Qty: {{ $totalfuelqty }}\n', fontSize: 10 },
                  { text: 'Total Vendor\'s Payable: {{ $totalcarrying_bill + $totalscale_fee - $totaladvance }}', fontSize: 10 }
                ]
              });
            }
          },
          {
            extend: 'print',
            footer: true,
            title: '',
            messageTop: function () {
              return `
                <div style="text-align: center; margin-bottom: 20px;">
                  <h4>Mother Vessel: {{ $motherVassel->name ?? "N/A" }}</h4>
                  <h5>Vendor: {{ $vendor->name ?? "N/A" }}</h5>
                  <h5>Sequence Number: {{ $vendorSequenceNumber->unique_id ?? "N/A" }}</h5>
                </div>`;
            },
            messageBottom: function () {
              return `
                <div style="text-align: center; margin-top: 20px;">
                  <strong>Total Fuel Amount: {{ $totalfuelamount }} | Total Fuel Qty: {{ $totalfuelqty }}</strong><br>
                  <strong>Total Vendor's Payable: {{ $totalcarrying_bill + $totalscale_fee - $totaladvance }}</strong>
                </div>`;
            }
          }
        ],
        lengthMenu: [[100, -1, 50, 25], [100, "All", 50, 25]]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
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