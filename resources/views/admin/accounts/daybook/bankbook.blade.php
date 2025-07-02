@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Bank Book</h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <div class="row justify-content-md-center mb-3">
                            <form id="filterForm" class="form-inline" role="form">
                                <div class="form-group col-md-2">
                                    <label for="vendor_id">Vendor</label>
                                    <select name="vendor_id" id="vendor_id" class="form-control select2">
                                        <option value="">Select</option>
                                        @foreach (\App\Models\Vendor::where('status', 1)->get() as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="mv_id">Mother Vassel</label>
                                    <select name="mv_id" id="mv_id" class="form-control select2">
                                        <option value="">Select</option>
                                        @foreach (\App\Models\MotherVassel::where('status', 1)->get() as $mvassel)
                                            <option value="{{ $mvassel->id }}">{{ $mvassel->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" class="form-control" name="start_date" id="start_date">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="end_date">End Date</label>
                                    <input type="date" class="form-control" name="end_date" id="end_date">
                                </div>
                                <div class="col-md-1">
                                    <label class="d-block" style="visibility:hidden;">Search</label>
                                    <button type="submit" class="btn btn-secondary btn-block">Search</button>
                                </div>
                            </form>
                        </div>

                        <div class="text-center my-4">
                            <h4>Day Bank Book</h4>
                        </div>

                        <table id="daybookTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Voucher</th>
                                    <th>Bill#</th>
                                    <th>Challan#</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
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
    $(document).ready(function() {
        $('.select2').select2();

        var table = $('#daybookTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.bankbook') }}",
                data: function(d) {
                    d.vendor_id = $('#vendor_id').val();
                    d.mv_id = $('#mv_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                },
                complete: function() {
                    $('#tableLoader').remove();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'date', name: 'date' },
                { data: 'description', name: 'description' },
                { data: 'type_label', name: 'tran_type' },
                { data: 'voucher', name: 'voucher', orderable: false, searchable: false },
                { data: 'bill_number', name: 'bill_number' },
                { data: 'challan_no', name: 'challan_no' },
                { data: 'debit', name: 'debit', orderable: false, searchable: false },
                { data: 'credit', name: 'credit', orderable: false, searchable: false },
                { data: 'balance', name: 'balance', orderable: false, searchable: false },
            ],
            pageLength: 100,
        });

        $('#filterForm').on('submit', function(e) {
            e.preventDefault();
            table.ajax.reload();
        });
    });
</script>

@endsection