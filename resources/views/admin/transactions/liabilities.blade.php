@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div id="alert-container"></div>
                <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Liabilities</h3>
                    <div class="card-tools">
                        <button class="btn btn-lg btn-success" data-toggle="modal" data-target="#chartModal" data-purpose="0">+ Add New liability</button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <form class="form-inline" role="form" method="POST" action="{{ route('admin.liability.filter') }}">
                            {{ csrf_field() }}
                            
                            <div class="form-group mx-sm-3">
                                <label class="sr-only">Start Date</label>
                                <input type="date" class="form-control" name="start_date" value="{{ request()->input('start_date') }}">
                            </div>
                            
                            <div class="form-group mx-sm-3">
                                <label class="sr-only">End Date</label>
                                <input type="date" class="form-control" name="end_date" value="{{ request()->input('end_date') }}">
                            </div>
                            
                            <div class="form-group mx-sm-3">
                                <label class="sr-only">Account</label>
                                <select class="form-control select2" name="account_name">
                                    <option value="">Select Account..</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->account_name }}" {{ request()->input('account_name') == $account->account_name ? 'selected' : '' }}>
                                            {{ $account->account_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>


                @component('components.widget')
                    @slot('title')
                    Liabilities
                    @endslot
                    @slot('description')

                    @endslot
                    @slot('body')
                        @component('components.table')
                            @slot('tableID')
                                expenseTBL
                            @endslot
                            @slot('head')
                                <th>ID</th>
                                <th>Date</th>
                                <th>Account Head</th>
                                <th>Ref</th>
                                <th>Description</th>
                                <th>Transaction Type</th>
                                <th>Payment Type</th>
                                <th>Acc Name</th>
                                <th>Amount</th>
                                <th><i class=""></i> Action</th>
                            @endslot
                        @endcomponent
                    @endslot
                @endcomponent
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="chartModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h4 class="modal-title">Liabilitiy</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                
            </div>
            <form class="form-horizontal" id="customer-form">
            <div id="alert-container1"></div>
                <div class="modal-body">
                    {{csrf_field()}}
                    
                    <div class="row">
                    <div class="col-md-6">
                    <div class="form-group">
                        <label for="date" class="col-form-label">Date</label>
                        <input type="date" name="date" class="form-control " id="date" value="{{date('Y-m-d')}}">
                    </div>
                    </div>

                    <div class="col-md-6">
                    <div class="form-group">
                        <label for="chart_of_account_id" class="col-form-label">Chart of Account</label>
                        <select class="form-control" id="chart_of_account_id" name="chart_of_account_id">
                            <option value="">Select chart of account</option>
                            @php
                                use App\Models\ChartOfAccount;
                                $liabilities = ChartOfAccount::where('account_head', 'Liabilities')->get();
                            @endphp
                            @foreach($liabilities as $liabilitie)
                                <option value="{{ $liabilitie->id }}">{{ $liabilitie->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ref" class="col-form-label">Reference</label>
                                <input type="text" name="ref" class="form-control " id="ref">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="transaction_type" class="col-form-label">Transaction Type</label>
                                <select class="form-control" id="transaction_type" name="transaction_type">
                                    <option value="">Select transaction type</option>
                                    <option value="Received">Received</option>
                                    <option value="Payment">Payment</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="amount" class="col-form-label">Amount</label>
                                <input type="text" name="amount" class="form-control " id="amount">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group" id="payment_type_container">
                                <label for="payment_type" class="col-form-label">Payment Type</label>
                                <select class="form-control" id="payment_type" name="payment_type">
                                    <option value="">Select payment type</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank">Bank</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="account_id" class="col-form-label">Account Type</label>
                                <select class="form-control" id="account_id" name="account_id">
                                    <option value="">Select Account Type</option>
                                    @foreach ($accountList as $account)
                                        <option value="{{ $account->id }}">{{ $account->type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 d-none">
                            <div class="form-group">
                                <label for="tax_rate" class="col-form-label">Tax %</label>
                                <input type="text" name="tax_rate" class="form-control " id="tax_rate">
                            </div>
                        </div>
                    </div>

                    <div class="row d-none">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tax_amount" class="col-form-label">Tax Amount</label>
                                <input type="text" name="tax_amount" class="form-control " id="tax_amount">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="at_amount" class="col-form-label">Total Amount</label>
                                <input type="text" name="at_amount" class="form-control " id="at_amount">
                            </div>
                        </div>
                    </div>

                    

                    <div class="form-group">
                        <label for="description" class="col-form-label">Description</label>
                        <textarea class="form-control" id="description" rows="3" placeholder="Description" name="description"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-primary submit-btn save-btn"> Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
    
@section('script')

<!-- Amount and tax rate calculation -->
<script>
    function calculateTotal() {
        var amount = parseFloat(document.getElementById('amount').value) || 0;
        var taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;

        var taxAmount = amount * (taxRate / 100);
        document.getElementById('tax_amount').value = taxAmount.toFixed(2);

        var totalAmount = amount + taxAmount;
        document.getElementById('at_amount').value = totalAmount.toFixed(2);
    }

    document.getElementById('amount').addEventListener('input', calculateTotal);
    document.getElementById('tax_rate').addEventListener('input', calculateTotal);

    calculateTotal();
</script>

<!-- Main script -->
<script>

    $(document).ready(function() {
        $('.select2').select2();
    });

    var charturl = "{{URL::to('/admin/liabilities')}}";
    var customerTBL = $('#expenseTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
        url: charturl,
        type: 'GET',
        data: function (d) {
            d.start_date = $('input[name="start_date"]').val();
            d.end_date = $('input[name="end_date"]').val();
            d.account_name = $('select[name="account_name"]').val();
        },
        error: function (xhr, error, thrown) {
            console.log(xhr.responseText);
        }
        },
        deferRender: true,
        columns: [
            {
              data: 'tran_id',
              name: 'tran_id',
              orderable: false,
              searchable: false,
            },
            {
              data: 'date',
              name: 'date',
              render: function(data, type, row) {
                return data ? dayjs(data).format('DD-MM-YYYY') : '';
              }
            },
            {data: 'chart_of_account', name: 'chart_of_account'},
            {data: 'ref', name: 'ref'},
            {data: 'description', name: 'description'},
            {data: 'tran_type', name: 'tran_type'},
            {data: 'payment_type', name: 'payment_type'},
            {data: 'accountname', name: 'accountname'},
            {data: 'amount', name: 'amount'},
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                let button = `<button type="button" class="btn btn-warning btn-xs edit-btn" data-toggle="modal" data-target="#chartModal" value="${row.id}" title="Edit" data-purpose='1'><i class="fa fa-edit" aria-hidden="true"></i> Edit</button>`;

                let voucherUrl = "{{ route('admin.expense.voucher', ['id' => '__id__']) }}".replace('__id__', row.id);
                
                button += `<a href="${voucherUrl}" target="blank" class="btn btn-info btn-xs" title="Voucher"><i class="fa fa-info-circle" aria-hidden="true"></i> Voucher</a>`;

                if (row.amount < 0) {}
                
                return button;
            }

            },
        ]
    });

    $('form').on('submit', function(e) {
        e.preventDefault();
        customerTBL.ajax.reload();
    });

    // modal

    $('#chartModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        let purpose = button.data('purpose');
        var modal = $(this);
        if (purpose) {
            let id = button.val();
            $.ajax({
                url: charturl +'/' + id,
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    // console.log(response);
                    $('#date').val(response.date);
                    $('#ref').val(response.ref);
                    $('#transaction_type').val(response.tran_type);
                    $('#amount').val(response.amount);
                    $('#tax_rate').val(response.tax_rate);
                    $('#tax_amount').val(response.tax_amount);
                    $('#at_amount').val(response.at_amount);
                    $('#payment_type').val(response.payment_type);
                    $('#description').val(response.description);
                    $('#chart_of_account_id').val(response.chart_of_account_id);
                    $('#account_id').val(response.account_id);

                    $('#chartModal .submit-btn').removeClass('save-btn').addClass('update-btn').text('Update').val(response.id);
                }
            });
        } else {
            $('#customer-form').trigger('reset');
            $('#customer-form textarea').text('');
            $('#chartModal .submit-btn').removeClass('update-btn').addClass('save-btn').text('Save').val("");
        }
    });

    // save button event

    $(document).on('click', '.save-btn', function () {
        let formDataSerialized = $('#customer-form').serializeArray();
        formDataSerialized.push({ name: 'table_type', value: 'Expenses' });
        let formData = $.param(formDataSerialized);
        // console.log(formData);


        $.ajax({
            url: charturl,
            type: 'POST',
            data: formData,
            beforeSend: function (request) {
                request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                // console.log(response);
                if (response.status === 200) {
                    $('#chartModal').modal('toggle');
                    swal({
                        text: "Saved successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                    customerTBL.draw();
                } else if (response.status === 303) {
                    let alertMessage = `<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>${response.message}</b></div>`;
                    $('#alert-container1').html(alertMessage);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    // update button event

    $(document).on('click', '.update-btn', function () {
        let formData = $('#customer-form').serialize();
        let id = $(this).val();
        // console.log(id);
        $.ajax({
            url: charturl + '/' + id,
            type: 'PUT',
            data: formData,
            beforeSend: function (request) {
                request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function (response) {
                if (response.status === 200) {
                    $('#chartModal').modal('toggle');
                    swal({
                        text: "Updated successfully",
                        icon: "success",
                        button: {
                            text: "OK",
                            className: "swal-button--confirm"
                        }
                    });
                    customerTBL.draw();
                } else if (response.status === 303) {
                    let alertMessage = `<div class='alert alert-warning'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>${response.message}</b></div>`;
                    $('#alert-container1').html(alertMessage);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

</script>

<!-- Main script -->

<script>
    $(document).ready(function() {
        $('#chartModal').on('hidden.bs.modal', function (e) {
            $('#payment_type').val('');
            $('#payment_type_container').show();
        });
    });
</script>

@endsection