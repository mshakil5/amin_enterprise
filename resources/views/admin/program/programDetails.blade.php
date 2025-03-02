@extends('admin.layouts.admin')

@section('content')
<style>
    #programTable th{
        background-color: #090b0b52;
        color: white;
    }
</style>
<!-- Main content -->
<section class="content mt-3" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <a href="{{route('admin.allProgram')}}" class="btn btn-secondary my-3">Back</a>
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
                        <h3 class="card-title" id="cardTitle">Challan Information</h3>
                        
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#modal-lg">
                              <i class="fas fa-envelope"></i>
                            </button>
                      </div>
                    </div>
                    <div class="card-body">
                        <div class="advermsg"> </div>
                        
                        

                            <div class="row">
                                <div class="col-sm-6">
                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="date">Date </label>
                                            <input type="date" class="form-control" id="date" name="date" value="{{ $data->date}}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="consignmentno">Consignment Number</label>
                                            <input type="text" class="form-control" id="consignmentno" name="consignmentno" value="{{$data->consignmentno}}">
                                        </div>
                                        
        
                                        
                                    </div>
                                </div>

            
                                <div class="col-sm-6">
                                    
                                    <div class="form-row">

                                        
        
                                        <div class="form-group col-md-4">
                                            <label for="mother_vassel_id">Mother Vassel</label>
                                            
                                            <select name="mother_vassel_id" id="mother_vassel_id" class="form-control">
                                              <option value="">Select</option>
                                              @foreach ($mvassels as $mvassel)
                                              <option value="{{$mvassel->id}}" @if ($data->mother_vassel_id == $mvassel->id) selected @endif>{{$mvassel->name}}</option>
                                                  
                                              @endforeach
                                            </select>
                                            
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="lighter_vassel_id">Lighter Vassel </label>
                                            
                                            <select name="lighter_vassel_id" id="lighter_vassel_id" class="form-control">
                                              <option value="">Select</option>
                                              @foreach ($lvassels as $lvassel)
                                              <option value="{{$lvassel->id}}"  @if ($data->lighter_vassel_id == $lvassel->id) selected @endif>{{$lvassel->name}}</option>
                                              @endforeach
                                            </select>
        
                                            
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="ghat_id">Ghat </label>
                                            
                                            <select name="ghat_id" id="ghat_id" class="form-control">
                                              <option value="">Select</option>
                                              @foreach ($ghats as $ghat)
                                              <option value="{{$ghat->id}}"   @if ($data->ghat_id == $ghat->id) selected @endif>{{$ghat->name}}</option>
                                              @endforeach
                                            </select>
                                            
                                        </div>
        
                                    </div>
                                </div>
                            </div>

                            
                        <form id="addadvThisForm">
                            @csrf

                            <table class="table table-bordered mb-2" id="programTable">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Truck#</th>
                                        <th>Cash Adv</th>
                                        <th>Fuel qty</th>
                                        <th>Fuel rate</th>
                                        <th>Fuel adv</th>
                                        <th>Fuel token</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <td>
                                            <select class="form-control" id="vendor_id{{$data->id}}" name="vendor_id">
                                                
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{$vendor->name}}" @if ($data->vendor_id == $vendor->id) selected @endif>{{$vendor->name}}</option>
                                                @endforeach
                                                
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" id="truck_number{{$data->id}}" value="{{$data->truck_number}}">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control pcashamount" id="cashamount{{$data->id}}" value="{{$data->advancePayment->cashamount}}" name="cashamount[]" readonly="">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control pfuelqty" id="fuelqty{{$data->id}}" name="fuelqty[]" value="{{$data->advancePayment->fuelqty}}">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control pfuel_rate" id="fuel_rate{{$data->id}}" name="fuel_rate[]" value="{{$data->advancePayment->fuel_rate}}">
                                        </td>
                                        <td> 
                                            <input type="number" class="form-control pfuel_amount" id="fuel_amount{{$data->id}}" readonly="" name="fuel_amount[]" value="{{$data->advancePayment->fuelamount}}">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="fueltoken{{$data->id}}" value="{{$data->advancePayment->fueltoken}}" name="fueltoken[]">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control pamount" id="amount{{$data->id}}" readonly="" value="{{$data->advancePayment->amount}}" name="pamount[]">
                                        </td>
                                        <td>
                                            <span class="btn btn-sm btn-success addrateThis" data-pdtlid="{{$data->id}}" data-adv="{{$data->advancePayment->amount}}" data-headerid="{{$data->headerid}}" data-destqty="{{$data->dest_qty}}" data-linecharge="{{$data->line_charge}}" data-scale_fee="{{$data->scale_fee}}" data-other_cost="{{$data->other_cost}}" data-destination_id="{{$data->destination_id}}" data-advid="{{$data->id}}" data-due="{{$data->due}}" data-additional_cost="{{$data->additional_cost}}" data-carrying_bill="{{$data->carrying_bill}}" data-vendor_sequence_number_id="{{$data->vendor_sequence_number_id}}"><i class="fas fa-arrow-right"></i></span>
                                        </td>
                                    </tr>
                                    
                                    
                                </tbody>
                            </table>

                        

                            <div class="row" id="headerDiv">
                                <div class="col-sm-12">
                                    <div class="afterchallanmsg"></div>
                                </div>
                                
                                <div class="col-sm-6">
                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="headerid">Header ID </label>
                                            <input type="text" class="form-control" id="headerid" name="headerid" value="{{$data->headerid}}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="totalqtyasperchallan">Quantity as per challan</label>
                                            <input type="number" class="form-control" id="totalqtyasperchallan" name="totalqtyasperchallan" value="{{$data->dest_qty}}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="destid">Destination</label>
                                            <select class="form-control" id="destid" name="destid">
                                                <option value="">Select destination</option>
                                                @foreach (\App\Models\Destination::where('status', 1)->get() as $destination)
                                                <option value="{{$destination->id}}" @if ($data->destination_id == $destination->id) selected @endif >{{$destination->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="scale_fee">Scale fee</label>
                                            <input type="number" class="form-control" id="scale_fee" name="scale_fee" value="{{$data->scale_fee}}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="line_charge">Line Charge</label>
                                            <input type="number" class="form-control" id="line_charge" name="line_charge"  value="{{$data->line_charge}}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="other_cost">Other cost</label>
                                            <input type="number" class="form-control" id="other_cost" name="other_cost"  value="{{$data->other_cost}}">
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label for="sequence_id">Vendors Sequence Id </label> <br>
                                            
                                            <select name="sequence_id" id="sequence_id" class="form-control ">
                                              <option value="">Select</option>

                                              @foreach (\App\Models\VendorSequenceNumber::all() as $vsequence)
                                              <option value="{{$vsequence->id}}"  @if ($data->vendor_sequence_number_id == $vsequence->id) selected @endif >{{$vsequence->unique_id}}</option>
                                              @endforeach
                                              
                                            </select>
                                            
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="col-sm-6">

                                    <table class="table table-bordered" id="rateTable">
                                        <thead>
                                            <tr>
                                                <th>Qty</th>
                                                <th>Rate</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data->challanRate as $key => $challanRate)
                                            <tr>
                                                <td>
                                                    <input type="number" class="form-control qty" id="slabqty{{$key+1}}" name="qty[]" value="{{$challanRate->qty}}"><input type="hidden" class="form-control" id="challanrateid{{$key+1}}" name="challanrateid[]" value="{{$challanRate->id}}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control rate" id="slabrate{{$key+1}}" name="rate[]" value="{{$challanRate->rate_per_unit}}">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control rateunittotal" id="slabamnt{{$key+1}}" name="amnt[]" value="{{$challanRate->total}}" readonly="">
                                                </td>
                                            </tr>
                                            @endforeach
                                            

                                        </tbody>
                                        <tfoot>

                                            <tr>
                                                <td></td>
                                                <td>Total</td>
                                                <td><input type="number" class="form-control" id="totalamount" name="totalamount" value="{{$data->carrying_bill}}" readonly></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Additional cost</td>
                                                <td><input type="number" class="form-control" id="additionalCost" name="additionalCost" value="{{$data->additional_cost}}" readonly></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Advance</td>
                                                <td><input type="number" class="form-control" id="advanceAmnt" name="advanceAmnt" readonly value="{{$data->advance}}">
                                                    <input type="hidden" class="form-control" id="prgmdtlid" name="prgmdtlid" value="{{$data->id}}" readonly>
                                                    <input type="hidden" class="form-control" id="advPmtid" name="advPmtid" value="{{$data->advancePayment->id}}" readonly></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Due</td>
                                                <td><input type="number" class="form-control" id="totalDue" name="totalDue" value="{{$data->due}}" readonly></td>
                                            </tr>
                                            
                                        </tfoot>
                                    </table>
                                    
                                    <button type="button" id="afterChallanBtn" form="addadvThisForm" class="btn btn-secondary">Submit</button>
                                    <div id="loader" style="display: none;">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Loading...
                                    </div>
                                    
                                </div>

                            </div>

                            
                        </form>
                    </div>
                    <div class="card-footer">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



  <div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-secondary">
          <h4 class="modal-title">Destination slab rate</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <form action="" id="slabRateForm">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="dermsg"></div>
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label for="ghat_id">Ghat </label>
                                <select name="ghat_id" id="ghat_id" class="form-control">
                                  <option value="">Select</option>
                                  @foreach (\App\Models\Ghat::where('status', 1)->get() as $ghat)
                                  <option value="{{$ghat->id}}">{{$ghat->name}}</option>
                                  @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="destination_id">Destination </label>
                                <select name="destination_id" id="destination_id" class="form-control">
                                  <option value="">Select</option>
                                  @foreach (\App\Models\Destination::where('status', 1)->get() as $dest)
                                  <option value="{{$dest->id}}">{{$dest->name}}</option>
                                  @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title" >
                            </div>
                            <div class="form-group col-md-2">
                                <label for="qty">Qty</label>
                                <input type="number" class="form-control" id="qty" name="qty" value="12" >
                            </div>
                            <div class="form-group col-md-2">
                                <label for="below_rate_per_qty">Below Rate</label>
                                <input type="number" class="form-control" id="below_rate_per_qty" name="below_rate_per_qty" >
                            </div>
                            <div class="form-group col-md-2">
                                <label for="above_rate_per_qty">Above Rate</label>
                                <input type="number" class="form-control" id="above_rate_per_qty" name="above_rate_per_qty" >
                            </div>
                            <div class="form-group col-md-12">
                                <button type="button" id="slabRateBtn"  class="btn btn-secondary">Submit</button>
                                <div id="loader" style="display: none;">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    Loading...
                                </div>
                            </div>
                            
                        </div>
                    </div>

                </div>
            </form>



            <div>
                
                <table id="example1" class="table table-bordered table-striped">
                    <thead class="bg-secondary">
                        <tr>
                            <th style="text-align: center">Ghat</th>
                            <th style="text-align: center">Destination</th>
                            <th style="text-align: center">Qty</th>
                            <th style="text-align: center">Below rate</th>
                            <th style="text-align: center">Above rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (\App\Models\DestinationSlabRate::get() as $key => $slbrate)
                        <tr>
                            <td style="text-align: center">{{$slbrate->ghat->name ?? null}}</td>
                            <td style="text-align: center">{{$slbrate->destination->name}}</td>
                            <td style="text-align: center">{{$slbrate->maxqty}}</td>
                            <td style="text-align: center">{{$slbrate->below_rate_per_qty}}</td>
                            <td style="text-align: center">{{$slbrate->above_rate_per_qty}}</td>
                        </tr>
                        @endforeach
                    </tbody>
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



@endsection
@section('script')

<script>
      function updateSummary() {
        
        var itemTotalAmount = 0;
        $('#rateTable tbody tr').each(function() {
            var fuelqty = parseFloat($(this).find('input.qty').val()) || 0;
            var fuel_rate = parseFloat($(this).find('input.rate').val()) || 0;

            var totalPrice = (fuelqty * fuel_rate).toFixed(2);

            $(this).find('input.rateunittotal').val(totalPrice);
            var rateunittotal = parseFloat($(this).find('input.rateunittotal').val()) || 0;
            

            itemTotalAmount += parseFloat(rateunittotal) || 0;
            // console.log(itemTotalAmount);
        });
            // add other cost
            var scale_fee = parseFloat($('#scale_fee').val()) || 0;
            var line_charge = parseFloat($('#line_charge').val()) || 0;
            var other_cost = parseFloat($('#other_cost').val()) || 0;
            var advanceAmnt = parseFloat($('#advanceAmnt').val()) || 0;
            // add other cost
        
            var totalAdditionalCost = scale_fee;
            var totalAdditionalIncome =  line_charge + other_cost;
            var totalDue = totalAdditionalCost + itemTotalAmount - advanceAmnt - totalAdditionalIncome;
            $("#totalamount").val(itemTotalAmount);
            $("#additionalCost").val(totalAdditionalCost);
            $("#totalDue").val(totalDue);
    }

    $(document).on('input', '#scale_fee, #line_charge, #other_cost', function() {
        updateSummary();
        var scale_fee = parseFloat($('#scale_fee').val()) || 0;
        var line_charge = parseFloat($('#line_charge').val()) || 0;
    });

    $(document).on('input', '#rateTable input.qty, #rateTable input.rate, #rateTable input.rateunittotal', function() {
        updateSummary();
    });


    $(document).on('input', '#programTable input.pfuelqty, #programTable input.pfuel_rate, #programTable input.pfuel_amount, #programTable input.pamount, #programTable input.pcashamount', function() {

        var prgmdtlid = $('#prgmdtlid').val();
        var cashamount = parseFloat($('#cashamount'+prgmdtlid).val()) || 0;
        var fuelqty = parseFloat($('#fuelqty'+prgmdtlid).val()) || 0;
        var fuel_rate = parseFloat($('#fuel_rate'+prgmdtlid).val()) || 0;
        var fuel_amount = parseFloat($('#fuel_amount'+prgmdtlid).val()) || 0;
        var amount = parseFloat($('#amount'+prgmdtlid).val()) || 0;

        var fuelAdv = fuelqty * fuel_rate;
        var totalAdv = fuelAdv + cashamount;
        $('#fuel_amount'+prgmdtlid).val(fuelAdv);
        $('#amount'+prgmdtlid).val(totalAdv);
        $('#advanceAmnt').val(totalAdv);
        updateSummary();
        // console.log(fuelqty, fuel_rate, fuel_amount, amount);
    });


    
    $(document).ready(function() {
        $(document).on('change', '#destid', function(e) {
            e.preventDefault();

            var totalqtyasperchallan = $("#totalqtyasperchallan").val();
            var prgmdtlid = $("#prgmdtlid").val();
            var vendor = $("#vendor_id"+prgmdtlid).val();

            if (!totalqtyasperchallan) {
                alert('Please input quantity as per challan first!');
                $("#destid").val('');
                return;
            }
            console.log(vendor);
            var formData = new FormData();
            formData.append("destid", $("#destid").val());
            formData.append("ghat", $("#ghat_id").val());
            formData.append("challanqty", $("#totalqtyasperchallan").val());
            formData.append("prgmdtlid", prgmdtlid);
            formData.append("vendor", vendor);

            $('#rateTable tbody').empty();

            $.ajax({
                url: '{{ route("admin.checkSlabRate") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.totalAmount > 0) {
                        $dueAmnt = $("#destid").val();
                        $('#rateTable tbody').append(response.rate);
                        $("#totalamount").val(response.totalAmount);
                        $("#sequence_id").html(response.vdata);
                        $("#totalDue").val(response.totalAmount - $dueAmnt);
                        
                        updateSummary();

                    } else {

                        $('#rateTable tbody').empty();
                        $("#totalamount").val(0);
                        $("#totalDue").val(0);
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

<!-- Create check challan Start -->
<script>
    $(document).ready(function() {
        $(document).on('click', '#checkBtn', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();
            $(this).attr('disabled', false);

            $('#programTable tbody').html('');
            
            var formData = new FormData($('#createThisForm')[0]);

            $.ajax({
                url: '{{ route("admin.checkChallan") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // console.log(response);
                    if (response.data == "empty") {

                        $("#mother_vassel_id").val('');
                        $("#lighter_vassel_id").val('');
                        $("#ghat_id").val('');
                        $("#consignmentno").val('');
                        $(".ermsg").html(response.message);
                        $('#programTable tbody').html('');
                        $('#rateTable tbody').html('');

                    } else {

                        $("#addadvThisForm").show();
                        $("#headerDiv").hide();
                        // $("#totalAmount").val(response.totalAmount);
                        $("#mother_vassel_id").val(response.program.mother_vassel_id);
                        $("#lighter_vassel_id").val(response.program.lighter_vassel_id);
                        $("#ghat_id").val(response.program.ghat_id);
                        $("#consignmentno").val(response.program.consignmentno);
                        $(".ermsg").html(response.message);
                        $('#programTable tbody').append(response.data);
                        $('#rateTable tbody').append(response.prate);

                        setTimeout(function() {
                            $('.ermsg').fadeOut('slow');
                        }, 3000);

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
<!-- Create  check challan End -->




<script>
    // return stock
    // $("#headerDiv").hide(); 
    $("#programTable").on('click','.addrateThis', function(){
        advAmnt = $(this).attr('data-adv');
        prgmDtlId = $(this).attr('data-pdtlid');
        advPmtiId = $(this).attr('data-advid');

        headerid = $(this).attr('data-headerid');
        destqty = $(this).attr('data-destqty');
        linecharge = $(this).attr('data-linecharge');
        scale_fee = $(this).attr('data-scale_fee');
        other_cost = $(this).attr('data-other_cost');
        destination_id = $(this).attr('data-destination_id');
        due = $(this).attr('data-due');
        additional_cost = $(this).attr('data-additional_cost');
        carrying_bill = $(this).attr('data-carrying_bill');
        vendor_sequence_number_id = $(this).attr('data-vendor_sequence_number_id');

        $("#advanceAmnt").val(advAmnt);
        $("#prgmdtlid").val(prgmDtlId);
        $("#advPmtid").val(advPmtiId);
        $("#headerid").val(headerid);
        $("#totalqtyasperchallan").val(destqty);
        $("#destid").val(destination_id);
        $("#scale_fee").val(scale_fee);
        $("#line_charge").val(linecharge);
        $("#other_cost").val(other_cost);
        $("#totalDue").val(due);
        $("#additionalCost").val(additional_cost);
        $("#totalamount").val(carrying_bill);
        $("#sequence_id").val(vendor_sequence_number_id);
        $("#headerDiv").show();
    });
    // return stock end
</script>


<!-- Create Destination -->
<script>
    $(document).ready(function() {
        $(document).on('click', '#slabRateBtn', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();

            var formData = new FormData($('#slabRateForm')[0]);

            $.ajax({
                url: '{{ route("addDestinationSlabRate") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $(".dermsg").html(response.message);
                    $(this).attr('disabled', false);
                    
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseJSON.message);
                    console.error(xhr.responseText);
                },
                complete: function() {
                    $('#loader').hide();
                    $('#addBtn').attr('disabled', false);
                }
            });
        });

    });
</script>
<!--  Destination End -->


<!--  Program after challan data store start -->
<script>
    $(document).ready(function() {
        $(document).on('click', '#afterChallanBtn', function(e) {
            e.preventDefault();

            // $(this).attr('disabled', true);
            // $('#loader').show();
            var prgmdtlid = $('#prgmdtlid').val();


            // console.log(prgmdtlid, vendorid, truck_number, fuelqty, fuel_rate, fuel_amount, tamount, fueltoken, tamount,);
            var formData = new FormData($('#addadvThisForm')[0]);
            formData.append("vendor_id", $('#vendor_id'+prgmdtlid).val());
            formData.append("truck_number", $('#truck_number'+prgmdtlid).val());
            formData.append("fuelqty", $('#fuelqty'+prgmdtlid).val());
            formData.append("fuel_rate", $('#fuel_rate'+prgmdtlid).val());
            formData.append("fuel_amount", $('#fuel_amount'+prgmdtlid).val());
            formData.append("amount", $('#amount'+prgmdtlid).val());
            formData.append("fueltoken", $('#fueltoken'+prgmdtlid).val());

            $.ajax({
                url: '{{ route("after-challan-store") }}',
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
                        $(".afterchallanmsg").html(response.message);
                    } else {
                        console.log(response);
                        $(".afterchallanmsg").html(response.message);
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
<!-- Program after challan data store End -->



@endsection