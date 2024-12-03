@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content pt-1" id="newBtnSection">
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
                        <h3 class="card-title" id="cardTitle">Create New Program</h3>
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
                                            <label for="client_id">Client <span style="color: red;">*</span>
                                              <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addCategoryModal">Add New</span>
                                            </label>
                                            
                                            <select name="client_id" id="client_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($clients as $client)
                                              <option value="{{$client->id}}">{{$client->name}}</option>
                                              @endforeach
                                            </select>
        
                                            @error('client_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="date">Date <span style="color: red;">*</span></label>
                                            <input type="date" class="form-control" id="date" name="date" value="{{date('Y-m-d')}}">
                                            <span id="productCodeError" class="text-danger"></span>
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
                                            <label for="mother_vassel_id">Mother Vassel <span style="color: red;">*</span>
                                              <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addCategoryModal">Add New</span>
                                            </label>
                                            
                                            <select name="mother_vassel_id" id="mother_vassel_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($mvassels as $mvassel)
                                              <option value="{{$mvassel->id}}">{{$mvassel->name}}</option>
                                              @endforeach
                                            </select>
        
                                            @error('mother_vassel_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="lighter_vassel_id">Lighter Vassel
                                              <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addCategoryModal">Add New</span>
                                            </label>
                                            
                                            <select name="lighter_vassel_id" id="lighter_vassel_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($lvassels as $lvassel)
                                              <option value="{{$lvassel->id}}">{{$lvassel->name}}</option>
                                              @endforeach
                                            </select>
        
                                            @error('lighter_vassel_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="ghat_id">Ghat<span style="color: red;">*</span>
                                              <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addCategoryModal">Add New</span>
                                            </label>
                                            
                                            <select name="ghat_id" id="ghat_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($ghats as $ghat)
                                              <option value="{{$ghat->id}}">{{$ghat->name}}</option>
                                              @endforeach
                                            </select>
        
                                            @error('ghat_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            
                                        </div>
        
                                        {{-- <div class="form-group col-md-8">
                                            <label for="description">Note</label>
                                            <textarea class="form-control" id="note" name="note"></textarea>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                            
                            <table class="table table-bordered" id="programTable">
                                <thead>
                                    <tr>
                                        <th>Vendor <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addColorModal">Add New</span></th>
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
                      <button type="submit" form="createThisForm" id="addBtn"  class="btn btn-secondary">Create</button>
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


<!-- Destination-->
<div class="modal fade" id="destModal" tabindex="-1" role="dialog" aria-labelledby="destModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="destModalLabel">Add slab rate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rateForm">
                <div class="modal-body">
                    
                    <div class="form-row p-2">
                        
                      <div class="form-group col-md-3">
                          <label for="maxqty">Max Qty</label>
                          <input type="number" class="form-control" name="maxqty[]" value="">
                      </div>
  
                      <div class="form-group col-md-6">
                          <label for="rate_per_qty">Rate per Qty</label>
                          <input type="number" class="form-control" name="rate_per_qty[]" value="">
                      </div>
  
                      <div class="form-group col-md-1">
                          <label>Action</label>
                          <button type="button" class="btn btn-success add-rate-row"><i class="fas fa-plus"></i></button>
                      </div>
                  </div>
  
  
                  <div id="dynamic-rate-rows" class="p-2"></div>
  
                  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="button" id="newRateBtn" class="btn btn-success">add</button>
                </div>
            </form>
        </div>
    </div>
</div>
  <!-- Destination end-->

@endsection
@section('script')



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


<!-- Create Program Start -->
<script>
    $(document).ready(function() {
        $(document).on('click', '#addBtn', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();

            var formData = new FormData($('#createThisForm')[0]);

            $.ajax({
                url: '{{ route("programStore") }}',
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