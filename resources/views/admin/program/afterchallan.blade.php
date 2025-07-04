@extends('admin.layouts.admin')

@section('content')
<style>
    #programTable th{
        background-color: #090b0b52;
        color: white;
    }

    [id^="prgmDtlArrowKey_"]:focus, .focused-button {
        background-color: #28a745 !important; /* Darker green for focus */
        outline: 2px solid #0056b3 !important; /* Blue outline for accessibility */
        outline-offset: 2px;
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
                        <h3 class="card-title" id="cardTitle">After challan receive posting program</h3>
                        <div class="card-tools">
                              <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#modal-lg">
                                <i class="fas fa-envelope"></i>
                              </button>
                        </div>
                    </div>
                    
                    
                    <div class="card-body">
                        <div class="ermsg"> </div>
                        
                        <form id="createThisForm">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-2">
                                            <label for="mv_id">Mother Vassel<span style="color: red;">*</span> </label>
                                            <select name="mv_id" id="mv_id" class="form-control select2">
                                              <option value="">Select</option>
                                                @foreach ($mvassels as $mvassel)
                                                    <option value="{{ $mvassel->id }}" 
                                                        {{ session('mv_id') == $mvassel->id ? 'selected' : '' }}>
                                                        {{ $mvassel->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        {{-- <div class="form-group col-md-2">
                                            <label for="date">Date</label>
                                            <input type="date" class="form-control" id="date" name="date" value="{{date('Y-m-d')}}">
                                            <span id="productCodeError" class="text-danger"></span>
                                        </div> --}}
                                        <div class="form-group col-md-2">
                                            <label for="challan_no">Challan Number <span style="color: red;">*</span></label>
                                            <input type="number" class="form-control" id="challan_no" name="challan_no" >
                                        </div>

                                        
                                        <div class="form-group col-md-2">
                                            <label>Action</label> <br>
                                            <button type="button" form="createThisForm" id="checkBtn"  class="btn btn-secondary">Check</button>
                                        </div>
                                        
                                    </div>
                                </div>

                            </div>
                            
                        </form>
                    </div>
                    <div class="card-footer"> </div>
                </div>
            </div>

            <div class="mt-3"></div>

            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Challan Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="advermsg"> </div>
                        
                        

                            <div class="row">
                                <div class="col-sm-6">
                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="date">Date </label>
                                            <input type="date" class="form-control" id="date" name="date" value="{{date('Y-m-d')}}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="consignmentno">Consignment Number</label>
                                            <input type="text" class="form-control" id="consignmentno" name="consignmentno" >
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="mother_vassel_id">Mother Vassel</label>
                                            
                                            <select name="mother_vassel_id" id="mother_vassel_id" class="form-control">
                                              <option value="">Select</option>
                                              @foreach ($mvassels as $mvassel)
                                              <option value="{{$mvassel->id}}">{{$mvassel->name}}</option>
                                              @endforeach
                                            </select>
                                            
                                        </div>
                                        
        
                                        
                                    </div>
                                </div>

            
                                <div class="col-sm-6">
                                    
                                    <div class="form-row">

                                        
        
                                        
                                        <div class="form-group col-md-4">
                                            <label for="lighter_vassel_id">Lighter Vassel </label>
                                            
                                            <select name="lighter_vassel_id" id="lighter_vassel_id" class="form-control">
                                              <option value="">Select</option>
                                              @foreach ($lvassels as $lvassel)
                                              <option value="{{$lvassel->id}}">{{$lvassel->name}}</option>
                                              @endforeach
                                            </select>
        
                                            
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="ghat_id">Ghat </label>
                                            
                                            <select name="ghat_id" id="ghat_id" class="form-control">
                                              <option value="">Select</option>
                                              @foreach ($ghats as $ghat)
                                              <option value="{{$ghat->id}}">{{$ghat->name}}</option>
                                              @endforeach
                                            </select>
                                            
                                        </div>

                                        <div class="form-group col-md-4">
                                            
                                            
                                        </div>

                                        
        
                                    </div>
                                </div>
                            </div>

                            
                        <form id="addadvThisForm">
                            @csrf

                            <table class="table table-bordered mb-2" id="programTable">
                                <thead>
                                    <tr>
                                        <th style="width: 200px; text-align: center">Action</th>
                                        <th style="text-align: center">Vendor</th>
                                        <th style="text-align: center">Truck#</th>
                                        <th style="text-align: center">Cash Adv</th>
                                        <th style="text-align: center">Fuel qty</th>
                                        <th style="text-align: center">Fuel rate</th>
                                        <th style="text-align: center">Fuel adv</th>
                                        <th style="text-align: center">Fuel token</th>
                                        <th style="text-align: center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>

                        

                            <div class="row" id="headerDiv">
                                <div class="col-sm-12">
                                    <div class="afterchallanmsg"></div>
                                </div>
                                
                                <div class="col-sm-6">
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="headerid">Header ID</label>
                                            <input type="text" class="form-control" id="headerid" name="headerid" tabindex="3">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="totalqtyasperchallan">Quantity as per challan</label>
                                            <input type="number" class="form-control" id="totalqtyasperchallan" name="totalqtyasperchallan" tabindex="4">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="destid">Destination</label>
                                            <select class="form-control" id="destid" name="destid" tabindex="5">
                                                <option value="">Select destination</option>
                                                @foreach (\App\Models\Destination::where('status', 1)->get() as $destination)
                                                    <option value="{{$destination->id}}">{{$destination->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="scale_fee">Scale fee</label>
                                            <input type="number" class="form-control" id="scale_fee" name="scale_fee" tabindex="6">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="line_charge">Line Charge</label>
                                            <input type="number" class="form-control" id="line_charge" name="line_charge" tabindex="7">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="other_cost">Other cost</label>
                                            <input type="number" class="form-control" id="other_cost" name="other_cost" tabindex="8">
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="sequence_id">Vendors Sequence Id</label>
                                            <select name="sequence_id" id="sequence_id" class="form-control select2" style="width: 100%;" tabindex="9">
                                                <option value="">Select</option>
                                                @foreach (\App\Models\VendorSequenceNumber::all() as $vsequence)
                                                    <option value="{{$vsequence->id}}">{{$vsequence->unique_id}}</option>
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
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td>Total</td>
                                                <td><input type="number" class="form-control" id="totalamount" name="totalamount" readonly ></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Additional cost</td>
                                                <td><input type="number" class="form-control" id="additionalCost" name="additionalCost" readonly ></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Advance</td>
                                                <td>
                                                    <input type="number" class="form-control" id="advanceAmnt" name="advanceAmnt" readonly >
                                                    <input type="hidden" class="form-control" id="prgmdtlid" name="prgmdtlid" readonly>
                                                    <input type="hidden" class="form-control" id="advPmtid" name="advPmtid" readonly>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Due</td>
                                                <td><input type="number" class="form-control" id="totalDue" name="totalDue" readonly ></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    
                                    <button type="button" id="afterChallanBtn" form="addadvThisForm" class="btn btn-secondary" tabindex="10">Submit</button>
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
                            <td style="text-align: center">{{$slbrate->ghat->name ?? null}} - {{$slbrate->ghat_id}}</td>
                            <td style="text-align: center">{{$slbrate->destination->name}} - {{$slbrate->destination_id}}</td>
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
        let ajaxRequest = null; // Track ongoing AJAX request
        let debounceTimeout = null; // Debounce timer

        $(document).on('change', '#destid', function(e) {
            e.preventDefault();

            // Clear any existing debounce timeout
            clearTimeout(debounceTimeout);

            // Debounce the change event (wait 300ms after last change)
            debounceTimeout = setTimeout(function() {
                var totalqtyasperchallan = $("#totalqtyasperchallan").val();
                var prgmdtlid = $("#prgmdtlid").val();
                var vendor = $("#vendor_id" + prgmdtlid).val();

                if (!totalqtyasperchallan) {
                    alert('Please input quantity as per challan first!');
                    $("#destid").val('');
                    return;
                }

                var formData = new FormData();
                formData.append("destid", $("#destid").val());
                formData.append("ghat", $("#ghat_id").val());
                formData.append("challanqty", totalqtyasperchallan);
                formData.append("prgmdtlid", prgmdtlid);
                formData.append("vendor", vendor);

                // Abort any ongoing AJAX request
                if (ajaxRequest) {
                    ajaxRequest.abort();
                }

                // Show loading state (optional)
                $('#rateTable tbody').html('<tr><td colspan="3" class="text-center"><div class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></div> Loading...</td></tr>');

                // Make AJAX request
                ajaxRequest = $.ajax({
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
                        console.log(response);

                        // Clear the table before appending new data
                        $('#rateTable tbody').empty();

                        if (response.status == 300) {
                            if (response.totalAmount > 0) {
                                // Assuming $dueAmnt should be a monetary value, not destid
                                // If $dueAmnt is meant to be something else, replace with correct logic
                                var dueAmnt = parseFloat($("#totalDue").val()) || 0; // Example: Use previous due amount or 0
                                $('#rateTable tbody').append(response.rate);
                                $("#totalamount").val(response.totalAmount);
                                $("#sequence_id").html(response.vdata);
                                $("#totalDue").val(response.totalAmount - dueAmnt);

                                updateSummary();
                            } else {
                                $("#totalamount").val(0);
                                $("#totalDue").val(0);
                            }
                        } else {
                            $("#totalamount").val(0);
                            $("#totalDue").val(0);
                            $(".advermsg").html(response.message);
                            setTimeout(function() {
                                $('.advermsg').fadeOut('slow', function() {
                                    $(this).html('').show();
                                });
                            }, 1000);
                        }
                    },
                    error: function(xhr, status, error) {
                        if (status !== 'abort') {
                            console.log(xhr.responseJSON?.message || 'An error occurred');
                        }
                    },
                    complete: function() {
                        $('#loader').hide();
                        $('#addBtn').attr('disabled', false);
                        ajaxRequest = null; // Reset AJAX request tracker
                    }
                });
            }, 300); // 300ms debounce delay
        });
    });


</script>

<!-- Create check challan Start -->
<script>
    $(document).ready(function() {

        
    $("#headerDiv").hide();

        $(document).on('click', '#checkBtn', function(e) {
            e.preventDefault();
            $('#programTable tbody').html('');
            $('#rateTable tbody').html('');

            $(this).attr('disabled', true);
            $('#loader').show();
            $(this).attr('disabled', false);

            $('#programTable tbody').html('');
            $("#addadvThisForm").hide();
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
                    console.log(response);
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
                        $("#program_detail_id").val(response.program_detail_id);
                        $("#mother_vassel_id").val(response.program.mother_vassel_id);
                        $("#lighter_vassel_id").val(response.program.lighter_vassel_id);
                        $("#ghat_id").val(response.ghatID);
                        $("#consignmentno").val(response.program.consignmentno);
                        $(".ermsg").html(response.message);
                        $('#programTable tbody').append(response.data);
                        $('#rateTable tbody').append(response.prate);

                        // Focus on the first prgmDtlArrowKey button
                        const $targetButton = $('#programTable tbody [id^="prgmDtlArrowKey_"]').first();
                        $targetButton.addClass('focused-button').focus();

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


        // Remove focused class on blur
    $(document).on('blur', '[id^="prgmDtlArrowKey_"]', function() {
        $(this).removeClass('focused-button');
    });

    // Custom keybinding for Ctrl+Q
    $(document).on('keydown', function(e) {
        if (e.ctrlKey && e.key === 'q') {
            e.preventDefault();
            const $targetButton = $('#rateTable tbody [id^="prgmDtlArrowKey_"]').first();
            if ($targetButton.length) {
                $targetButton.addClass('focused-button').focus();
                console.log('Focused on button:', $targetButton.attr('id'));
            } else {
                console.log('No prgmDtlArrowKey button found');
            }
        }
    });

    // Handle click and Enter key for .addrateThis
    $(document).on('click', '.addrateThis', function() {
        handleAddRateThis($(this));
    });

    $(document).on('keydown', '[id^="prgmDtlArrowKey_"]', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Prevent default Enter behavior
            handleAddRateThis($(this)); // Trigger the same logic as click
        }
    });

    // Function to handle .addrateThis logic
    function handleAddRateThis($element) {
        const advAmnt = $element.attr('data-adv');
        const prgmDtlId = $element.attr('data-pdtlid');
        const advPmtiId = $element.attr('data-advid');
        const headerid = $element.attr('data-headerid');
        const destqty = $element.attr('data-destqty');
        const linecharge = $element.attr('data-linecharge');
        const scale_fee = $element.attr('data-scale_fee');
        const other_cost = $element.attr('data-other_cost');
        const destination_id = $element.attr('data-destination_id');
        const due = $element.attr('data-due');
        const additional_cost = $element.attr('data-additional_cost');
        const carrying_bill = $element.attr('data-carrying_bill');
        const vendor_sequence_number_id = $element.attr('data-vendor_sequence_number_id');

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
    }

});
</script>
<!-- Create  check challan End -->







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

            $(this).attr('disabled', true);
            $('#loader').show();

            var prgmdtlid = $('#prgmdtlid').val();
            var ghat_id = $('#ghat_id').val();

            var formData = new FormData($('#addadvThisForm')[0]);
            formData.append("vendor_id", $('#vendor_id'+prgmdtlid).val());
            formData.append("truck_number", $('#truck_number'+prgmdtlid).val());
            formData.append("fuelqty", $('#fuelqty'+prgmdtlid).val());
            formData.append("fuel_rate", $('#fuel_rate'+prgmdtlid).val());
            formData.append("fuel_amount", $('#fuel_amount'+prgmdtlid).val());
            formData.append("amount", $('#amount'+prgmdtlid).val());
            formData.append("fueltoken", $('#fueltoken'+prgmdtlid).val());
            formData.append("ghat_id", $('#ghat_id').val());

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
                        console.log(response);
                    $('#loader').hide();
                    $('#afterChallanBtn').attr('disabled', false);

                    if (response.status == 400) {
                        $(".afterchallanmsg").html(response.message);
                    } else {
                        console.log(response);
                        $(".afterchallanmsg").html(response.message);
                        window.setTimeout(function(){location.reload()},2000)
                    }

                    
                },
                error: function(xhr, status, error) {
                    $('#loader').hide();
                    $('#afterChallanBtn').attr('disabled', false);
                    console.log(xhr.responseJSON.message);
                    // console.error(xhr.responseText);
                },
                complete: function() {
                    $('#loader').hide();
                    $('#afterChallanBtn').attr('disabled', false);
                }
            });
        });



         $(document).on('click', '#updatebtn', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();
            var prgmdtlid = $('#program_detail_id').val();
            var formData = new FormData($('#addadvThisForm')[0]);
            formData.append("vendor_id", $('#vendor_id'+prgmdtlid).val());
            formData.append("truck_number", $('#truck_number'+prgmdtlid).val());
            formData.append("fuelqty", $('#fuelqty'+prgmdtlid).val());
            formData.append("fuel_rate", $('#fuel_rate'+prgmdtlid).val());
            formData.append("fuel_amount", $('#fuel_amount'+prgmdtlid).val());
            formData.append("amount", $('#amount'+prgmdtlid).val());
            formData.append("fueltoken", $('#fueltoken'+prgmdtlid).val());

            formData.append("mother_vassel_id", $('#mother_vassel_id').val());
            formData.append("lighter_vassel_id", $('#lighter_vassel_id').val());
            formData.append("ghat_id", $('#ghat_id').val());
            formData.append("program_detail_id", $('#program_detail_id').val());

            $.ajax({
                url: '{{ route("single-programdetail-update") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(response);
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

        // Clear specific fields when certain inputs change
        $(document).on('input change', '#totalqtyasperchallan', function() {
            // Clear rate table and summary fields
            $('#rateTable tbody').empty();
            $('#totalamount').val('');
            $('#additionalCost').val('');
            $('#advanceAmnt').val('');
            $('#totalDue').val('');
            $('#destid').val('');
        });

        // Optionally, clear vendor sequence when destination changes
        $(document).on('change', '#destid', function() {
            $('#sequence_id').val('');
        });



    });
</script>
<!-- Program after challan data store End -->



@endsection