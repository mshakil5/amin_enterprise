@extends('admin.layouts.admin')

@section('content')
<style>
    /* Professional Select2 & UI Tweaks */
    .select2-container--default .select2-selection--single { height: 38px; border-color: #ced4da; }
    .table-hover tbody tr:hover { background-color: rgba(0,0,0,.03); transition: 0.3s; }
    .card-title { font-weight: 600; font-size: 1.1rem; }
    .badge-debit { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .badge-credit { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    /* Force Select2 to take full width of its parent container */
.select2-container {
    width: 100% !important;
}

/* Fix for Select2 inside Bootstrap Modals */
.select2-container--open {
    z-index: 9999 !important;
}

</style>


<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Vendor Ledger</h1>
            </div>
            <div class="col-sm-6 text-right">
                <span class="badge badge-success p-2 mr-2" style="font-size: 1rem;">
                    Current Balance: {{ number_format($balance, 2) }}
                </span>
                <span class="badge badge-info p-2">Vendor: {{ $vendor->name }}</span>
            </div>
        </div>
    </div>
</div>

<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header bg-white">
                <h3 class="card-title">
                    <i class="fas fa-exchange-alt mr-1"></i> Transaction History
                </h3>
            </div>
            
            <div class="card-body">
                <div id="alert-container"></div>

                <div class="table-responsive">
                    <table id="daybookTable" class="table table-hover table-valign-middle">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Account</th>
                                <th>Voucher</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                                <th class="text-right">Balance</th> <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                // Start with the total balance passed from controller
                                $runningBalance = $balance; 
                            @endphp
                            
                            @foreach($transactions as $key => $data)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="text-nowrap font-weight-bold text-muted">
                                    {{ \Carbon\Carbon::parse($data->date)->format('d M, Y') }}
                                </td>
                                <td>
                                    <span class="d-block">{{ $data->description }}</span>
                                    <small class="text-primary">{{ $data->vendorSequenceNumber->unique_id ?? "" }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-light border">{{ $data->account->type ?? 'N/A' }}</span>
                                    <span class="badge badge-light border">{{ $data->tran_id ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.expense.voucher', $data->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                </td>
                                
                                <td class="text-right font-weight-bold text-danger">
                                    @if(in_array($data->table_type, ['Expenses', 'Expense']))
                                        {{ number_format($data->amount, 2) }}
                                    @endif
                                </td>
                                
                                <td class="text-right font-weight-bold text-success">
                                    @if(in_array($data->table_type, ['Income']))
                                        {{ number_format($data->amount, 2) }}
                                    @endif
                                </td>

                                <td class="text-right font-weight-bold">
                                    {{ number_format($runningBalance, 2) }}
                                    @php
                                        // Since list is DESC, we "undo" the current transaction for the NEXT row
                                        if(in_array($data->table_type, ['Income'])) {
                                            $runningBalance -= $data->amount;
                                        } else {
                                            $runningBalance += $data->amount;
                                        }
                                    @endphp
                                </td>

                                <td class="text-center">
                                    <div class="btn-group">
                                        <button class="btn btn-xs btn-info detailsBtn" 
                                            data-date="{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}"
                                            data-description="{{ $data->description }}"
                                            data-type="{{ $data->tran_type }} {{ $data->payment_type }}"
                                            data-voucher="{{ route('admin.expense.voucher', $data->id) }}"
                                            data-challan="{{ $data->challan_no }}"
                                            data-amount="{{ $data->amount }}"
                                            data-note="{{ $data->note }}"
                                            data-account_id="{{ $data->account->type ?? '' }}"
                                            title="View Details">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <button class="btn btn-xs btn-primary editBtn" 
                                            tranid="{{$data->id}}" 
                                            data-date="{{ \Carbon\Carbon::parse($data->date)->format('Y-m-d') }}" 
                                            data-amount="{{ $data->amount }}" 
                                            data-payment_type="{{ $data->payment_type }}" 
                                            data-account_id="{{ $data->account_id }}" 
                                            data-note="{{ $data->note }}" 
                                            data-vsid="{{ $data->vendor_sequence_number_id }}" 
                                            title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>


                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal for add money -->
<div class="modal fade" id="addWalletModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document"> <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i> Update Transaction</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="addWalletForm">
                <div class="modal-body">
                    <div class="permsg"></div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="wallet_date" name="wallet_date" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="number" class="form-control" id="walletamount" name="walletamount">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Payment Type</label>
                            <select name="payment_type" id="payment_type" class="form-control">
                                <option value="Cash">Cash</option>
                                <option value="Bank">Bank</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Account</label>
                            <select name="account_id" id="account_id" class="form-control">
                                @foreach (\App\Models\Account::latest()->get() as $item)
                                    <option value="{{$item->id}}">{{$item->type}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Vendor Sequence</label>
                        <select name="vsequence" id="vsequence" class="form-control select2">
                            @foreach ($vendorSeqNums as $vitem)
                                <option value="{{$vitem->id}}">{{$vitem->unique_id}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Note / Remarks</label>
                        <textarea class="form-control" id="note" rows="2" placeholder="Optional details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning shadow-sm px-4">Save Changes</button>
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
                    title: 'Vendor Transaction'
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
