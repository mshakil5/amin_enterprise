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

<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Mother Vassel: {{$data->motherVassel->name}}</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Date</th>
                  <th>Vendor</th>
                  <th>Header ID</th>
                  <th>Truck Number</th>
                  <th>Challan no</th>
                  <th>Destination</th>
                  <th>Qty</th>
                  <th>Carring Bill</th>
                  <th>Line Charge</th>
                  <th>Scale fee</th>
                  <th>Other Cost</th>
                  <th>Advance</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data->programDetail as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y')}}</td>
                    <td style="text-align: center">{{$data->vendor->name}}</td>
                    <td style="text-align: center">{{$data->headerid}}</td>
                    <td style="text-align: center">{{$data->truck_number}}</td>
                    <td style="text-align: center">{{$data->challan_no}}</td>
                    <td style="text-align: center">{{$data->destination->name ?? ' '}}</td>
                    <td style="text-align: center">{{$data->dest_qty}}</td>
                    <td style="text-align: center">{{$data->carrying_bill}}</td>
                    <td style="text-align: center">{{$data->line_charge}}</td>
                    <td style="text-align: center">{{$data->scale_fee}}</td>
                    <td style="text-align: center">{{$data->other_cost}}</td>
                    <td style="text-align: center">{{$data->advance}}</td>

                    {{-- <td style="text-align: center">
                      <span class="badge badge-success adv-btn" style="cursor: pointer;" data-id="{{ $data->id }}" data-vendor-id="{{ $data->vendor_id }}" data-program-id="{{ $data->program_id }}">Advance Pay</span>

                      <span class="badge badge-secondary trn-btn" style="cursor: pointer;" data-id="{{ $data->id }}" data-vendor-id="{{ $data->vendor_id }}">Transaction</span>

                      @if ($data->programDestination)
                        <a class="btn btn-app destUpBtn" id="destinationUpBtn" rid="{{ $data->id }}" data-id="{{ $data->id }}" data-pdid="{{ $data->programDestination->id }}" data-vendor-id="{{ $data->vendor_id }}" data-program-id="{{ $data->program_id }}">
                          <i class="fa fa-map-marker" aria-hidden="true"></i> Destination
                        </a>
                      @else
                        <a class="btn btn-app destBtn" id="destinationBtn" rid="{{ $data->id }}" data-id="{{ $data->id }}" data-vendor-id="{{ $data->vendor_id }}" data-program-id="{{ $data->program_id }}">
                          <i class="fa fa-map-marker" aria-hidden="true"></i> Destination
                        </a>
                      @endif
                    </td> --}}

                    <td style="text-align: center">
                        {{-- <a class="btn btn-app" id="trnEditBtn" rid="{{ $data->id }}">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a class="btn btn-app" id="trndeleteBtn" rid="{{ $data->id }}">
                            <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>Delete
                        </a> --}}
                    </td>
                  </tr>
                  @endforeach
                
                </tbody>
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

<div class="modal fade" id="advModal" tabindex="-1" role="dialog" aria-labelledby="advModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="advModalLabel">Vendor Advance Payment Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="payForm">
                <div class="modal-body">


                    <div class="form-group">
                        <label for="payment_type">Advance Item<span style="color: red;">*</span></label>
                        <select name="payment_type" id="payment_type" class="form-control" >
                            <option value="Fuel">Fuel</option>
                            <option value="Money">Money</option>
                        </select>
                    </div>

                    <div id="fuelDiv">
                      <div class="form-group">
                        <label for="petrol_pump_id">Petrol Pump <span style="color: red;">*</span></label>
                        <select name="petrol_pump_id" id="petrol_pump_id" class="form-control" >
                          <option value="">Select</option>
                          @foreach ($pumps as $pump)
                            <option value="{{$pump->id}}">{{$pump->name}}</option>
                          @endforeach
                        </select>
                      </div>
  
                      <div class="row">
                        <div class="form-group p-3">
                          <label for="fuel_rate">Fuel Rate <span style="color: red;">*</span></label>
                          <input type="number" class="form-control" id="fuel_rate" name="fuel_rate">
                        </div>
    
                        <div class="form-group p-3">
                            <label for="fuelqty">Fuel Qty <span style="color: red;">*</span></label>
                            <input type="number" class="form-control" id="fuelqty" name="fuelqty">
                        </div>
                      </div>
                    </div>

                  <div class="form-group">
                      <label for="paymentAmount">Payment Amount <span style="color: red;">*</span></label>
                      <input type="number" class="form-control" id="paymentAmount" name="paymentAmount" placeholder="Enter payment amount">
                  </div>

                  <div class="form-group">
                    <label for="receiver_name">Receiver Name<span style="color: red;">*</span></label>
                    <input type="text" class="form-control" id="receiver_name" name="receiver_name">
                  </div>
                  
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Pay</button>
                </div>
            </form>
        </div>
    </div>
</div>


 <!-- advance transaction -->
 <div class="modal fade" id="trnModal" tabindex="-1" role="dialog" aria-labelledby="trnModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="trnModalLabel">Vendor Advance Transaction</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">

            <table class="table table-striped table-hover trnsitem" id="trnsitem" style="width: 100%">
              <thead>
                <th>Payment Type</th>
                <th>Amount</th>
                <th>Receiver Name</th>
                <th>Action</th>
              </thead>
              <tbody id="trnData">
                
              </tbody>
            </table>

            
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
      </div>
  </div>
</div>

<!-- Destination-->
<div class="modal fade" id="destModal" tabindex="-1" role="dialog" aria-labelledby="destModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="destModalLabel">Add destination Form</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <form id="destForm">
              <div class="modal-body">


                  <div class="form-group">
                      <label for="destination_id">Destination<span style="color: red;">*</span></label>
                      <select name="destination_id" id="destination_id" class="form-control" >
                          <option value="">Select</option>
                          @foreach (\App\Models\Destination::where('status', 1)->select('id','name')->get() as $destination)
                          <option value="{{$destination->id}}">{{$destination->name}}</option>
                          @endforeach
                      </select>
                  </div>

                  <h5>Add slab rate</h5>
                  <hr>

                  <div class="form-row p-2">
                    {{-- <div class="form-group col-md-3">
                        <label for="minqty">Min Qty</label>
                        <input type="number" class="form-control" name="minqty[]" value="0">
                    </div> --}}
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
                        <button type="button" class="btn btn-success add-row"><i class="fas fa-plus"></i></button>
                    </div>
                </div>


                <div id="dynamic-rows" class="p-2"></div>

                
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-success">Create</button>
              </div>
          </form>
      </div>
  </div>
</div>
<!-- Destination end-->

<!-- Destination-->
<div class="modal fade" id="destUpModal" tabindex="-1" role="dialog" aria-labelledby="destUpModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="destUpModalLabel">Add destination Form</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <form id="destUpForm">
              <div class="modal-body">
                  <div class="form-group">
                    <input type="hidden" id="pdstid">
                      <label for="updestid">Destination<span style="color: red;">*</span></label>
                      <select name="updestid" id="updestid" class="form-control" >
                          <option value="">Select</option>
                          @foreach (\App\Models\Destination::where('status', 1)->select('id','name')->get() as $destination)
                          <option value="{{$destination->id}}">{{$destination->name}}</option>
                          @endforeach
                      </select>
                  </div>

                  <h5>Add slab rate</h5>
                  <hr>

                  <div class="form-row">
                    <div class="form-group col-md-3">
                        <label for="maxqty">Max Qty</label>
                    </div>

                    <div class="form-group col-md-6">
                      <label for="rate_per_qty">Rate per Qty</label>
                    </div>

                    <div class="form-group col-md-1">
                        <button type="button" class="btn btn-success add-row"><i class="fas fa-plus"></i></button>
                    </div>
                  </div>


                <div id="dynamic-up-rows" class=""></div>

                
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-success">Update</button>
              </div>
          </form>
      </div>
  </div>
</div>
<!-- Destination end-->

@endsection
@section('script')
<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
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


  <!-- Dynamic Row Script -->
<script>
  $(document).ready(function() {
      $(document).on('click', '.add-row', function() {
          let newRow = `
          <div class="form-row dynamic-row">
                    <div class="form-group col-md-3">
                        <input type="number" class="form-control" name="maxqty[]"  value="">
                    </div>

                    <div class="form-group col-md-6">
                        <input type="number" class="form-control" name="rate_per_qty[]" value="">
                    </div>

                    <div class="form-group col-md-1">
                        <button type="button" class="btn btn-danger remove-row"><i class="fas fa-minus"></i></button>
                    </div>
                </div>`;

          $('#dynamic-rows').append(newRow);
          $('#dynamic-up-rows').append(newRow);
      });

      $(document).on('click', '.remove-row', function() {
          $(this).closest('.dynamic-row').remove();
      });
  });
</script>



<script>
  $(document).ready(function () {


    $(function() {
      $('#fuelDiv').show(); 
        $('#payment_type').change(function(){
            if($('#payment_type').val() == 'Money') {
                $('#fuelDiv').hide();
            } else { 
              $('#fuelDiv').show(); 
            } 
        });
    });

    //
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    //


      $("#contentContainer").on('click', '.adv-btn', function () {
          var id = $(this).data('id');
          var vendorId = $(this).data('vendor-id');
          var programId = $(this).data('program-id');
          console.log(vendorId);
          $('#advModal').modal('show');
          
          $('#payForm').off('submit').on('submit', function (event) {
              event.preventDefault();

              var form_data = new FormData();
              form_data.append("id", id);
              form_data.append("vendorId", vendorId);
              form_data.append("programId", programId);
              form_data.append("paymentAmount", $("#paymentAmount").val());
              form_data.append("payment_type", $("#payment_type").val());
              form_data.append("petrol_pump_id", $("#petrol_pump_id").val());
              form_data.append("fuel_rate", $("#fuel_rate").val());
              form_data.append("fuelqty", $("#fuelqty").val());
              form_data.append("receiver_name", $("#receiver_name").val());

              $.ajax({
                  url: '{{ URL::to('/admin/vendor-advance-pay') }}',
                  method: 'POST',
                  data:form_data,
                  contentType: false,
                  processData: false,
                  success: function (response) {
                      $('#advModal').modal('hide');
                      swal({
                          text: "Data store successfully",
                          icon: "success",
                          button: {
                              text: "OK",
                              className: "swal-button--confirm"
                          }
                      }).then(() => {
                          location.reload();
                      });
                  },
                  error: function (xhr) {
                      console.log(xhr.responseText);
                  }
              });
          });
      });

      $('#advModal').on('hidden.bs.modal', function () {
          $('#paymentAmount').val('');
      });


      $("#contentContainer").on('click', '.trn-btn', function () {
          var pdid = $(this).data('id');
          var vendorId = $(this).data('vendor-id');
          var programId = $(this).data('program-id');
          
          var form_data = new FormData();
          form_data.append("pdid", pdid);
          
            $.ajax({
                url: '{{ URL::to('/admin/vendor-advance-transaction') }}',
                method: 'POST',
                data:form_data,
                contentType: false,
                processData: false,
                success: function (d) {
                  console.log(d);
                    $('#trnModal').modal('show');
                    // trnsitem
                    var trnsitem = $("#trnsitem tbody");
                    trnsitem.empty();
                    $.each(d.data, function (a, b) {
                    
                        trnsitem.append("<tr><td style='width: 10%; text-align:center'>" + b.payment_type + "</td><td style='width: 10%; text-align:center'>" + b.amount + "</td><td style='width: 10%; text-align:center'>" + b.receiver_name + "</td><td style='width: 10%; text-align:center'><a class='btn btn-app' id='trnEditBtn' rid=" + b.id + "><i class='fas fa-edit'></i> Edit </a><a class='btn btn-app' id='trndeleteBtn' rid=" + b.id + "> <i class='fa fa-trash-o' style='color: red; font-size:16px;'></i>Delete</a></td></tr>"); 

                    });
                    // trnsitem end
                    console.log(trnsitem);
                    
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                }
            });
              

      });

      // add destination
      $("#contentContainer").on('click', '.destBtn', function () {
          var id = $(this).data('id');
          var vendorId = $(this).data('vendor-id');
          var programId = $(this).data('program-id');
          console.log(vendorId);
          $('#destModal').modal('show');
          
          $('#destForm').off('submit').on('submit', function (event) {
              event.preventDefault();
              $(this).attr('disabled', true);
              $('#loader').show();
              var formData = new FormData($('#destForm')[0]);
              formData.append("prgmdtlid", id);
              formData.append("vendorId", vendorId);
              formData.append("programId", programId);
              

              $.ajax({
                  url: '{{ URL::to('/admin/add-destination-slab-rate') }}',
                  method: 'POST',
                  data:formData,
                  contentType: false,
                  processData: false,
                  success: function (response) {
                    console.log(response);
                      $('#advModal').modal('hide');
                      swal({
                          text: "Data store successfully",
                          icon: "success",
                          button: {
                              text: "OK",
                              className: "swal-button--confirm"
                          }
                      }).then(() => {
                          location.reload();
                      });
                  },
                  error: function (xhr) {
                      console.log(xhr.responseText);
                  }
              });
          });
      });



      // update destination
      $("#contentContainer").on('click', '.destUpBtn', function () {
          var id = $(this).data('id');
          var vendorId = $(this).data('vendor-id');
          var programId = $(this).data('program-id');
          var pdstid = $(this).data('pdid');
          console.log(pdstid);
          $('#destUpModal').modal('show');
              var formData = new FormData();
              formData.append("prgmdtlid", id);
              formData.append("vendorId", vendorId);
              formData.append("programId", programId);
              formData.append("pdid", pdstid);
              $.ajax({
                  url: '{{ URL::to('/admin/get-destination-slab-rate') }}',
                  method: 'POST',
                  data:formData,
                  contentType: false,
                  processData: false,
                  success: function (response) {
                      $('#updestid').val(response.data.destination_id);
                      $('#pdstid').val(response.data.id);
                      $('#dynamic-up-rows').html(response.rates);
                      
                  },
                  error: function (xhr) {
                      console.log(xhr.responseText);
                  }
              });


            $('#destUpForm').off('submit').on('submit', function (event) {
              event.preventDefault();
              $(this).attr('disabled', true);
              $('#loader').show();
              var formData = new FormData($('#destUpForm')[0]);
              formData.append("prgmdtlid", id);
              formData.append("pdid", pdstid);
              formData.append("vendorId", vendorId);
              formData.append("programId", programId);
              $.ajax({
                  url: '{{ URL::to('/admin/destination-slab-rate-update') }}',
                  method: 'POST',
                  data:formData,
                  contentType: false,
                  processData: false,
                  success: function (response) {
                      $('#advModal').modal('hide');
                      swal({
                          text: "Data updated successfully",
                          icon: "success",
                          button: {
                              text: "OK",
                              className: "swal-button--confirm"
                          }
                      }).then(() => {
                          location.reload();
                      });
                  },
                  error: function (xhr) {
                      console.log(xhr.responseText);
                  }
              });
            });
      });

          


  });
</script>

<script>
  $(document).ready(function () {

    
  });
</script>
@endsection