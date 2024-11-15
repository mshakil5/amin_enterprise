@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
{{-- <section class="content mt-3" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <a href="#" class="btn btn-secondary my-3">Back</a>
        </div>
      </div>
    </div>
</section> --}}
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
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="client_id">Client <span style="color: red;">*</span>
                                      <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addCategoryModal">Add New</span>
                                    </label>
                                    
                                    <select name="client_id" id="client_id" class="form-control">
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
                                <div class="form-group col-md-2">
                                    <label for="date">Date <span style="color: red;">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{date('Y-m-d')}}">
                                    <span id="productCodeError" class="text-danger"></span>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="consignmentno">Consignment Number</label>
                                    <input type="text" class="form-control" id="consignmentno" name="consignmentno" >
                                </div>
                                
                                <div class="form-group col-md-2">
                                    <label for="headerid">Header ID</label>
                                    <input type="text" class="form-control" id="headerid" name="headerid" >
                                </div>

                                <div class="form-group col-md-2">
                                  <label for="qty_per_challan">Qty per challan</label>
                                  <input type="number" class="form-control" id="qty_per_challan" name="qty_per_challan" >
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
                                    <label for="lighter_vassel_id">Lighter Vassel <span style="color: red;">*</span>
                                      <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addCategoryModal">Add New</span>
                                    </label>
                                    
                                    <select name="lighter_vassel_id" id="lighter_vassel_id" class="form-control">
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
                                    <label for="amount">Contract Amount</label>
                                    <input type="number" class="form-control" id="camount" name="camount">
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="description">Note</label>
                                    <textarea class="form-control" id="note" name="note"></textarea>
                                </div>
                            </div>

                            

                            <div class="form-row">
                                <div class="form-group col-md-2">
                                    <label for="vendor_id">Vendor</label>
                                    <span class="badge badge-success" style="cursor: pointer;" data-toggle="modal" data-target="#addColorModal">Add New</span>
                                    <select class="form-control" name="vendor_id[]" id="vendor_id">
                                        <option value="">Select Vendor</option>
                                        @foreach ($vendors as $vendor)
                                        <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="truck_number">Truck Number</label>
                                    <input type="text" class="form-control" name="truck_number[]" >
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="qty">Quantity</label>
                                    <input type="number" class="form-control" name="qty[]" >
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="challan_no">Challan No</label>
                                    <input type="number" class="form-control" name="challan_no[]" >
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="line_charge">Line Ch.</label>
                                    <input type="number" class="form-control" name="line_charge[]" >
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="token_fee">Token Fee</label>
                                    <input type="number" class="form-control" name="token_fee[]" >
                                </div>
                                
                                <div class="form-group col-md-1">
                                  <label for="party_name">Party</label>
                                  <input type="text" class="form-control" name="party_name[]" >
                                </div>
                                <div class="form-group col-md-1">
                                    <label for="amount">Amount</label>
                                    <input type="number" class="form-control" name="amount[]" >
                                </div>

                                <div class="form-group col-md-1">
                                    <label>Action</label>
                                    <button type="button" class="btn btn-success add-row"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            <div id="dynamic-rows">

                            </div>

                            

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
      var url = "{{URL::to('/admin/destination')}}";
      var upurl = "{{URL::to('/admin/destination-update')}}";
      // console.log(url);
      $("#addBtn2").click(function(){
        
              var form_data = new FormData();
              form_data.append("name", $("#name").val());
              form_data.append("address", $("#address").val());
              form_data.append("client_id", $("#client_id").val());
              $.ajax({
                url: url,
                method: "POST",
                contentType: false,
                processData: false,
                data:form_data,
                success: function (d) {
                    if (d.status == 303) {
                        $(".ermsg").html(d.message);
                    }else if(d.status == 300){

                      $(".ermsg").html(d.message);
                      window.setTimeout(function(){location.reload()},2000)
                    }
                },
                error: function (d) {
                    console.log(d);
                }
            });
            
          //create  end
          
      });
      //Edit
      $("#contentContainer").on('click','#EditBtn', function(){
          //alert("btn work");
          codeid = $(this).attr('rid');
          //console.log($codeid);
          info_url = url + '/'+codeid+'/edit';
          //console.log($info_url);
          $.get(info_url,{},function(d){
              populateForm(d);
              pagetop();
          });
      });
      //Edit  end
      

      
      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create');
      }
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
                    
                    $(".ermsg").html(response.message);
                    window.setTimeout(function(){location.reload()},2000)

                    // swal({
                    //     text: "Created successfully",
                    //     icon: "success",
                    //     button: {
                    //         text: "OK",
                    //         className: "swal-button--confirm"
                    //     }
                    // }).then(() => {
                    //     // location.reload();
                    // });
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