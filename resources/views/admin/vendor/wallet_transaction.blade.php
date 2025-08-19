@extends('admin.layouts.admin')

@section('content')

<style>
    
    .select2-container {
        width: 100% !important;
    }
</style>

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Vendor Transaction</h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <div class="row">
                            {{-- <div class="row  justify-content-md-center mb-3">
                                <form class="form-inline" role="form" method="POST" action="">
                                    {{ csrf_field() }}

                                    
                                    <div class="form-group col-md-2">
                                        <label for="vendor_id">Vendor</label>
                                        <select name="vendor_id" id="vendor_id" class="form-control select2">
                                          <option value="">Select</option>
                                          @foreach (\App\Models\Vendor::where('status', 1)->get() as $vendor)
                                          <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="mv_id">Mother Vassel </label>
                                        <select name="mv_id" id="mv_id" class="form-control select2">
                                          <option value="">Select</option>
                                          @foreach (\App\Models\MotherVassel::where('status', 1)->get() as $mvassel)
                                          <option value="{{$mvassel->id}}">{{$mvassel->name}}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group col-md-2">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" value="{{ request()->input('start_date') }}">
                                    </div>
                                    
                                    <div class="form-group col-md-2">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control" name="end_date" value="{{ request()->input('end_date') }}">
                                    </div>

                                    <div class="col-md-1">
                                        <label class="label label-primary" style="visibility:hidden;">Action</label>
                                        <button type="submit" class="btn btn-secondary btn-block">Search</button>
                                    </div>
                                </form>
                            </div> --}}
                            <div class="col-md-12">
                                <div class="text-center my-4 company-name-container">
                                    <h3>{{$vendor->name}}</h3>
                                    <h4>Transaction</h4>
                                </div>
                        
                                
                                <table id="daybookTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Sequence ID</th>
                                            <th>Type</th>
                                            <th>Voucher</th>                        
                                            <th>Challan#</th>                            
                                            <th>Debit</th>                            
                                            <th>Action</th>                            
                                            {{-- <th>Credit</th>                            
                                            <th>Balance</th>                             --}}
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @php
                                            $balance = $balance ?? 0;
                                        @endphp

                                        @foreach($transactions as $key => $data)
                                            <tr>
                                                <td> {{ $key + 1 }} </td>
                                                <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                                <td>
                                                    {{ $data->description }} 
                                                </td>
                                                <td>
                                                    {{$data->vendorSequenceNumber->unique_id ?? ""}}
                                                </td>
                                                <td>
                                                    {{ $data->tran_type }} {{ $data->payment_type }} 
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.expense.voucher', $data->id) }}" target="_blank" class="btn btn-info btn-xs" title="Voucher">
                                                        <i class="fa fa-info-circle" aria-hidden="true"></i> Voucher
                                                    </a>
                                                  </td>
                                                <td>{{ $data->challan_no }}</td>
                                                @if(in_array($data->tran_type, ['Wallet']))
                                                <td>{{ number_format($data->amount, 2) }}</td>
                                                {{-- <td></td>
                                                <td>{{ number_format($balance, 2) }}</td> --}}
                                                @php
                                                    $balance = $balance - $data->amount;
                                                @endphp
                                                @elseif(in_array($data->tran_type, ['Advance']))
                                                {{-- <td></td>
                                                <td>{{ number_format($data->amount, 2) }}</td>
                                                <td>{{ number_format($balance, 2) }}</td> --}}
                                                @php
                                                    $balance = $balance + $data->amount;
                                                @endphp

                                                @endif
                                                <td>
                                                    <a class="btn btn-info btn-xs detailsBtn" 
                                                        data-date="{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}"
                                                        data-description="{{ $data->description }}"
                                                        data-type="{{ $data->tran_type }} {{ $data->payment_type }}"
                                                        data-voucher="{{ route('admin.expense.voucher', $data->id) }}"
                                                        data-challan="{{ $data->challan_no }}"
                                                        data-amount="{{ $data->amount }}"
                                                        data-note="{{ $data->note }}"
                                                        data-account_id="{{ $data->account->type ?? '' }}"
                                                        data-toggle="modal" data-target="#detailsModal"
                                                        title="Details">
                                                         <i class="fa fa-eye"></i> Details
                                                    </a>
                                                    <a  class="btn btn-primary btn-xs editBtn"  tranid="{{$data->id}}" data-date="{{ \Carbon\Carbon::parse($data->date)->format('Y-m-d') }}" data-amount="{{ $data->amount }}" data-payment_type="{{ $data->payment_type }}" data-account_id="{{ $data->account_id }}" data-note="{{ $data->note }}" data-vsid="{{ $data->vendor_sequence_number_id }}" data-toggle="modal" data-target="#addWalletModal" title="Edit">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>
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
        </div>
    </div>
</section>


<!-- Modal for add money -->
<div class="modal fade" id="addWalletModal" tabindex="-1" role="dialog" aria-labelledby="addWalletLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="addWalletLabel">Update transaction</h5>
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
                      <label for="vsequence">Vendor Sequence <span style="color: red;">*</span></label>
                      <select name="vsequence" id="vsequence" class="form-control select2">
                        @foreach ($vendorSeqNums as $vitem)
                        <option value="{{$vitem->id}}">{{$vitem->unique_id}}</option>
                        @endforeach
                      </select>
                  </div>


                  <div class="form-group">
                      <label for="note">Note</label>
                      <textarea class="form-control" id="note" rows="3"></textarea>
                  </div>


              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-warning">Update</button>
              </div>
          </form>
      </div>
  </div>
</div>


@endsection

@section('script')

<script>
    
    $(document).ready(function() {
        $('#daybookTable').DataTable({
            pageLength: 100,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Download Excel',
                    title: 'Vendor_Transaction'
                }
            ]
        });

        
      //header for csrf-token is must in laravel
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      //


        $("#contentContainer").on('click', '.editBtn', function () {
          var id = $(this).data('id');
          $('#addWalletModal').modal('show');


        $('#wallet_date').val($(this).data('date'));
        $('#walletamount').val($(this).data('amount'));
        $('#payment_type').val($(this).data('payment_type'));
        $('#account_id').val($(this).data('account_id'));
        $('#vsequence').val($(this).data('vsid'));
        $('#note').val($(this).data('note'));
        id = $(this).attr('tranid');


          $('#addWalletForm').off('submit').on('submit', function (event) {
              event.preventDefault();

              var form_data = new FormData();
              form_data.append("walletamount", $("#walletamount").val());
              form_data.append("payment_type", $("#payment_type").val());
              form_data.append("account_id", $("#account_id").val());
              form_data.append("wallet_date", $("#wallet_date").val());
              form_data.append("note", $("#note").val());
              form_data.append("vsequence", $("#vsequence").val());
              form_data.append("tranid", id);

              if (!$("#walletamount").val()) {
                  alert('Please enter wallet amount.');
                  return;
              }

              if (!$("#payment_type").val()) {
                  alert('Please enter payment type.');
                  return;
              }

              if (!$("#account_id").val()) {
                  alert('Please enter Account.');
                  return;
              }


              $.ajax({
                  url: '{{ URL::to('/admin/update-vendor-wallet-balance') }}',
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
                    

                  },
                  error: function (xhr) {
                      console.log(xhr.responseText);
                  }
              });
          });
      });


    $("#contentContainer").on('click', '.detailsBtn', function () {
        var date = $(this).data('date');
        var description = $(this).data('description');
        var type = $(this).data('type');
        var voucher = $(this).data('voucher');
        var challan = $(this).data('challan');
        var amount = $(this).data('amount');
        var note = $(this).data('note');
        var accountname = $(this).data('account_id');

        var modalHtml = `
            <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailsLabel">Transaction Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Date:</strong> ${date}</p>
                            <p><strong>Description:</strong> ${description}</p>
                            <p><strong>Type:</strong> ${type}</p>
                            <p><strong>Voucher:</strong> <a href="${voucher}" target="_blank">View Voucher</a></p>
                            <p><strong>Challan#:</strong> ${challan}</p>
                            <p><strong>Amount:</strong> ${amount}</p>
                            <p><strong>Note:</strong> ${note ? note : ''}</p>
                            <p><strong>Account:</strong> ${accountname ? accountname : ''}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove any existing modal to avoid duplicates
        $('#detailsModal').remove();
        $('body').append(modalHtml);
        $('#detailsModal').modal('show');
    });





    });
</script>

@endsection
