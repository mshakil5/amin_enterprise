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
                        
                        <form id="createThisForm">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <div class="form-row">

                                        <div class="form-group col-md-3">
                                            <label for="client_id">Client<span style="color: red;">*</span> </label>
                                            <select name="client_id" id="client_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($clients as $client)
                                              <option value="{{$client->id}}">{{$client->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="mv_id">Mother Vassel<span style="color: red;">*</span> </label>
                                            <select name="mv_id" id="mv_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($mvassels as $mvassel)
                                              <option value="{{$mvassel->id}}">{{$mvassel->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="bill_number">Bill Number<span style="color: red;">*</span> </label>
                                            <input type="number" id="bill_number" name="bill_number" class="form-control" value="134904">
                                        </div>

                                        
                                        <div class="form-group col-md-2">
                                            <label>Action</label> <br>
                                            <button type="button" form="createThisForm" id="checkBtn"  class="btn btn-secondary">Check</button>
                                        </div>
                                        
                                    </div>
                                </div>

                            </div>
                            
                        </form>

                        <hr>


                            <div class="row justify-content-md-center">
                                <div class="col-sm-6">
                                    
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
                                              <input type="number" class="form-control" id="netAmount" name="netAmount">
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
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">All Generated bill not received from client.</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th style="text-align: center">Sl</th>
                  <th style="text-align: center">Bill Status</th>
                  <th style="text-align: center">Bill No</th>
                  <th style="text-align: center">Date</th>
                  <th style="text-align: center">Vendor</th>
                  <th style="text-align: center">Header ID</th>
                  <th style="text-align: center">Challan no</th>
                  <th> From-To </th>
                  <th style="text-align: center">Qty</th>
                  <th style="text-align: center">Receivable Amount</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)

                  @php
                      $rate = \App\Models\ClientRate::where('client_id', $data->client_id)->where('destination_id', $data->destination_id)->where('ghat_id', $data->ghat_id)->first();
                      $totalQty = $data->dest_qty;

                      if ($rate) {
                        if ( $totalQty > $rate->maxqty) {
                            $belowAmount = $rate->maxqty * $rate->below_rate_per_qty;
                            $aboveQty = $totalQty - $rate->maxqty;
                            $aboveAmount = $aboveQty * $rate->above_rate_per_qty;
                            $totalAmount = $belowAmount + $aboveAmount;
                        } else {
                            $totalAmount = $totalQty * $rate->below_rate_per_qty;
                        }
                      }
                  @endphp

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
                    <td style="text-align: center">{{$data->challan_no}}</td>
                    <td style="text-align: center">{{$data->ghat->name ?? ''}}-{{$data->destination->name ?? ''}}
                    </td>
                    <td style="text-align: center">{{$data->dest_qty}}</td>
                    <td style="text-align: center">
                        @if (isset($rate))
                        {{$totalAmount}}
                        @endif
                    </td>


                  </tr>
                  @endforeach
                
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <label>Total Amount</label> 
                        </td>
                        <td>
                            <input type="number" id="totalBill" class="form-control" readonly>
                        </td>
                    </tr>
                    
                </tfoot>
              </table>
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


@endsection
@section('script')

<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"],
        "lengthMenu": [[100, "All", 50, 25], [100, "All", 50, 25]]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

      
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
        $(document).on('click', '#checkBtn', function(e) {
            e.preventDefault();

            $(this).attr('disabled', true);
            $('#loader').show();
            $(this).attr('disabled', false);

            var formData = new FormData($('#createThisForm')[0]);

            $.ajax({
                url: '{{ route("admin.checkBill") }}',
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
                        // $("#mv_id").val('');
                        // $("#client_id").val('');
                        $(".ermsg").html(response.message);
                        // $('#programTable tbody').html('');

                    } else {

                        $("#totalAmount").val(response.totalAmount);
                        $("#netAmount").val(response.totalAmount);
                        $("#totalqty").val(response.totalQty);
                        $(".ermsg").html(response.message);

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

<!--  Program after challan data store start -->
<script>
    $(document).ready(function() {


        $(document).on('input', '#maintainance, #otherexp, #scaleCharge, #otherRcv', function() {
            updateSummary();
        });




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
                        $(".ermsg").html(response.message);
                    } else {
                        console.log(response);
                        $(".ermsg").html(response.message);
                        // window.setTimeout(function(){location.reload()},2000)
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