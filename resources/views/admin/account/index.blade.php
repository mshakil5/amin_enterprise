@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
@if ($data->count() < 2)
<section class="content" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
        </div>
      </div>
    </div>
</section>
@endif
<!-- /.content -->

<!-- Main content -->
<section class="content pt-3" id="addThisFormContainer">
  <div class="container-fluid">
    <div class="row justify-content-md-center">
      <!-- right column -->
      <div class="col-md-6">
        <!-- general form elements disabled -->
        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title">Create New Account</h3>
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
                    <label>Type</label>
                    <input type="text" class="form-control" id="type" name="type" value="">
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-group">
                    <label>Amount</label>
                    <input type="number" class="form-control" id="amount" name="amount">
                  </div>
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
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row mt-5">
                  <div class="col-12 text-center">
                    <h3>Accounts</h3>
                  </div>
                </div>

              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Balance</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)

                  @php
                      $id = $data->id;

                      $increase = \App\Models\Transaction::where('account_id', $id)
                          ->where(function ($q) {
                              $q->where(function ($q) {
                                  $q->where('table_type', 'Income')->where('tran_type', 'Current');
                              })->orWhere(function ($q) {
                                  $q->where('table_type', 'Liabilities')->where('tran_type', 'Received');
                              })->orWhere(function ($q) {
                                  $q->where('table_type', 'Equity')->where('tran_type', 'Received');
                              })->orWhere(function ($q) {
                                  $q->whereNull('table_type')->where('tran_type', 'Transfer')->where('description', 'like', 'Transfer from%');
                              });
                          })
                          ->sum('amount');

                      $decrease = \App\Models\Transaction::where('account_id', $id)
                          ->where(function ($q) {
                              $q->where(function ($q) {
                                  $q->where('table_type', 'Income')->where('tran_type', 'Refund');
                              })->orWhere(function ($q) {
                                  $q->whereIn('table_type', ['Expenses', 'Cogs'])->where('tran_type', 'Current');
                              })->orWhere(function ($q) {
                                  $q->where('table_type', 'Liabilities')->where('tran_type', 'Payment');
                              })->orWhere(function ($q) {
                                  $q->where('table_type', 'Equity')->where('tran_type', 'Payment');
                              })->orWhere(function ($q) {
                                  $q->whereNull('table_type')->where('tran_type', 'Transfer')->where('description', 'like', 'Transfer to%');
                              })->orWhere(function ($q) {
                                  $q->where('table_type', 'Asset')->where('tran_type', 'Petty Cash In');
                              })->orWhere(function ($q) {
                                  $q->where('table_type', 'Expense')->where('tran_type', 'Wallet');
                              });
                          })
                          ->sum('amount');

                      $balance = $increase - $decrease;
                  @endphp

                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{$data->type}}</td>
                    <td>{{$data->amount}}</td>
                    <td>{{ number_format($balance, 2) }}</td>
                    <td>
                      <a id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit mr-2" style="color: #2196f3;font-size:20px;"></i></a>
                      @if($data->amount > 0)
                      <button class="btn btn-sm btn-info transferBtn" data-id="{{$data->id}}" data-type="{{$data->type}}" data-amount="{{$data->amount}}">Transfer</button>
                      @endif
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

<!-- Add this modal at the bottom of your view file -->
<div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Transfer Funds</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="ermsg"></div>
                <form id="transferForm">
                    @csrf
                    <input type="hidden" id="fromAccountId" name="from_account_id">
                    <div class="form-group">
                        <label>From Account</label>
                        <input type="text" class="form-control" id="fromAccountType" readonly>
                    </div>
                    <div class="form-group">
                        <label>Current Balance</label>
                        <input type="text" class="form-control" id="currentBalance" readonly>
                    </div>
                    <div class="form-group">
                        <label>Transfer Amount</label>
                        <input type="number" class="form-control" id="transferAmount" name="amount" min="0" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>To Account</label>
                        <select class="form-control" id="toAccountId" name="to_account_id">
                            <option value="">Select Account</option>
                            @foreach($allAccounts as $account)
                                <option value="{{$account->id ?? ''}}">{{$account->type ?? ''}}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmTransfer">Transfer</button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')

<script>
  $(document).ready(function() {
      $('.transferBtn').click(function() {
          var accountId = $(this).data('id');
          var accountType = $(this).data('type');
          var accountAmount = $(this).data('amount');
          
          $('#fromAccountId').val(accountId);
          $('#fromAccountType').val(accountType);
          $('#currentBalance').val(accountAmount);
          $('#transferAmount').attr('max', accountAmount);
          $('#transferAmount').val('');
          $('#toAccountId').val('');
          
          $('#toAccountId option').show();
          $('#toAccountId option[value="' + accountId + '"]').hide();
          
          $('#transferModal').modal('show');
      });

      $('#confirmTransfer').click(function() {
          var formData = $('#transferForm').serialize();
          var fromAmount = parseFloat($('#currentBalance').val());
          var transferAmount = parseFloat($('#transferAmount').val());
          
          if (transferAmount > fromAmount) {
              $('.ermsg').html('<div class="alert alert-danger">Transfer amount cannot exceed current balance!</div>');
              return;
          }
          
          if (!transferAmount || transferAmount <= 0) {
              $('.ermsg').html('<div class="alert alert-danger">Please enter a valid amount!</div>');
              return;
          }
          
          if (!$('#toAccountId').val()) {
              $('.ermsg').html('<div class="alert alert-danger">Please select a destination account!</div>');
              return;
          }
          
          $.ajax({
              url: "{{ route('admin.account.transfer') }}",
              method: "POST",
              data: formData,
              success: function(response) {
                  if (response.success) {
                      $('#transferModal').modal('hide');
                      $('.ermsg').html('<div class="alert alert-success">' + response.message + '</div>');
                      location.reload();
                  } else {
                      $('.ermsg').html('<div class="alert alert-danger">' + response.message + '</div>');
                  }
              },
              error: function(xhr) {
                  $('.ermsg').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
              }
          });
      });
  });
</script>

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

<script>
  $(document).ready(function () {
      $("#addThisFormContainer").hide();
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
      //header for csrf-token is must in laravel
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      //
      var url = "{{URL::to('/admin/account')}}";
      var upurl = "{{URL::to('/admin/account-update')}}";
      // console.log(url);
      $("#addBtn").click(function(){
      //   alert("#addBtn");
          if($(this).val() == 'Create') {
              var form_data = new FormData();
              form_data.append("amount", $("#amount").val());
              form_data.append("type", $("#type").val());
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
                          pagetop();
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
              form_data.append("amount", $("#amount").val());
              form_data.append("type", $("#type").val());
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
                          pagetop();
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
          $("#type").val(data.type);
          $("#amount").val(data.amount);
          $("#codeid").val(data.id);
          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);
      }
      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create');
      }
  });
</script>
@endsection