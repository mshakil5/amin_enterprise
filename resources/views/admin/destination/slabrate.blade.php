@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content mt-3" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
        </div>
      </div>
    </div>
</section>

    <!-- Main content -->
    <section class="content" id="addThisFormContainer">
      <div class="container-fluid">
        <div class="row justify-content-md-center">
          <div class="col-md-10">
            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Add/Edit destination slab rate</h3>
              </div>
              <div class="card-body">
                <div class="ermsg"></div>
                <form id="createThisForm">
                  @csrf
                  <input type="hidden" class="form-control" id="codeid" name="codeid">
                  
                  <!-- Basic Info Row -->
                  <div class="form-row">
                      <div class="form-group col-md-3">
                          <label>Client <span class="text-danger">*</span></label>
                          <select name="client_id" id="client_id" class="form-control" required>
                            <option value="">Select Client</option>
                            @foreach ($clients as $client)
                            <option value="{{$client->id}}">{{$client->name}}</option>
                            @endforeach
                          </select>
                      </div>
                      <div class="form-group col-md-3">
                          <label>Ghat <span class="text-danger">*</span></label>
                          <select name="ghat_id" id="ghat_id" class="form-control" required>
                            <option value="">Select Ghat</option>
                            @foreach ($ghats as $ghat)
                            <option value="{{$ghat->id}}">{{$ghat->name}}</option>
                            @endforeach
                          </select>
                      </div>
                      <div class="form-group col-md-3">
                          <label>Destination <span class="text-danger">*</span></label>
                          <select name="destination_id" id="destination_id" class="form-control" required>
                            <option value="">Select Destination</option>
                            @foreach ($destinations as $dest)
                            <option value="{{$dest->id}}">{{$dest->name}}</option>
                            @endforeach
                          </select>
                      </div>
                      <div class="form-group col-md-3">
                          <label>Title</label>
                          <input type="text" class="form-control" id="title" name="title">
                      </div>
                  </div>

                  <hr>

                  <!-- BSRM FIELDS (Shown only if Client ID is 3) -->
                  <div id="bsrmFields" style="display: none;">
                      <h5>BSRM Slab Rate</h5>
                      <div class="form-row">
                          <div class="form-group col-md-3">
                              <label>Qty (Max)</label>
                              <input type="number" class="form-control" id="qty" name="qty" value="12">
                          </div>
                          <div class="form-group col-md-3">
                              <label>Below Rate</label>
                              <input type="number" class="form-control" id="below_rate_per_qty" name="below_rate_per_qty">
                          </div>
                          <div class="form-group col-md-3">
                              <label>Above Rate</label>
                              <input type="number" class="form-control" id="above_rate_per_qty" name="above_rate_per_qty">
                          </div>
                      </div>
                  </div>

                  <!-- NEW TIERS FIELDS (Shown only if Client ID is NOT 3) -->
                  <div id="newTierFields" style="display: none;">
                      <h5>Multi-Tier Slab Rates</h5>
                      <table class="table table-bordered table-sm" id="tierTable">
                          <thead class="bg-light">
                              <tr>
                                  <th style="width:25%">Min Qty</th>
                                  <th style="width:30%">Max Qty (Leave blank for "and up")</th>
                                  <th style="width:30%">Rate</th>
                                  <th style="width:15%">Action</th>
                              </tr>
                          </thead>
                          <tbody>
                              <tr>
                                  <td><input type="number" step="0.01" name="tiers[0][min_qty]" class="form-control" value="0" required></td>
                                  <td><input type="number" step="0.01" name="tiers[0][max_qty]" class="form-control" placeholder="e.g. 11.99 or blank"></td>
                                  <td><input type="number" step="0.01" name="tiers[0][rate]" class="form-control" required></td>
                                  <td><button type="button" class="btn btn-sm btn-danger removeTier">X</button></td>
                              </tr>
                          </tbody>
                      </table>
                      <button type="button" class="btn btn-sm btn-secondary" id="addTierBtn">+ Add Tier</button>
                  </div>

                </form>
              </div>
              <div class="card-footer">
                <button type="button" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                <button type="button" id="FormCloseBtn" class="btn btn-default">Cancel</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>


<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">All Slab Rates</h3>
            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead class="bg-secondary">
                    <tr>
                        <th>Client</th>
                        <th>Ghat</th>
                        <th>Destination</th>
                        <th>Quantity Range</th>
                        <th>Rate</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $item)
                    <tr>
                        <td>{{ $item->client->name ?? 'N/A' }}</td>
                        <td>{{ $item->ghat->name ?? '' }}</td>
                        <td>{{ $item->destination->name ?? '' }}</td>
                        <td>
                            @if($item->client_id == 3)
                                &le; {{ $item->maxqty }} <br> > {{ $item->maxqty }}
                            @else
                                {{ $item->tier_min_qty }} - {{ $item->tier_max_qty ?? 'Up' }}
                            @endif
                        </td>
                        <td>
                            @if($item->client_id == 3)
                                Below: {{ $item->below_rate_per_qty }} <br> Above: {{ $item->above_rate_per_qty }}
                            @else
                                {{ $item->tier_rate }}
                            @endif
                        </td>
                        <td style="text-align: center">
                          <a id="EditBtn" rid="{{$item->id}}" style="cursor:pointer;"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                          <a id="deleteBtn" rid="{{$item->id}}" style="cursor:pointer;"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>

@endsection

@section('script')
<script>
    $(function () {
      $("#example1").DataTable({
          "responsive": true,
          "lengthChange": true,
          "autoWidth": false,
          "buttons": ["copy", "csv", "excel", "pdf", "print"],
          "pageLength": 50,
          "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<script>
  $(document).ready(function () {
      $("#addThisFormContainer").hide();
      
      // Toggle UI based on Client Selection
      $('#client_id').on('change', function() {
          let clientId = $(this).val();
          if (clientId == '3') {
              $('#bsrmFields').show();
              $('#newTierFields').hide();
          } else if (clientId != '') {
              $('#bsrmFields').hide();
              $('#newTierFields').show();
          } else {
              $('#bsrmFields').hide();
              $('#newTierFields').hide();
          }
      });

      // Show/Hide Form
      $("#newBtn").click(function(){
          clearform();
          $("#newBtn").hide(100);
          $("#addThisFormContainer").show(300);
      });
      
      $("#FormCloseBtn").click(function(){
          $("#addThisFormContainer").hide(200);
          $("#newBtn").show(100);
          clearform();
      });

      // CSRF Setup
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      
      var url = "{{URL::to('/admin/slab-rate')}}";
      var upurl = "{{URL::to('/admin/slab-rate-update')}}";

      // Add Tier Button Logic
      var tierIndex = 1;
      $("#addTierBtn").click(function(){
          var newRow = `<tr>
              <td><input type="number" step="0.01" name="tiers[${tierIndex}][min_qty]" class="form-control" required></td>
              <td><input type="number" step="0.01" name="tiers[${tierIndex}][max_qty]" class="form-control" placeholder="Blank for unlimited"></td>
              <td><input type="number" step="0.01" name="tiers[${tierIndex}][rate]" class="form-control" required></td>
              <td><button type="button" class="btn btn-sm btn-danger removeTier">X</button></td>
          </tr>`;
          $("#tierTable tbody").append(newRow);
          tierIndex++;
      });

      // Remove Tier Row Logic
      $(document).on('click', '.removeTier', function(){
          $(this).closest('tr').remove();
      });

      // UNIFIED CREATE & UPDATE AJAX
      $("#addBtn").click(function(){
          var btnValue = $(this).val();
          var requestUrl = (btnValue == 'Create') ? url : upurl;
          
          var form_data = $('#createThisForm').serialize();

          $.ajax({
              url: requestUrl,
              method: "POST",
              data: form_data,
              success: function (d) {
                  if (d.status == 303 || d.status == 400) {
                      $(".ermsg").html(d.message);
                  } else if(d.status == 300){
                      $(".ermsg").html(d.message);
                      window.setTimeout(function(){ location.reload(); }, 1500);
                  }
              },
              error: function (d) {
                  console.log(d);
              }
          });
      });

      // Edit Button
      $("#contentContainer").on('click','#EditBtn', function(){
          codeid = $(this).attr('rid');
          info_url = url + '/'+codeid+'/edit';
          
          $.get(info_url, {}, function(d){
              populateForm(d);
              $("html, body").animate({ scrollTop: 0 }, "slow");
          });
      });

      // Delete Button
      $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Are you sure you want to delete this?')) return;
            codeid = $(this).attr('rid');
            info_url = url + '/'+codeid;
            $.ajax({
                url: info_url,
                method: "GET",
                data: {_method: 'DELETE'}, 
                success: function(d){
                    if(d.success) {
                        alert(d.message);
                        location.reload();
                    }
                },
                error:function(d){
                    console.log(d);
                }
            });
      });

      // Populate Form for Edit
      function populateForm(data){
          clearform(); 
          
          $("#client_id").val(data.client_id).trigger('change'); // Trigger change to show/hide correct fields
          $("#ghat_id").val(data.ghat_id);
          $("#destination_id").val(data.destination_id);
          $("#title").val(data.title);
          $("#codeid").val(data.id);
          
          if (data.client_id == 3) {
              // Fill old BSRM fields
              $("#qty").val(data.maxqty);
              $("#below_rate_per_qty").val(data.below_rate_per_qty);
              $("#above_rate_per_qty").val(data.above_rate_per_qty);
          } else {
              // Fill new Tier table
              var maxQtyVal = data.tier_max_qty ? data.tier_max_qty : '';
              var minQtyVal = data.tier_min_qty ? data.tier_min_qty : 0;
              var rateVal = data.tier_rate ? data.tier_rate : 0;

              var editRow = `<tr>
                  <td><input type="number" step="0.01" name="tiers[0][min_qty]" class="form-control" value="${minQtyVal}" required></td>
                  <td><input type="number" step="0.01" name="tiers[0][max_qty]" class="form-control" value="${maxQtyVal}" placeholder="Blank for unlimited"></td>
                  <td><input type="number" step="0.01" name="tiers[0][rate]" class="form-control" value="${rateVal}" required></td>
                  <td><button type="button" class="btn btn-sm btn-danger removeTier">X</button></td>
              </tr>`;
              
              $("#tierTable tbody").html(editRow); 
          }

          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);
      }

      function clearform(){
          $('#createThisForm')[0].reset();
          $("#codeid").val('');
          
          // Reset BSRM fields
          $("#qty").val('12');
          
          // Reset dynamic tier table to 1 empty row
          var defaultRow = `<tr>
              <td><input type="number" step="0.01" name="tiers[0][min_qty]" class="form-control" value="0" required></td>
              <td><input type="number" step="0.01" name="tiers[0][max_qty]" class="form-control" placeholder="e.g. 11.99 or blank"></td>
              <td><input type="number" step="0.01" name="tiers[0][rate]" class="form-control" required></td>
              <td><button type="button" class="btn btn-sm btn-danger removeTier">X</button></td>
          </tr>`;
          $("#tierTable tbody").html(defaultRow);
          
          // Hide custom sections until client is selected
          $('#bsrmFields').hide();
          $('#newTierFields').hide();
          
          $("#addBtn").val('Create');
          $("#addBtn").html('Create');
      }
  });
</script>
@endsection