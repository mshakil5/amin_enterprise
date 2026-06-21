@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <a href="{{route('admin.allProgram')}}" class="btn btn-secondary my-3">
                    <i class="fas fa-arrow-left"></i> Program List
                </a>
            </div>
        </div>
        
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-gas-pump"></i> Challan qty wise vendor rate Update
                        </h3>
                    </div>
                    
                    <div class="card-body">
                        <div class="ermsg"></div>
                        
                        <!-- Professional Search Form -->
                        <div class="card card-light shadow-sm mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="clientId"><strong>Client</strong></label>
                                            <select id="clientId" class="form-control">
                                                <option value="">-- Select Client --</option>
                                                @foreach($clients as $client)
                                                    <option value="{{ $client->id }}" {{ (isset($filters['client_id']) && $filters['client_id'] == $client->id) ? 'selected' : '' }}>
                                                        {{ $client->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="startDate"><strong>From Date</strong></label>
                                            <input type="date" class="form-control" id="startDate" value="{{ $filters['fromdate'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="endDate"><strong>To Date</strong></label>
                                            <input type="date" class="form-control" id="endDate" value="{{ $filters['todate'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary btn-block" id="searchBtn">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date Range Results -->
                        <div id="dateRangeSummary" class="mb-3" style="display:none;">
                            <div class="alert alert-info">
                                <strong>Showing results from: </strong>
                                <span id="rangeStart"></span> to <span id="rangeEnd"></span>
                                <button type="button" class="close" id="clearRangeBtn">&times;</button>
                            </div>
                        </div>

                        <!-- Results Table -->
                        <div id="resultContainer">
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-search fa-3x mb-3"></i>
                                <p>Select criteria and click search to find fuel transactions.</p>
                            </div>
                        </div>
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

    let challanTable = null;

    // Auto-trigger search if session filters exist
    let hasFilters = '{{ !empty($filters) ? "1" : "0" }}';
    if (hasFilters === '1') {
        $('#searchBtn').click();
    }

    // Search Button Click
    $('#searchBtn').click(function() {
        let fromdate = $('#startDate').val();
        let todate   = $('#endDate').val();
        let clientId = $('#clientId').val();

        if (!fromdate) {
            showMessage('Please select from date.', 400);
            return;
        }
        if (!todate) {
            showMessage('Please select to date.', 400);
            return;
        }

        $.ajax({
            url: '{{ route("admin.afterChallanSearchByDateResult") }}',
            method: 'POST',
            data: {
                fromdate: fromdate,
                todate: todate,
                client_id: clientId,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#resultContainer').html(
                    '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading...</p></div>'
                );
            },
            success: function(response) {
                
                // Kept console.log as requested
                console.log(response);

                if (response.status == 400) {
                    showMessage(response.message, 400);
                    $('#resultContainer').html(
                        '<div class="text-center text-muted py-5"><i class="fas fa-inbox fa-3x mb-3"></i><p>No data found for this date.</p></div>'
                    );
                    $('#dateRangeSummary').hide();
                } else {
                    $('#resultContainer').html(response.html);
                    showMessage('Found ' + response.count + ' records.', 200);

                    $('#rangeStart').text(fromdate);
                    $('#rangeEnd').text(todate);
                    $('#dateRangeSummary').show();

                    if (challanTable) {
                        challanTable.destroy();
                    }

                    challanTable = $('#challanRateTable').DataTable({
                        responsive: true,
                        lengthChange: true,
                        pageLength: 10,
                        autoWidth: false,
                        order: [[0, 'asc']],
                        dom: '<"row mb-2"<"col-md-6"B><"col-md-6 text-right"f>>rtip',
                        buttons: [
                            { extend: 'excelHtml5', className: 'btn btn-sm btn-success', text: '<i class="fas fa-file-excel"></i> Excel' },
                            { extend: 'csvHtml5', className: 'btn btn-sm btn-info', text: '<i class="fas fa-file-csv"></i> CSV' },
                            { extend: 'pdfHtml5', className: 'btn btn-sm btn-danger', text: '<i class="fas fa-file-pdf"></i> PDF' },
                            { extend: 'print', className: 'btn btn-sm btn-secondary', text: '<i class="fas fa-print"></i> Print' }
                        ]
                    });

                    bindTableEvents();
                }
            },
            error: function(xhr) {
                showMessage('Error occurred while fetching data.', 400);
                $('#resultContainer').html(
                    '<div class="text-center text-danger py-5"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>Error occurred.</p></div>'
                );
            }
        });
    });

    function bindTableEvents() {
        $('#selectAllTop').off('click').on('click', function() {
            let checked = this.checked;
            $('#challanRateTable tbody tr').each(function() {
                $(this).toggleClass('table-active', checked);
                $(this).data('selected', checked);
            });
        });

        $('#challanRateTable tbody tr').off('click').on('click', function(e) {
            if($(e.target).is('button, a, input')) return;
            
            let isSelected = $(this).data('selected') || false;
            $(this).data('selected', !isSelected);
            $(this).toggleClass('table-active', !isSelected);
        });

        $('#previewChangesBtn').off('click').on('click', function() {
            let selected = getSelectedIds();
            if (selected.length === 0) {
                showMessage('Please select at least one record by clicking a row.', 400);
                return;
            }
            showMessage('Previewing ' + selected.length + ' records.', 200);
            console.log('Preview IDs:', selected);
        });

        $('#confirmBulkUpdate').off('click').on('click', function() {
            let selected = getSelectedIds();
            if (selected.length === 0) {
                showMessage('Please select at least one record by clicking a row.', 400);
                return;
            }
            
            showMessage('Submitting ' + selected.length + ' records for update.', 200);
            console.log('Submit IDs:', selected);
        });
    }

    function getSelectedIds() {
        let selected = [];
        $('#challanRateTable tbody tr').each(function() {
            if ($(this).data('selected')) {
                selected.push($(this).data('id'));
            }
        });
        return selected;
    }

    $('#clearRangeBtn').click(function() {
        $('#startDate').val('');
        $('#endDate').val('');
        $('#clientId').val('');
        $('#dateRangeSummary').hide();
        $('#resultContainer').html(
            '<div class="text-center text-muted py-5"><i class="fas fa-search fa-3x mb-3"></i><p>Select criteria and click search to find fuel transactions.</p></div>'
        );
    });

    function showMessage(message, status) {
        let type = (status == 200 || status == 300) ? 'success' : (status == 400 ? 'warning' : 'danger');
        let alertClass = 'alert-' + type;

        message = message.replace(/<div class="alert alert-\w+">/g, '').replace(/<\/div>/g, '');

        $('.ermsg').html(
            '<div class="alert ' + alertClass + ' alert-dismissible fade show">' +
            message +
            '<button type="button" class="close" data-dismiss="alert">&times;</button></div>'
        );

        setTimeout(function() {
            $('.ermsg').html('');
        }, 5000);
    }

});
</script>
@endsection