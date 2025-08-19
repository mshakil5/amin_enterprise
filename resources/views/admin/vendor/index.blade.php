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
    
    .select2-container {
        width: 100% !important;
    }
</style>


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
  <!-- /.content -->



    <!-- Main content -->
    <section class="content" id="addThisFormContainer">
      <div class="container-fluid">
        <div class="row justify-content-md-center">
          <!-- right column -->
          <div class="col-md-8">
            <!-- general form elements disabled -->
            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Add new vendor</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="ermsg"></div>
                <form id="createThisForm">
                  @csrf
                  <input type="hidden" class="form-control" id="codeid" name="codeid">
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Name*</label>
                        <input type="text" class="form-control" id="name" name="name">
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                      </div>
                    </div>

                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                      </div>
                    </div>

                    
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>Address</label>
                        <input type="text" class="form-control" id="address" name="address">
                      </div>
                    </div>

                    
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>Company Name</label>
                        <input type="text" class="form-control" id="company" name="company">
                      </div>
                    </div>

                    <div  class="col-sm-12" id="PreBlanace">
                      
                    </div>




                  </div>

                  
                </form>
              </div>

              
              <!-- /.card-body -->
              <div class="card-footer">
                <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
              </div>
              <!-- /.card-footer -->
              <!-- /.card-body -->
            </div>
          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
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
              <h3 class="card-title">All Data</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Advance Balance</th>
                  <th>Wallet</th>
                  <th>Add Sequence Number</th>
                  <th>Ledger</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">{{$data->name}}</td>
                    <td style="text-align: center">{{$data->email}}</td>
                    <td style="text-align: center">{{$data->phone}}</td>
                    <td style="text-align: center">{{$data->balance}}</td>
                    <td style="text-align: center">
                      
                      <span class="btn btn-success btn-xs add-money-btn" style="cursor: pointer;" data-id="{{ $data->id }}">Wallet</span>

                      <a class="btn btn-info btn-xs viewtranbtn" style="cursor: pointer;" target="bla
                      " href="{{ route('getWalletTransaction', $data->id)}}">Tran</a>

                    </td>
                    <td style="text-align: center">
                      
                      <span class="btn btn-success btn-xs add-sq-btn" style="cursor: pointer;" data-id="{{ $data->id }}">+ add</span>

                      <span class="btn btn-info btn-xs view-btn" style="cursor: pointer;" data-id="{{ $data->id }}">View</span>

                    </td>

                    <td style="text-align: center">
                        <a href="{{ url('/admin/ledger/vendor/' . $data->id) }}" class="btn btn-default btn-xs">Ledger Details</a>
                    </td>
                    <td style="text-align: center">
                      <a id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                      {{-- <a id="deleteBtn" rid="{{$data->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a> --}}
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


<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="payModalLabel">Add sequence form</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <form id="payForm">
              <div class="modal-body">
                <div class="permsg"></div>
                  <div class="form-group">
                      <label for="challanqty">Challan Qty <span style="color: red;">*</span></label>
                      <input type="number" class="form-control" id="challanqty" name="challanqty" >
                  </div>

                  
                  {{-- <div class="form-group">
                    <label for="sequence">Sequence<span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="sequence" name="sequence" >
                </div> --}}

              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-warning">Create</button>
              </div>
          </form>
      </div>
  </div>
</div>



<div class="modal fade" id="tranModal" tabindex="-1" role="dialog" aria-labelledby="tranModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="tranModalLabel">Sequence number and quantity</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>

          <div class="modal-body">
              <div class="seqErrmsg"></div>
              <div class="form-group">
                  <label for="tranTableSearch">Search:</label>
                  <input type="search" id="tranTableSearch" class="form-control" placeholder="Search transactions...">
              </div>
              <table id="trantable" class="table table-bordered table-striped">
                  <thead>
                      <tr>
                          <th class="d-none">SL</th>
                          <th>Date</th>
                          <th>Challan</th>
                          <th>Challan Store</th>
                          <th>Sequence</th>
                          <th>Due/Advance</th>
                          <th>Unique ID</th>
                          <th>Action</th>
                          <th>Checked By</th>
                          <th>Approved By</th>
                          <th>Edit</th>
                      </tr>
                  </thead>
                  <tbody></tbody>
              </table>
          </div>
      </div>
  </div>
</div>

<div class="modal fade" id="editQtyModal" tabindex="-1" role="dialog" aria-labelledby="editQtyModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="updateQtyForm" method="POST" action="{{ route('admin.vendor.sequence.qty.update') }}">
      @csrf
      @method('POST')
      <input type="hidden" name="vendor_sequence_id" id="vendor_sequence_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editQtyModalLabel">Update Quantity</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="edit_qty">Quantity</label>
            <input type="number" class="form-control" name="qty" id="edit_qty" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal for add money -->
<div class="modal fade" id="addWalletModal" tabindex="-1" role="dialog" aria-labelledby="addWalletLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="addWalletLabel">Add balance to wallet</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <form id="addWalletForm">
              <div class="modal-body">
                <div class="permsg"></div>
                  <div class="form-group">
                    <label for="wallet_date">Date <span style="color: red;">*</span></label>
                    <input type="date" class="form-control" id="wallet_date" name="wallet_date" value="{{ date('Y-m-d') }}" required>
                  </div>
                  <div class="form-group">
                      <label for="walletamount">Amount <span style="color: red;">*</span></label>
                      <input type="number" class="form-control" id="walletamount" name="walletamount" >
                  </div>
                  <div class="form-group">
                      <label for="walletamount">Payment Type <span style="color: red;">*</span></label>
                      <select name="payment_type" id="payment_type" class="form-control">
                        <option value="Cash">Cash</option>
                        <option value="Bank">Bank</option>
                      </select>
                  </div>
                  <div class="form-group">
                      <label for="account_id">Account <span style="color: red;">*</span></label>
                      <select name="account_id" id="account_id" class="form-control">
                        @foreach (\App\Models\Account::latest()->get() as $item)
                        <option value="{{$item->id}}">{{$item->type}}</option>
                        @endforeach
                      </select>
                  </div>
                  <div class="form-group">
                      <label for="vsequence">Sequence Number <span style="color: red;">*</span></label> <br>
                      <select name="vsequence" id="vsequence" class="form-control select2" required>
                          <option value="">Select Sequence Number</option>
                          <!-- Options will be populated dynamically -->
                      </select>
                  </div>
                  <div class="form-group">
                      <label for="note">Note</label>
                      <textarea class="form-control" id="note" rows="3"></textarea>
                  </div>


              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-warning">Add Money</button>
              </div>
          </form>
      </div>
  </div>
</div>

@endsection
@section('script')
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"],
      "lengthMenu": [[100, "All", 50, 25], [100, "All", 50, 25]]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

  });

  $(document).on('click', '.editQtyBtn', function () {
      const id = $(this).data('id');
      const qty = $(this).data('qty');

      $('#vendor_sequence_id').val(id);
      $('#edit_qty').val(qty);
      $('#editQtyModal').modal('show');
  });

  $(document).on('submit', '#updateQtyForm', function(e) {
      e.preventDefault();

      $.ajax({
          url: $(this).attr('action'),
          method: 'POST',
          data: $(this).serialize(),
          success: function(response) {
              if(response.status === 200){
                  alert(response.message);
                  location.reload();
              } else {
                  alert('Update failed!');
              }
          },
          error: function(xhr, status, error) {
            console.error(xhr.responseText);
              alert('An error occurred.');
          }
      });
  });
</script>

<script>

  var vendorSeqNums = @json($vendorSeqNums);
  $(document).ready(function () {

    // Bind custom search input to trantable
    $('#tranTableSearch').on('keyup', function () {
        // $('#trantable').DataTable().search(this.value).draw();
    });

    
      $("#addThisFormContainer").hide();
      $("#newBtn").click(function(){
          clearform();
          $("#PreBlanace").html('');
          $("#newBtn").hide(100);
          $("#addThisFormContainer").show(300);

      });
      $("#FormCloseBtn").click(function(){
          $("#addThisFormContainer").hide(200);
          $("#newBtn").show(100);
          clearform();
      });
      //header for csrf-token is must in laravel
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      //
      var url = "{{URL::to('/admin/vendor')}}";
      var upurl = "{{URL::to('/admin/vendor-update')}}";
      // console.log(url);
      $("#addBtn").click(function(){
      //   alert("#addBtn");
          if($(this).val() == 'Create') {
              var form_data = new FormData();
              form_data.append("name", $("#name").val());
              form_data.append("phone", $("#phone").val());
              form_data.append("email", $("#email").val());
              form_data.append("address", $("#address").val());
              form_data.append("company", $("#company").val());
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
          }
          //create  end
          //Update
          if($(this).val() == 'Update'){
              var form_data = new FormData();
              form_data.append("name", $("#name").val());
              form_data.append("phone", $("#phone").val());
              form_data.append("email", $("#email").val());
              form_data.append("address", $("#address").val());
              form_data.append("company", $("#company").val());
              form_data.append("opening_balance", $("#opening_balance").val());
              form_data.append("codeid", $("#codeid").val());
              
              $.ajax({
                  url:upurl,
                  type: "POST",
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  data:form_data,
                  success: function(d){
                      console.log(d);
                      if (d.status == 303) {
                          $(".ermsg").html(d.message);
                          pagetop();
                      }else if(d.status == 300){
                        $(".ermsg").html(d.message);
                          window.setTimeout(function(){location.reload()},2000)
                      }
                  },
                  error:function(d){
                      console.log(d);
                  }
              });
          }
          //Update
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
      //Delete 
      $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Sure?')) return;
            codeid = $(this).attr('rid');
            info_url = url + '/'+codeid;
            $.ajax({
                url:info_url,
                method: "GET",
                type: "DELETE",
                data:{
                },
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
      //Delete  
      function populateForm(data){
          $("#name").val(data.name);
          $("#phone").val(data.phone);
          $("#email").val(data.email);
          $("#address").val(data.address);
          $("#company").val(data.company);
          $("#codeid").val(data.id);
          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#PreBlanace").html('<div class="form-group"><label>Starting Balance (Date: 2025-07-20)</label><input type="number" class="form-control" id="opening_balance" name="opening_balance" value="' + data.opening_balance + '"></div>');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);
      }
      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create');
      }

      $("#contentContainer").on('click', '.add-sq-btn', function () {
          var id = $(this).data('id');
          $('#payModal').modal('show');
          $('#payForm').off('submit').on('submit', function (event) {
              event.preventDefault();

              var form_data = new FormData();
              form_data.append("vendorId", id);
              form_data.append("challanqty", $("#challanqty").val());
              // form_data.append("sequence", $("#sequence").val());

              if (!$("#challanqty").val()) {
                  alert('Please enter challan quantity.');
                  return;
              }


              $.ajax({
                  url: '{{ URL::to('/admin/add-vendor-sequence') }}',
                  method: 'POST',
                  data:form_data,
                  contentType: false,
                  processData: false,
                  // dataType: 'json',
                  success: function (response) {
                    if (response.status == 303) {
                        $(".permsg").html(response.message);
                    }else if(response.status == 300){

                      $(".permsg").html(response.message);
                      window.setTimeout(function(){location.reload()},2000)
                    }
                    
                      console.log(response);
                      $('#payModal').modal('hide');

                  },
                  error: function (xhr) {
                      console.log(xhr.responseText);
                  }
              });
          });
      });

      $('#payModal').on('hidden.bs.modal', function () {
            $('#paymentAmount').val('');
            $('#paymentNote').val('');
      });


      $("#contentContainer").on('click', '.view-btn', function () {
          var id = $(this).data('id');
          $('#tranModal').modal('show');
              // console.log(id);
              var form_data = new FormData();
              form_data.append("vendorId", id);

              $.ajax({
                  url: '{{ URL::to('/admin/get-vendor-sequence') }}',
                  method: 'POST',
                  data:form_data,
                  contentType: false,
                  processData: false,
                  // dataType: 'json',
                  success: function (response) {
                    // console.log(response);
                      $('#trantable tbody').html(response.data);
                  },
                  error: function (xhr) {
                      console.log(xhr.responseText);
                  }
              });
      });


      var vsqurl = "{{URL::to('/admin/vendor-sequence')}}";
      $("#trantable tbody").on('click', '.seqDeleteBtn', function () {
          var id = $(this).data('rid');
            if(!confirm('Sure?')) return;
            codeid = $(this).attr('rid');
            info_url = vsqurl + '/'+codeid;
            $.ajax({
                url:info_url,
                method: "GET",
                type: "DELETE",
                data:{
                },
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

      var appurl = "{{URL::to('/admin/vendor-sequence-approved')}}";
      $("#trantable tbody").on('click', '.approvedBtn', function () {
          var id = $(this).data('vsid');
          $(this).attr('disabled', true);
          $('#loader').show();

          var form_data = new FormData();
          form_data.append("vsId", id);

          $.ajax({
              url: appurl,
              method: 'POST',
              data:form_data,
              contentType: false,
              processData: false,
              success: function (response) {
                if (response.status == 303) {
                    $(".seqErrmsg").html(response.message);
                }else if(response.status == 300){
                  $(".seqErrmsg").html(response.message);
                }
              },
              error: function (xhr) {
                  console.log(xhr.responseText);
              }
          });
      });


      var chkurl = "{{URL::to('/admin/vendor-sequence-checked')}}";
      $("#trantable tbody").on('click', '.checkedBtn', function () {
          var id = $(this).data('vsid');
          $(this).attr('disabled', true);
          $('#loader').show();

          var form_data = new FormData();
          form_data.append("vsId", id);

          $.ajax({
              url: chkurl,
              method: 'POST',
              data:form_data,
              contentType: false,
              processData: false,
              success: function (response) {
                if (response.status == 303) {
                    $(".seqErrmsg").html(response.message);
                }else if(response.status == 300){
                  $(".seqErrmsg").html(response.message);
                }
              },
              error: function (xhr) {
                  console.log(xhr.responseText);
              }
          });
      });


      // vendor wallet add
      // $("#contentContainer").on('click', '.add-money-btn', function () {
      //     var id = $(this).data('id');
      //     $('#addWalletModal').modal('show');
      //     $('#addWalletForm').off('submit').on('submit', function (event) {
      //         event.preventDefault();

      //         var form_data = new FormData();
      //         form_data.append("vendorId", id);
      //         form_data.append("walletamount", $("#walletamount").val());
      //         form_data.append("payment_type", $("#payment_type").val());
      //         form_data.append("account_id", $("#account_id").val());
      //         form_data.append("wallet_date", $("#wallet_date").val());
      //         form_data.append("note", $("#note").val());
      //         // form_data.append("sequence", $("#sequence").val());

      //         if (!$("#walletamount").val()) {
      //             alert('Please enter wallet amount.');
      //             return;
      //         }

      //         if (!$("#payment_type").val()) {
      //             alert('Please enter payment type.');
      //             return;
      //         }

      //         if (!$("#account_id").val()) {
      //             alert('Please enter Account.');
      //             return;
      //         }


      //         $.ajax({
      //             url: '{{ URL::to('/admin/add-vendor-wallet-balance') }}',
      //             method: 'POST',
      //             data:form_data,
      //             contentType: false,
      //             processData: false,
      //             // dataType: 'json',
      //             success: function (response) {
      //               if (response.status == 303) {
      //                   $(".permsg").html(response.message);
      //               }else if(response.status == 300){

      //                 $(".permsg").html(response.message);
      //                 window.setTimeout(function(){location.reload()},2000)
      //               }
                    

      //             },
      //             error: function (xhr) {
      //                 console.log(xhr.responseText);
      //             }
      //         });
      //     });
      // });

      $("#contentContainer").on('click', '.add-money-btn', function () {
          var id = $(this).data('id');
          $('#addWalletModal').modal('show');

          $('#vsequence').html('<option value="">Select Sequence Number</option>');

          var vendorSequences = vendorSeqNums.filter(function (seq) {
              return seq.vendor_id == id;
          });


          // Populate the dropdown
          if (vendorSequences.length > 0) {
              $.each(vendorSequences, function (index, seq) {
                  $('#vsequence').append(
                      $('<option>', {
                          value: seq.id,
                          text: seq.unique_id 
                      })
                  );
              });
          } else {
              $('#vsequence').append(
                  $('<option>', {
                      value: '',
                      text: 'No sequence numbers available'
                  })
              );
          }

          $('#addWalletForm').off('submit').on('submit', function (event) {
              event.preventDefault();

              var form_data = new FormData();
              form_data.append("vendorId", id);
              form_data.append("walletamount", $("#walletamount").val());
              form_data.append("payment_type", $("#payment_type").val());
              form_data.append("account_id", $("#account_id").val());
              form_data.append("wallet_date", $("#wallet_date").val());
              form_data.append("vsequence", $("#vsequence").val());
              form_data.append("note", $("#note").val());

              const fields = [
                  { id: 'walletamount', message: 'Please enter wallet amount.' },
                  { id: 'payment_type', message: 'Please enter payment type.' },
                  { id: 'account_id', message: 'Please enter Account.' },
                  { id: 'vsequence', message: 'Please select a sequence number.' }
              ];


              $.ajax({
                  url: '{{ URL::to("/admin/add-vendor-wallet-balance") }}',
                  method: 'POST',
                  data: form_data,
                  contentType: false,
                  processData: false,
                  success: function (response) {
                      if (response.status == 303) {
                          $(".permsg").html(response.message);
                      } else if (response.status == 300) {
                          $(".permsg").html(response.message);
                          window.setTimeout(function () {
                              location.reload();
                          }, 2000);
                      }
                  },
                  error: function (xhr) {
                      console.log(xhr.responseText);
                  }
              });
          });
      });



  });
</script>
@endsection