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
        <div class="col-2">
            {{-- <a href="{{route('admin.allProgram')}}" class="btn btn-secondary my-3">Back</a> --}}
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
                        <h3 class="card-title" id="cardTitle">Bill Received</h3>
                    </div>
                    
                    <div class="card-body">
                        <div class="ermsg"> </div>
                        
                        {{-- <form id="createThisForm">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <div class="form-row">

                                        <div class="form-group col-md-3">
                                            <label for="client_id">Client </label>
                                            <select name="client_id" id="client_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($clients as $client)
                                              <option value="{{$client->id}}">{{$client->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="mv_id">Mother Vassel </label>
                                            <select name="mv_id" id="mv_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($mvassels as $mvassel)
                                              <option value="{{$mvassel->id}}">{{$mvassel->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="bill_number">Bill Number<span style="color: red;">*</span> </label>
                                            <input type="number" id="bill_number" name="bill_number" class="form-control" value="">
                                        </div>

                                        
                                        <div class="form-group col-md-2">
                                            <label>Action</label> <br>
                                            <button type="button" form="createThisForm" id="checkBtn"  class="btn btn-secondary">Check</button>
                                        </div>
                                        
                                    </div>
                                </div>

                            </div>
                            
                        </form> --}}

                        <hr>


                            <div class="row justify-content-md-center">
                                <div class="col-sm-6">
                                    <div class="billmsg"></div>
                                    <form class="form-horizontal" id="billForm">
                                        <div class="card-body">
                                            
                                          <div class="form-group row">
                                            <label for="date" class="col-sm-4 col-form-label">Total Qty</label>
                                            <div class="col-sm-6">
                                                <input type="date" class="form-control" id="date" name="date" value="{{date('Y-m-d')}}">
                                            </div>
                                          </div>

                                          <div class="form-group row">
                                            <label for="totalqty" class="col-sm-4 col-form-label">Total Qty</label>
                                            <div class="col-sm-6">
                                              <input type="number" class="form-control" id="totalqty" name="totalqty" readonly>
                                            </div>
                                          </div>

                                          
                                          <div class="form-group row">
                                            <label for="rcvType" class="col-sm-4 col-form-label">Received Method</label>
                                            <div class="col-sm-6">
                                                <select name="rcvType" id="rcvType" class="form-control">
                                                    <option value="Bank">Bank</option>
                                                    <option value="Cash">Cash</option>
                                                </select>
                                            </div>
                                          </div>

                                          
                                          <div class="form-group row">
                                            <label for="" class="col-sm-4 col-form-label">Total Amount (BDT)</label>
                                            <div class="col-sm-6">
                                              <input type="number" class="form-control" id="totalAmount" name="totalAmount" readonly>
                                            </div>
                                          </div>

                                          
                                          <div class="form-group row">
                                            <label for="maintainance" class="col-sm-4 col-form-label">Less: Maintainance</label>
                                            <div class="col-sm-6">
                                              <input type="number" class="form-control" id="maintainance" name="maintainance">
                                            </div>
                                          </div>

                                          
                                          <div class="form-group row">
                                            <label for="otherexp" class="col-sm-4 col-form-label">Less: Other Cost</label>
                                            <div class="col-sm-6">
                                              <input type="number" class="form-control" id="otherexp" name="otherexp">
                                            </div>
                                          </div>

                                          
                                          <div class="form-group row">
                                            <label for="scaleCharge" class="col-sm-4 col-form-label">Add: Scale Charge</label>
                                            <div class="col-sm-6">
                                              <input type="number" class="form-control" id="scaleCharge" name="scaleCharge">
                                            </div>
                                          </div>

                                          <div class="form-group row">
                                            <label for="otherRcv" class="col-sm-4 col-form-label">Add: Others Receive</label>
                                            <div class="col-sm-6">
                                              <input type="number" class="form-control" id="otherRcv" name="otherRcv">
                                            </div>
                                          </div>

                                          <div class="form-group row">
                                            <label for="" class="col-sm-4 col-form-label">Total</label>
                                            <div class="col-sm-6">
                                              <input type="number" class="form-control" id="netAmount" name="netAmount" readonly>
                                            </div>
                                          </div>

                                          <div class="form-group row">
                                            
                                            <div class="col-sm-6">
                                                <button type="submit" id="saveBtn" class="btn btn-info float-right">Save</button>
                                            </div>
                                            
                                          </div>
                                          
                                        </div>

                                      </form>
                                </div>

                            </div>
                            

                    </div>
                    <div class="card-footer"> </div>
                </div>
            </div>

            <div class="mt-3"></div>

        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Client</label>
                        <select id="client_id" class="form-control select2">
                          <option value="">Select</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Bill Number</label>
                        <div class="input-group">
                            <input type="text" id="bill_number" class="form-control" placeholder="Search Bill No...">
                            <div class="input-group-append">
                                <button id="searchBtn" class="btn btn-primary"><i class="fas fa-search"></i> Find Bill</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-secondary" id="resultCard" style="display:none;">
            <div class="card-header">
                <h3 class="card-title">Bill Details: <span id="displayBillNo"></span></h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-head-fixed table-striped" id="prgmDtl">
                    <thead class="text-center">
                        <tr>
                            <th>Sl</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Vendor</th>
                            <th>Challan No</th>
                            <th>Header ID</th>
                            <th>From - To</th>
                            <th>Scale Fee</th>
                            <th>Prev. Qty</th>
                            <th>Prev. Amount</th>
                            <th>Qty</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody id="billTableBody">
                        </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <th colspan="7" class="text-right">Grand Total:</th>
                            <th class="text-center" id="totalScaleFee">0.00</th>
                            <th class="text-center" id="footerPevQty">0</th>
                            <th class="text-center text-success" id="footerPevTotal">0.00</th>
                            <th class="text-center" id="footerQty">0</th>
                            <th class="text-center text-success" id="footerTotal">0.00</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->


@endsection
@section('script')

<script>
    $(function () {
      $("#prgmDtl").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"],
        "lengthMenu": [[100, "All", 50, 25], [100, "All", 50, 25]]
      }).buttons().container().appendTo('#prgmDtl_wrapper .col-md-6:eq(0)');

      
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


<!-- Create check challan Start -->
<script>
    $(document).ready(function() {


    $('#searchBtn').click(function() {
        const billNo = $('#bill_number').val();
        const clientId = $('#client_id').val();

        if (!billNo) {
            alert('Please enter a Bill Number');
            return;
        }

        $.ajax({
            url: "{{ route('admin.checkBill') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                bill_number: $('#bill_number').val(),
                client_id: $('#client_id').val()
            },
            success: function(response) {
                $('#searchBtn').prop('disabled', false).html('<i class="fas fa-search"></i> Find Bill');
                
                if (response.status === 200) {
                    // 1. Get the DataTable instance
                    var table = $('#prgmDtl').DataTable();

                    // 2. Clear the existing table data
                    table.clear().destroy(); 

                    // 3. Inject the new HTML rows into the tbody
                    $('#billTableBody').html(response.html);

                    // 4. Re-initialize DataTable so it "sees" the new rows
                    $("#prgmDtl").DataTable({
                        "responsive": true, 
                        "lengthChange": false, 
                        "autoWidth": false,
                        "buttons": ["copy", "csv", "excel", "pdf", "print"],
                        "lengthMenu": [[100, 50, 25, -1], [100, 50, 25, "All"]]
                    }).buttons().container().appendTo('#prgmDtl_wrapper .col-md-6:eq(0)');

                    // 5. Update footer totals
                    $('#totalScaleFee').text(response.totalscalefee);
                    $('#footerQty').text(response.totalQty);
                    $('#footerTotal').text(response.totalAmount);
                    $('#footerPevQty').text(response.totalPrevQty);
                    $('#footerPevTotal').text(response.totalprevAmount);

                    $('#resultCard').fadeIn();
                } else {
                    alert(response.message);
                }
            }
        });
    });



    // Reusable Calculation Function
    function calculateNetAmount() {
        var totalAmount = parseFloat($('#totalAmount').val()) || 0;
        var maintainance = parseFloat($('#maintainance').val()) || 0;
        var otherexp = parseFloat($('#otherexp').val()) || 0;
        var scaleCharge = parseFloat($('#scaleCharge').val()) || 0;
        var otherRcv = parseFloat($('#otherRcv').val()) || 0;
        
        var netamnt = totalAmount + scaleCharge + otherRcv - otherexp - maintainance;
        $("#netAmount").val(netamnt.toFixed(2));
    }

    // Trigger on typing
    $(document).on('keyup', '#maintainance, #otherexp, #scaleCharge, #otherRcv', function() {
        calculateNetAmount();
    });




});
</script>
<!-- Create  check challan End -->

<!--  Program after challan data store start -->
<script>

        $(document).on('keyup', '#maintainance, #otherexp, #scaleCharge, #otherRcv', function() {
            var totalAmount = parseFloat($('#totalAmount').val()) || 0;
            var maintainance = parseFloat($('#maintainance').val()) || 0;
            var otherexp = parseFloat($('#otherexp').val()) || 0;
            var scaleCharge = parseFloat($('#scaleCharge').val()) || 0;
            var otherRcv = parseFloat($('#otherRcv').val()) || 0;
            
            var netamnt = totalAmount + scaleCharge + otherRcv - otherexp - maintainance;
            console.log(netamnt);
            $("#netAmount").val(netamnt);
        });


    $(document).ready(function() {


        




        $(document).on('click', '#saveBtn', function(e) {
            e.preventDefault();

            // console.log(prgmdtlid, vendorid, truck_number, fuelqty, fuel_rate, fuel_amount, tamount, fueltoken, tamount,);
            var formData = new FormData($('#billForm')[0]);
            formData.append("client_id", $('#client_id').val());
            formData.append("mv_id", $('#mv_id').val());
            formData.append("bill_number", $('#bill_number').val());
            

            $.ajax({
                url: '{{ route("admin.billStore") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    if (status == 400) {
                        $(".billmsg").html(response.message);
                    } else {
                        console.log(response);
                        $(".billmsg").html(response.message);
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