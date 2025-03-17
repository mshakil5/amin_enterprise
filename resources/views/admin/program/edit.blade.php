@extends('admin.layouts.admin')

@section('content')

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
                        <h3 class="card-title" id="cardTitle">Update New Program</h3>
                    </div>
                    
                    <div class="card-body">
                        <div class="ermsg">
                            
                        </div>
                        
                        <form id="updateThisForm">
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
                                                <option value="{{$client->id}}" {{$program->client_id == $client->id ? 'selected' : '' }}>{{$client->name}}</option>
                                                @endforeach
                                            </select>
        
                                            @error('client_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                            
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="date">Date</label>
                                            <input type="date" class="form-control" id="date" name="date" value="{{ $program->date}}">
                                            <input type="hidden" class="form-control" id="pid" name="pid" value="{{ $program->id}}">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="consignmentno">Consignment Number</label>
                                            <input type="text" class="form-control" id="consignmentno" name="consignmentno"  value="{{ $program->consignmentno}}">
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
                                                <option value="{{$mvassel->id}}" {{$program->mother_vassel_id == $mvassel->id ? 'selected' : '' }}>{{$mvassel->name}}</option>
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
                                                <option value="{{$lvassel->id}}" {{$program->lighter_vassel_id == $lvassel->id ? 'selected' : '' }}>{{$lvassel->name}}</option>
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
                                              <option value="{{$ghat->id}}"  {{$program->ghat_id == $ghat->id ? 'selected' : '' }}>{{$ghat->name}}</option>
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

                                    @foreach ($program->programDetail as $key => $pdtl)
                                    <tr>
                                        <td>
                                            
                                            @foreach ($pdtl->transaction as $tran)
                                                <input type="hidden" value="{{$tran->id}}" name="tranid[]">
                                            @endforeach


                                            <select class="form-control" name="vendor_id[]" id="vendor_id">
                                                <option value="">Select Vendor</option>
                                                @foreach ($vendors as $vendor)
                                                <option value="{{$vendor->id}}" {{$pdtl->vendor_id == $vendor->id ? 'selected' : '' }}>{{$vendor->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="truck_number[]" value="{{$pdtl->truck_number}}" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="challan_no[]" value="{{$pdtl->challan_no}}" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control cashamount" name="cashamount[]" value="{{$pdtl->advancePayment->cashamount ?? ""}}" >
                                            <input type="hidden" class="form-control" name="advancePaymentId[]" value="{{$pdtl->advancePayment->id ?? ""}}" >

                                            <input type="hidden" class="form-control" name="program_detail_id[]" value="{{$pdtl->id}}" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control fuelqty" name="fuelqty[]" value="{{$pdtl->advancePayment->fuelqty ?? ""}}" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control fuel_rate" name="fuel_rate[]" value="{{$pdtl->advancePayment->fuel_rate ?? ""}}">
                                        </td>
                                        <td> 
                                            <input type="number" class="form-control fuel_amount" name="fuel_amount[]" readonly value="{{$pdtl->advancePayment->fuelamount ?? ""}}" >
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" name="fueltoken[]" value="{{$pdtl->advancePayment->fueltoken ?? ""}}" >
                                        </td>
                                        <td>
                                            <select name="petrol_pump_id[]" id="petrol_pump_id[]" class="form-control" >
                                                <option value="">Select</option>
                                                @foreach ($pumps as $pump)
                                                    <option value="{{$pump->id}}" @if (isset($pdtl->advancePayment->petrol_pump_id))
                                                        {{$pdtl->advancePayment->petrol_pump_id == $pump->id ? 'selected' : '' }} @endif >{{$pump->name}}</option>
                                                @endforeach
                                                </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control totalamount" name="amount[]" value="{{$pdtl->advancePayment->amount ?? ""}}" readonly>
                                        </td>
                                        <td>
                                            @if ($key == 0)
                                                <button type="button" class="btn btn-success add-row"><i class="fas fa-plus"></i></button>
                                            @else
                                            
                                            {{-- <button type="button" class="btn btn-danger remove-row"><i class="fas fa-minus"></i></button> --}}
                                                
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach

                                    


                                </tbody>
                            </table>
                            

                        </form>
                    </div>
                    <div class="card-footer">
                      <button type="submit" form="updateThisForm" id="updateBtn"  class="btn btn-secondary">Update</button>
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
  {{-- <script>
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
  </script> --}}
  <!-- Create Program End -->

<!-- update Program Start -->
<script>
    $(document).ready(function() {
        $(document).on('click', '#updateBtn', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();

            var formData = new FormData($('#updateThisForm')[0]);

            $.ajax({
                url: '{{ route("programUpdate") }}',
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
                    swal({
                        text: "Updated successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    }).then(() => {
                        location.reload();
                    });
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

        $('#product_code').on('keyup', function() {
            let productCode = $(this).val().trim();

            if (productCode.length >= 2) {
                $.ajax({
                    url: "{{ route('programStore') }}",
                    method: "GET",
                    data: { product_code: productCode },
                    success: function(response) {
                        if (response.exists) {
                            $('#productCodeError').text('This product code is already in use.');
                            $('#addBtn').attr('disabled', true);
                        } else {
                            $('#productCodeError').text('');
                            $('#addBtn').attr('disabled', false);
                        }
                    }
                });
            } else {
                $('#productCodeError').text('');
                $('#addBtn').attr('disabled', true);
            }
        });
    });
</script>
<!-- update Program End -->


@endsection