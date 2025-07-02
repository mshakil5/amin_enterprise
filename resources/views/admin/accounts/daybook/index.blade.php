@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Day Book</h3>
                    </div>
                    <div class="card-body">
                        <form id="filterForm" class="form-inline mb-3">
                            <div class="form-group mr-2">
                                <label for="start_date" class="mr-1">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="form-group mr-2">
                                <label for="end_date" class="mr-1">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <button type="submit" class="btn btn-secondary">Search</button>
                        </form>

                        <table id="daybookTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Voucher</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="6" class="text-right">Total Income:</td>
                                    <td id="totalIncome"></td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td colspan="6" class="text-right">Total Expense:</td>
                                    <td id="totalExpenses"></td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td colspan="6" class="text-right">Assets:</td>
                                    <td id="totalAssets"></td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td colspan="6" class="text-right">Liabilities:</td>
                                    <td id="totalLiabilities"></td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td colspan="6" class="text-right">Equity:</td>
                                    <td id="totalEquity"></td>
                                </tr>
                            </tfoot>
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
    $(document).ready(function () {
        let table = $('#daybookTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.daybook') }}",
                data: function (d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'date', name: 'date' },
                { data: 'description', name: 'description' },
                { data: 'type_label', name: 'type_label' },
                { data: 'voucher', name: 'voucher', orderable: false, searchable: false },
                { data: 'debit', name: 'debit', orderable: false, searchable: false },
                { data: 'credit', name: 'credit', orderable: false, searchable: false },
            ],
            pageLength: 100,
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();
                
                // Update totals in footer
                if (api.ajax.json()) {
                    $('#totalIncome').html(api.ajax.json().categoryTotals.Income.toFixed(2));
                    $('#totalExpenses').html(api.ajax.json().categoryTotals.Expenses.toFixed(2));
                    $('#totalAssets').html(api.ajax.json().categoryTotals.Assets.toFixed(2));
                    $('#totalLiabilities').html(api.ajax.json().categoryTotals.Liabilities.toFixed(2));
                    $('#totalEquity').html(api.ajax.json().categoryTotals.Equity.toFixed(2));
                }
            }
        });

        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            table.ajax.reload();
        });
    });
</script>
@endsection