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
                                    
                                </tbody>
                            </table>


                            <div class="row">
                                
                                <div class="col-sm-6">
                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <label for="headerid">Header ID </label>
                                            <input type="text" class="form-control" id="headerid" >
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="totalqtyasperchallan">Quantity as per challan</label>
                                            <input type="number" class="form-control" id="totalqtyasperchallan" >
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="destid">Destination</label>
                                            <select class="form-control" id="destid" name="destid">
                                                <option value="">Select destination</option>
                                                @foreach (\App\Models\Destination::where('status', 1)->get() as $destination)
                                                <option value="{{$destination->id}}">{{$destination->name}}</option>
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
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Advance</td>
                                                <td><span id="advanceAmnt"></span></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>Due</td>
                                                <td></td>
                                            </tr>
                                            
                                        </tfoot>
                                    </table>
                                    
                                    
                                    
                                </div>

                            </div>

                            
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
                            <td style="text-align: center">{{$slbrate->ghat->name}}</td>
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
                    $("#mother_vassel_id").val(response.program.mother_vassel_id);
                    $("#lighter_vassel_id").val(response.program.lighter_vassel_id);
                    $("#ghat_id").val(response.program.ghat_id);
                    $("#consignmentno").val(response.program.consignmentno);
                    $(".ermsg").html(response.message);
                    $('#programTable tbody').append(response.data);
                    
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


<!-- Check slab rate -->
<script>
    $(document).ready(function() {
        $(document).on('change', '#destid', function(e) {
            e.preventDefault();

            var totalqtyasperchallan = $("#totalqtyasperchallan").val();

            if (!totalqtyasperchallan) {
                alert('Please input quantity as per challan first!');
                $("#destid").val('');
                return;
            }
            var formData = new FormData();
            formData.append("destid", $("#destid").val());
            formData.append("ghat", $("#ghat_id").val());
            formData.append("challanqty", $("#totalqtyasperchallan").val());

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
                    
                    console.log(response);
                    $('#rateTable tbody').append(response.rate);

                    // $("#mother_vassel_id").val(response.program.mother_vassel_id);
                    // $("#lighter_vassel_id").val(response.program.lighter_vassel_id);
                    // $("#ghat_id").val(response.program.ghat_id);
                    // $("#consignmentno").val(response.program.consignmentno);
                    // $(".ermsg").html(response.message);
                    
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
<!-- Check slab rate End -->

<script>
    // return stock
    $("#programTable").on('click','.addrateThis', function(){
        alert('work');
        advAmnt = $(this).attr('data-adv');
        prgmDtlId = $(this).attr('data-pdtlid');
        console.log(advAmnt, prgmDtlId);
        $("#advanceAmnt").html(advAmnt);


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
                    console.log(response);
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
@endsection