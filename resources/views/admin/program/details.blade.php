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
                                              {{\App\Models\Ghat::where('id', $data->ghat_id)->first()->name}}
                                          @endif </b></p>
                                          
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
                                          <input type="number" class="form-control fuel_rate" name="fuel_rate[]" value="105">
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
                                          <input type="number" class="form-control fuel_rate" name="fuel_rate[]" value="105">
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
                        <th>Line Charge</th>
                        <th>Scale fee</th>
                        <th>Other Cost</th>
                        <th>Advance</th>
                        <th>Fuel qty</th>
                        <th>Fuel Amount</th>
                        <th>Fuel token</th>
                        <th>Pump name</th>
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
                            <td style="text-align: center">{{$data->advancePayment->cashamount}}</td>
                            <td style="text-align: center">{{$data->advancePayment->fuelqty}}</td>
                            <td style="text-align: center">{{$data->advancePayment->fuelamount}}</td>
                            <td style="text-align: center">{{$data->advancePayment->fueltoken}}</td>
                            <td style="text-align: center">{{$data->advancePayment->petrolPump->name ?? ""}}</td>

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
                            <td style="text-align: center"></td>
                            <td style="text-align: center"></td>
                            <td style="text-align: center">{{$totaldest_qty}}</td>
                            <td style="text-align: center">{{$totalcarrying_bill}}</td>
                            <td style="text-align: center">{{$totalline_charge}}</td>
                            <td style="text-align: center">{{$totalscale_fee}}</td>
                            <td style="text-align: center">{{$totalother_cost}}</td>
                            <td style="text-align: center">{{$totaladvance}}</td>
                            <td style="text-align: center">{{$totalfuelqty}}</td>
                            <td style="text-align: center"></td>
                            <td style="text-align: center"></td>
                            <td style="text-align: center"></td>
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
                                <td style="text-align: center">{{$data->total_cashamount}}</td>
                                <td style="text-align: center">{{$data->total_fuelqty}}</td>
                                <td style="text-align: center">{{$data->total_fuelamount}}</td>
                                <td style="text-align: center">{{$data->total_amount}}</td>
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
                    <button type="button" id="undoBtn" class="btn btn-secondary">Undo</button>
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
                                  <input type="number" class="form-control fuel_rate" name="fuel_rate[]" value="105">
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
                    // console.log(response)
                    $('#advTitle').html(`
                        <h2>Vendor Advance Summary</h2>
                        <h4>Mother Vessel: ${response.program.mother_vassel.name}</h4>
                        <h4>Details of vendor advances for the selected date: ${selectedDate}</h4>
                    `);
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
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseJSON.message);
                }
            });
        }
    });

    // change qty
    $('#qtyBtn').click(function() {
        console.log('work');
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