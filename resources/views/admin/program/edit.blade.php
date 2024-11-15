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
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="client_id">Client <span style="color: red;">*</span>
                                      <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addCategoryModal">Add New</span>
                                    </label>
                                    
                                    <select name="client_id" id="client_id" class="form-control">
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
                                <div class="form-group col-md-2">
                                    <label for="date">Date <span style="color: red;">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ $program->date}}">
                                    <span id="productCodeError" class="text-danger"></span>
                                    <input type="hidden" class="form-control" id="pid" name="pid" value="{{ $program->id}}">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="consignmentno">Consignment Number</label>
                                    <input type="text" class="form-control" id="consignmentno" name="consignmentno"  value="{{ $program->consignmentno}}">
                                </div>
                                
                                <div class="form-group col-md-2">
                                    <label for="headerid">Header ID</label>
                                    <input type="text" class="form-control" id="headerid" name="headerid" value="{{ $program->headerid}}">
                                </div>

                                <div class="form-group col-md-2">
                                  <label for="qty_per_challan">Qty per challan</label>
                                  <input type="number" class="form-control" id="qty_per_challan" name="qty_per_challan"  value="{{ $program->qty_per_challan}}">
                              </div>

                            </div>

                            <div class="form-row">

                                <div class="form-group col-md-4">
                                    <label for="mother_vassel_id">Mother Vassel <span style="color: red;">*</span>
                                      <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addCategoryModal">Add New</span>
                                    </label>
                                    
                                    <select name="mother_vassel_id" id="mother_vassel_id" class="form-control">
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
                                    <label for="lighter_vassel_id">Lighter Vassel <span style="color: red;">*</span>
                                      <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addCategoryModal">Add New</span>
                                    </label>
                                    
                                    <select name="lighter_vassel_id" id="lighter_vassel_id" class="form-control">
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
                                    <label for="camount">Contract Amount</label>
                                    <input type="number" class="form-control" id="camount" name="camount" value="{{ $program->amount}}">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="description">Note</label>
                                    <textarea class="form-control" id="note" name="note">{{ $program->note}}</textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label for="vendor_id">Vendor</label>
                                    <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addColorModal">Add New</span>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="truck_number">Truck Number</label>
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="qty">Quantity</label>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="challan_no">Challan No</label>
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="line_charge">Line Ch.</label>
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="token_fee">Token Fee</label>
                                </div>
                                
                                <div class="form-group col-md-1">
                                  <label for="party_name">Party</label>
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="amount">Amount</label>
                                </div>

                                <div class="form-group col-md-1">
                                    <button type="button" class="btn btn-success add-row"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            
                            @foreach ($program->programDetail as $key => $dtl)

                            <div class="form-row dynamic-row">
                                <div class="form-group col-md-2">
                                    <select class="form-control" name="vendor_id[]" id="vendor_id">
                                        <option value="">Select Vendor</option>
                                        @foreach ($vendors as $vendor)
                                        <option value="{{$vendor->id}}" {{$dtl->vendor_id == $vendor->id ? 'selected' : '' }}>{{$vendor->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="text" class="form-control" name="truck_number[]" value="{{$dtl->truck_number}}">
                                    <input type="hidden" class="form-control" name="program_detail_id[]" value="{{$dtl->id}}">
                                </div>
                                <div class="form-group col-md-1">
                                    <input type="number" class="form-control" name="qty[]" value="{{$dtl->qty}}">
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="number" class="form-control" name="challan_no[]" value="{{$dtl->challan_no}}">
                                </div>
                                <div class="form-group col-md-1">
                                    <input type="number" class="form-control" name="line_charge[]" value="{{$dtl->line_charge}}">
                                </div>
                                <div class="form-group col-md-1">
                                    <input type="number" class="form-control" name="token_fee[]" value="{{$dtl->token_fee}}">
                                </div>
                                
                                <div class="form-group col-md-1">
                                  <input type="text" class="form-control" name="party_name[]" value="{{$dtl->party_name}}">
                                </div>
                                <div class="form-group col-md-1">
                                    <input type="number" class="form-control" name="amount[]" value="{{$dtl->amount}}">
                                </div>

                                <div class="form-group col-md-1">
                                    <button type="button" class="btn btn-danger remove-row"><i class="fas fa-minus"></i></button>
                                </div>
                            </div>

                            @endforeach

                            
                            <div id="dynamic-rows">

                            </div>

                            

                        </form>
                    </div>
                    <div class="card-footer">
                      <button type="submit" form="updateThisForm" id="addBtn"  class="btn btn-secondary">Update</button>
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
      $(document).on('click', '.add-row', function() {
          let newRow = `
          <div class="form-row dynamic-row">
            
              <div class="form-group col-md-2">
                  <select class="form-control" name="vendor_id[]" id="vendor_id">
                      <option value="">Select Vendor</option>
                      @foreach ($vendors as $vendor)
                      <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                      @endforeach
                  </select>
              </div>
              <div class="form-group col-md-2">
                  <input type="text" class="form-control" name="truck_number[]" >
              </div>
              <div class="form-group col-md-1">
                  <input type="number" class="form-control" name="qty[]" >
              </div>
              <div class="form-group col-md-2">
                  <input type="number" class="form-control" name="challan_no[]" >
              </div>
              <div class="form-group col-md-1">
                  <input type="number" class="form-control" name="line_charge[]" >
              </div>
              <div class="form-group col-md-1">
                  <input type="number" class="form-control" name="token_fee[]" >
              </div>
              
              <div class="form-group col-md-1">
                <input type="text" class="form-control" name="party_name[]" >
              </div>
              <div class="form-group col-md-1">
                    <input type="number" class="form-control" name="amount[]" >
                </div>
              <div class="form-group col-md-1">
                  <button type="button" class="btn btn-danger remove-row"><i class="fas fa-minus"></i></button>
              </div>

          </div>`;

          $('#dynamic-rows').append(newRow);
      });

      $(document).on('click', '.remove-row', function() {
          $(this).closest('.dynamic-row').remove();
      });
  });
</script>

<script>
  $(document).ready(function () {
    
      //header for csrf-token is must in laravel
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      //

  });
</script>

<!-- Create Program Start -->
<script>
    $(document).ready(function() {
        $(document).on('click', '#addBtn', function(e) {
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
                        text: "Created successfully",
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
<!-- Create Program End -->


@endsection