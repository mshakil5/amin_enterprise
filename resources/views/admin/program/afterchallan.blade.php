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
                        <h3 class="card-title" id="cardTitle">After challan receive posting program</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"> </div>
                        
                        <form id="createThisForm">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-2">
                                            <label for="client_id">Client </label>
                                            <select name="client_id" id="client_id" class="form-control">
                                              <option value="">Select</option>
                                              @foreach ($clients as $client)
                                              <option value="{{$client->id}}">{{$client->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="date">Date</label>
                                            <input type="date" class="form-control" id="date" name="date" value="{{date('Y-m-d')}}">
                                            <span id="productCodeError" class="text-danger"></span>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="challan_no">Challan Number <span style="color: red;">*</span></label>
                                            <input type="number" class="form-control" id="challan_no" name="challan_no" >
                                        </div>

                                        
                                        <div class="form-group col-md-2">
                                            <label>Action</label> <br>
                                            <button type="button" form="createThisForm" id="checkBtn"  class="btn btn-secondary">Check</button>
                                            <div id="loader" style="display: none;">
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Loading...
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>

                            </div>
                            
                        </form>
                    </div>
                    <div class="card-footer">
                      
                        
                    </div>



                    
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
                        
                        <form id="addadvThisForm">
                            @csrf

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
                                        
        
                                        
                                    </div>
                                </div>

            
                                <div class="col-sm-6">
                                    
                                    <div class="form-row">

                                        
        
                                        <div class="form-group col-md-4">
                                            <label for="mother_vassel_id">Mother Vassel</label>
                                            
                                            <select name="mother_vassel_id" id="mother_vassel_id" class="form-control">
                                              <option value="">Select</option>
                                              @foreach ($mvassels as $mvassel)
                                              <option value="{{$mvassel->id}}">{{$mvassel->name}}</option>
                                              @endforeach
                                            </select>
                                            
                                        </div>
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
        
                                    </div>
                                </div>
                            </div>


                            <table class="table table-bordered mb-5" id="programTable">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Truck#</th>
                                        <th>Cash Adv</th>
                                        <th>Fuel qty</th>
                                        <th>Fuel rate</th>
                                        <th>Fuel adv</th>
                                        <th>Fuel token</th>
                                        <th>Pump</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="form-control" id="vendor_id">
                                                <option value="">Select Vendor</option>
                                                @foreach ($vendors as $vendor)
                                                <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" id="truck_number" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="cashamount" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="fuelqty" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="fuel_rate" >
                                        </td>
                                        <td> 
                                            <input type="number" class="form-control" id="fuel_amount" readonly >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="fueltoken" >
                                        </td>
                                        <td>
                                            <select id="petrol_pump_id" class="form-control" >
                                                <option value="">Select</option>
                                                @foreach ($pumps as $pump)
                                                    <option value="{{$pump->id}}">{{$pump->name}}</option>
                                                @endforeach
                                                </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="amount" value="" readonly>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>


                            <table class="table table-bordered" id="rateTable">
                                <thead>
                                    <tr>
                                        <th>Header ID</th>
                                        <th>Quantity as per challan</th>
                                        <th>Location</th>

                                        <th>Quantity</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                        
                                        <th>Quantity</th>
                                        <th>Rate</th>
                                        <th>Amount</th>

                                        <th>Grand Total</th>
                                        <th>Advance</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" id="headerid" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" id="totalqty" >
                                        </td>
                                        <td>
                                            <select class="form-control" id="destination_id">
                                                <option value="">Select destination</option>
                                                @foreach (\App\Models\Destination::where('status', 1)->get() as $destination)
                                                <option value="{{$destination->id}}">{{$destination->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="quantity1" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="rate1" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="amount1" readonly>
                                        </td>

                                        
                                        <td>
                                            <input type="number" class="form-control" id="quantity2" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="rate2" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="amount2" readonly>
                                        </td>



                                        <td> 
                                            <input type="number" class="form-control" id="grand_total" readonly >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="advance" readonly>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" id="balance" value="" readonly>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" form="addadvThisForm" class="btn btn-secondary">Check</button>
                        <div id="loader" style="display: none;">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection
@section('script')



<!-- Create Program Start -->
<script>
    $(document).ready(function() {
        $(document).on('click', '#checkBtn', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();
            $(this).attr('disabled', false);

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
                    $("#mother_vassel_id").val(response.program.mother_vassel_id);
                    $("#lighter_vassel_id").val(response.program.lighter_vassel_id);
                    $("#consignmentno").val(response.program.consignmentno);
                    $("#vendor_id").val(response.data.advance_payment.vendor_id);

                    $("#petrol_pump_id").val(response.data.advance_payment.petrol_pump_id);
                    $("#fuel_rate").val(response.data.advance_payment.fuel_rate);
                    $("#fuelqty").val(response.data.advance_payment.fuelqty);
                    $("#fueltoken").val(response.data.advance_payment.fueltoken);
                    $("#cashamount").val(response.data.advance_payment.cashamount);
                    $("#amount").val(response.data.advance_payment.amount);
                    $("#advance").val(response.data.advance_payment.amount);
                    $("#fuel_amount").val(response.data.advance_payment.fuel_rate * response.data.advance_payment.fuelqty);
                    $("#truck_number").val(response.data.truck_number);
                    $(".ermsg").html(response.message);
                    
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
<!-- Create Program End -->


@endsection