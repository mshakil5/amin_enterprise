@extends('admin.layouts.admin')

@section('content')
<style>
    .fuel-rate-input {
        width: 90px !important;
    }
    #fuelRateTable th, #fuelRateTable td {
        font-size: 12px;
        vertical-align: middle;
        padding: 5px 8px;
    }
    .updated-row {
        background-color: #d4edda !important;
    }
</style>

<section class="content pt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <a href="{{route('admin.allProgram')}}" class="btn btn-secondary my-3">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-gas-pump"></i> Fuel Rate Update
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-info" id="resultCount" style="display:none;"></span>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="ermsg"></div>
                        
                        <!-- Search Form -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <input type="date" class="form-control" id="searchDate" value="{{date('Y-m-d')}}">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" id="searchBtn">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-8 text-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-secondary date-range-btn" data-days="1">Today</button>
                                    <button type="button" class="btn btn-outline-secondary date-range-btn" data-days="7">Last 7 Days</button>
                                    <button type="button" class="btn btn-outline-secondary date-range-btn" data-days="30">Last 30 Days</button>
                                    <button type="button" class="btn btn-outline-secondary date-range-btn" data-custom="true">Custom Range</button>
                                </div>
                                
                                <div id="customRangeDiv" class="d-none mt-2">
                                    <div class="input-group">
                                        <input type="date" class="form-control" id="startDate">
                                        <span class="input-group-text">To</span>
                                        <input type="date" class="form-control" id="endDate">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-primary" id="rangeSearchBtn">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
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
                                <p>Select a date and click search to find fuel transactions.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer" id="bulkActionFooter" style="display:none;">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <span class="text-muted" id="selectedCount">0 records selected</span>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-warning btn-sm" id="previewChangesBtn">
                                    <i class="fas fa-eye"></i> Preview Changes
                                </button>
                                <button type="button" class="btn btn-success btn-sm" id="confirmBulkUpdate" disabled>
                                    <i class="fas fa-check"></i> Confirm Update
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Preview Changes
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="previewContent">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmFromModal">
                    <i class="fas fa-check"></i> Confirm Update
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Single Update Confirmation Modal -->
<div class="modal fade" id="singleUpdateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">Confirm Single Update</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="singleUpdateContent">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmSingleFromModal">
                    <i class="fas fa-check"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
 $(document).ready(function() {
    let searchDateForBulk = '';
    let pendingUpdateIds = [];
    let pendingNewRate = 0;

    // Date range buttons
    $('.date-range-btn').click(function() {
        let days = $(this).data('days');
        let isCustom = $(this).data('custom');
        
        if (isCustom) {
            $('#customRangeDiv').toggleClass('d-none');
            return;
        }
        
        $('#customRangeDiv').addClass('d-none');
        let endDate = new Date();
        let startDate = new Date();
        startDate.setDate(startDate.getDate() - days);
        
        $('#startDate').val(formatDate(startDate));
        $('#endDate').val(formatDate(endDate));
        
        searchByRange();
    });

    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    // Single date search
    $('#searchBtn').click(function() {
        let date = $('#searchDate').val();
        if (!date) {
            showMessage('<div class="alert alert-warning">Please select a date.</div>', 400);
            return;
        }
        
        searchDateForBulk = date;
        $('#dateRangeSummary').hide();
        
        $.ajax({
            url: '{{ route("admin.fuelRateUpdate.search") }}',
            method: 'POST',
            data: {
                search_date: date,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status == 400) {
                    showMessage(response.message, 400);
                    $('#resultContainer').html('<div class="text-center text-muted py-5"><i class="fas fa-inbox fa-3x mb-3"></i><p>No data found for this date.</p></div>');
                    $('#bulkActionFooter').hide();
                    $('#resultCount').hide();
                } else {
                    $('#resultContainer').html(response.html);
                    $('#resultCount').text(response.count + ' records found').show();
                    $('#bulkActionFooter').show();
                    showMessage('<div class="alert alert-success">Found ' + response.count + ' records.</div>', 200);
                    initTableEvents();
                }
            },
            error: function(xhr) {
                showMessage('<div class="alert alert-danger">Error occurred.</div>', 400);
            }
        });
    });

    // Range search
    $('#rangeSearchBtn').click(searchByRange);
    
    function searchByRange() {
        let startDate = $('#startDate').val();
        let endDate = $('#endDate').val();
        
        if (!startDate || !endDate) {
            showMessage('<div class="alert alert-warning">Please select start and end date.</div>', 400);
            return;
        }

        searchMultipleDates(startDate, endDate);
    }

    function searchMultipleDates(startDate, endDate) {
        $('#resultContainer').html('<div class="text-center py-5"><div class="spinner-border"></div><p>Searching...</p></div>');
        
        let dates = [];
        let current = new Date(startDate);
        let end = new Date(endDate);
        
        while (current <= end) {
            dates.push(formatDate(current));
            current.setDate(current.getDate() + 1);
        }
        
        let allResults = [];
        let completed = 0;
        
        dates.forEach(function(date) {
            $.ajax({
                url: '{{ route("admin.fuelRateUpdate.search") }}',
                method: 'POST',
                data: { search_date: date, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    completed++;
                    if (response.status == 200) {
                        allResults.push({ date: date, html: response.html, count: response.count });
                    }
                    
                    if (completed == dates.length) {
                        displayRangeResults(allResults, startDate, endDate);
                    }
                },
                error: function() {
                    completed++;
                    if (completed == dates.length) {
                        displayRangeResults(allResults, startDate, endDate);
                    }
                }
            });
        });
    }

    function displayRangeResults(results, startDate, endDate) {
        let totalCount = results.reduce((sum, r) => sum + r.count, 0);
        
        if (totalCount == 0) {
            $('#resultContainer').html('<div class="text-center text-muted py-5"><i class="fas fa-inbox fa-3x mb-3"></i><p>No data found in this range.</p></div>');
            $('#bulkActionFooter').hide();
            $('#resultCount').hide();
            return;
        }
        
        let html = '';
        results.forEach(function(r) {
            if (r.count > 0) {
                html += '<h6 class="mt-3 mb-2"><i class="fas fa-calendar-day"></i> ' + r.date + '</h6>';
                html += r.html;
            }
        });
        
        $('#resultContainer').html(html);
        $('#dateRangeSummary').show();
        $('#rangeStart').text(startDate);
        $('#rangeEnd').text(endDate);
        $('#resultCount').text(totalCount + ' records found').show();
        $('#bulkActionFooter').show();
        
        initTableEvents();
        showMessage('<div class="alert alert-success">Found ' + totalCount + ' records.</div>', 200);
    }

    $('#clearRangeBtn').click(function() {
        $('#dateRangeSummary').hide();
        $('#resultContainer').html('<div class="text-center text-muted py-5"><i class="fas fa-search fa-3x mb-3"></i><p>Select a date and click search.</p></div>');
        $('#bulkActionFooter').hide();
        $('#resultCount').hide();
    });

    // Initialize table events
    function initTableEvents() {
        // Select all checkbox
        $('#selectAll').off('change').on('change', function() {
            $('.advance-checkbox').prop('checked', this.checked);
            updateSelectedCount();
        });

        // Individual checkbox
        $('.advance-checkbox').off('change').on('change', function() {
            if ($('.advance-checkbox:checked').length == $('.advance-checkbox').length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
            updateSelectedCount();
        });

        // Single rate input change - calculate preview
        $('.single-rate-input').off('input').on('input', function() {
            let row = $(this).closest('tr');
            let newRate = parseFloat($(this).val()) || 0;
            let fuelqty = parseFloat($(this).data('fuelqty')) || 0;
            let cashamount = parseFloat($(this).data('cashamount')) || 0;
            
            let newFuelAmount = newRate * fuelqty;
            let newTotal = newFuelAmount + cashamount;
            
            row.find('.new-fuel-amount').text(newFuelAmount.toFixed(2));
            row.find('.new-total').text(newTotal.toFixed(2));
        });

        // Single update button
        $('.single-update-btn').off('click').on('click', function() {
            let id = $(this).data('id');
            let row = $(this).closest('tr');
            
            // Parse all values as float to ensure correct calculation
            let oldRate = parseFloat(row.find('.old-rate').text()) || 0;
            let newRate = parseFloat(row.find('.single-rate-input').val()) || 0;
            let fuelqty = parseFloat(row.find('.single-rate-input').data('fuelqty')) || 0;
            let cashamount = parseFloat(row.find('.single-rate-input').data('cashamount')) || 0;
            
            let oldFuelAmount = parseFloat(row.find('.old-fuel-amount').text()) || 0;
            let oldTotal = parseFloat(row.find('.old-total').text()) || 0;
            
            let newFuelAmount = newRate * fuelqty;
            let newTotal = newFuelAmount + cashamount;
            
            // Calculate differences
            let rateDiff = newRate - oldRate;
            let fuelAmtDiff = newFuelAmount - oldFuelAmount;
            let totalDiff = newTotal - oldTotal;
            
            let diffClass = totalDiff > 0 ? 'text-danger' : (totalDiff < 0 ? 'text-success' : 'text-muted');
            let rateDiffClass = rateDiff > 0 ? 'text-danger' : (rateDiff < 0 ? 'text-success' : 'text-muted');
            let fuelDiffClass = fuelAmtDiff > 0 ? 'text-danger' : (fuelAmtDiff < 0 ? 'text-success' : 'text-muted');
            
            let content = `
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Field</th>
                                <th class="text-center">Old Value</th>
                                <th class="text-center">New Value</th>
                                <th class="text-center">Difference</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Fuel Qty</strong></td>
                                <td class="text-center" colspan="3">${fuelqty.toFixed(2)} <span class="text-muted">(No Change)</span></td>
                            </tr>
                            <tr>
                                <td><strong>Cash Advance</strong></td>
                                <td class="text-center" colspan="3">${cashamount.toFixed(2)} <span class="text-muted">(No Change)</span></td>
                            </tr>
                            <tr>
                                <td><strong>Fuel Rate</strong></td>
                                <td class="text-center">${oldRate.toFixed(2)}</td>
                                <td class="text-center">${newRate.toFixed(2)}</td>
                                <td class="text-center ${rateDiffClass}">${rateDiff > 0 ? '+' : ''}${rateDiff.toFixed(2)}</td>
                            </tr>
                            <tr>
                                <td><strong>Fuel Amount</strong></td>
                                <td class="text-center">${oldFuelAmount.toFixed(2)}</td>
                                <td class="text-center">${newFuelAmount.toFixed(2)}</td>
                                <td class="text-center ${fuelDiffClass}">${fuelAmtDiff > 0 ? '+' : ''}${fuelAmtDiff.toFixed(2)}</td>
                            </tr>
                            <tr class="font-weight-bold">
                                <td><strong>Total Amount</strong></td>
                                <td class="text-center">${oldTotal.toFixed(2)}</td>
                                <td class="text-center">${newTotal.toFixed(2)}</td>
                                <td class="text-center ${diffClass}">${totalDiff > 0 ? '+' : ''}${totalDiff.toFixed(2)}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-warning mt-2"><i class="fas fa-exclamation-triangle"></i> This will update AdvancePayment, ProgramDetail, and Transaction records.</p>
            `;
            
            $('#singleUpdateContent').html(content);
            $('#singleUpdateModal').data('advance-id', id).data('new-rate', newRate).modal('show');
        });

        // Bulk new rate input - update all rows preview
        $('#bulkNewRate').off('input').on('input', function() {
            let newRate = parseFloat($(this).val()) || 0;
            $('.single-rate-input').val(newRate).trigger('input');
        });

        // Preview bulk changes
        $('#previewChangesBtn').off('click').on('click', function() {
            let selectedIds = [];
            let changes = [];
            
            $('.advance-checkbox:checked').each(function() {
                let row = $(this).closest('tr');
                let id = $(this).val();
                
                // Parse all values correctly
                let oldRate = parseFloat(row.find('.old-rate').text()) || 0;
                let newRate = parseFloat(row.find('.single-rate-input').val()) || 0;
                let fuelqty = parseFloat(row.find('.single-rate-input').data('fuelqty')) || 0;
                let cashamount = parseFloat(row.find('.single-rate-input').data('cashamount')) || 0;
                let oldFuelAmount = parseFloat(row.find('.old-fuel-amount').text()) || 0;
                let oldTotal = parseFloat(row.find('.old-total').text()) || 0;
                
                let newFuelAmount = newRate * fuelqty;
                let newTotal = newFuelAmount + cashamount;
                
                if (newRate != oldRate) {
                    selectedIds.push(id);
                    changes.push({
                        id: id,
                        vendor: row.find('td:nth-child(4)').text(),
                        truck: row.find('td:nth-child(5)').text(),
                        fuelqty: fuelqty,
                        cashamount: cashamount,
                        oldRate: oldRate,
                        newRate: newRate,
                        oldFuelAmount: oldFuelAmount,
                        newFuelAmount: newFuelAmount,
                        oldTotal: oldTotal,
                        newTotal: newTotal
                    });
                }
            });
            
            if (changes.length == 0) {
                showMessage('<div class="alert alert-warning">No changes detected or no records selected.</div>', 400);
                return;
            }
            
            pendingUpdateIds = selectedIds;
            pendingNewRate = changes[0].newRate;
            
            let html = `
                <h6 class="mb-3">Summary: <span class="badge badge-warning">${changes.length}</span> records will be updated</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Vendor</th>
                                <th>Truck</th>
                                <th class="text-center">Fuel Qty</th>
                                <th class="text-center">Old Rate</th>
                                <th class="text-center">New Rate</th>
                                <th class="text-center">Old Fuel Amt</th>
                                <th class="text-center">New Fuel Amt</th>
                                <th class="text-center">Old Total</th>
                                <th class="text-center">New Total</th>
                                <th class="text-center">Difference</th>
                            </tr>
                        </thead>
                        <tbody>`;
            
            let totalOldFuelAmount = 0;
            let totalNewFuelAmount = 0;
            let totalOldTotal = 0;
            let totalNewTotal = 0;
            
            changes.forEach(function(c) {
                let fuelDiff = c.newFuelAmount - c.oldFuelAmount;
                let totalDiff = c.newTotal - c.oldTotal;
                let diffClass = totalDiff > 0 ? 'text-danger' : (totalDiff < 0 ? 'text-success' : '');
                
                totalOldFuelAmount += c.oldFuelAmount;
                totalNewFuelAmount += c.newFuelAmount;
                totalOldTotal += c.oldTotal;
                totalNewTotal += c.newTotal;
                
                html += `
                    <tr>
                        <td>${c.vendor}</td>
                        <td>${c.truck}</td>
                        <td class="text-center">${c.fuelqty.toFixed(2)}</td>
                        <td class="text-center">${c.oldRate.toFixed(2)}</td>
                        <td class="text-center">${c.newRate.toFixed(2)}</td>
                        <td class="text-center">${c.oldFuelAmount.toFixed(2)}</td>
                        <td class="text-center">${c.newFuelAmount.toFixed(2)}</td>
                        <td class="text-center">${c.oldTotal.toFixed(2)}</td>
                        <td class="text-center">${c.newTotal.toFixed(2)}</td>
                        <td class="text-center ${diffClass}">${totalDiff > 0 ? '+' : ''}${totalDiff.toFixed(2)}</td>
                    </tr>`;
            });
            
            let grandFuelDiff = totalNewFuelAmount - totalOldFuelAmount;
            let grandTotalDiff = totalNewTotal - totalOldTotal;
            let grandDiffClass = grandTotalDiff > 0 ? 'text-danger' : (grandTotalDiff < 0 ? 'text-success' : '');
            let grandFuelDiffClass = grandFuelDiff > 0 ? 'text-danger' : (grandFuelDiff < 0 ? 'text-success' : '');
            
            html += `
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold bg-light">
                                <td colspan="2">GRAND TOTAL</td>
                                <td class="text-center">${changes.reduce((sum, c) => sum + c.fuelqty, 0).toFixed(2)}</td>
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td class="text-center">${totalOldFuelAmount.toFixed(2)}</td>
                                <td class="text-center">${totalNewFuelAmount.toFixed(2)}</td>
                                <td class="text-center">${totalOldTotal.toFixed(2)}</td>
                                <td class="text-center">${totalNewTotal.toFixed(2)}</td>
                                <td class="text-center ${grandDiffClass}">${grandTotalDiff > 0 ? '+' : ''}${grandTotalDiff.toFixed(2)}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="alert alert-info mb-0">
                            <strong>Fuel Amount Change:</strong> 
                            <span class="${grandFuelDiffClass}">${grandFuelDiff > 0 ? '+' : ''}${grandFuelDiff.toFixed(2)}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-warning mb-0">
                            <strong>Total Amount Change:</strong> 
                            <span class="${grandDiffClass}">${grandTotalDiff > 0 ? '+' : ''}${grandTotalDiff.toFixed(2)}</span>
                        </div>
                    </div>
                </div>
                <p class="text-danger mt-3"><i class="fas fa-exclamation-triangle"></i> This will update AdvancePayment, ProgramDetail, and Transaction records.</p>
            `;
            
            $('#previewContent').html(html);
            $('#confirmBulkUpdate').prop('disabled', false);
            $('#previewModal').modal('show');
        });
    }

    function updateSelectedCount() {
        let count = $('.advance-checkbox:checked').length;
        $('#selectedCount').text(count + ' records selected');
        if (count > 0) {
            $('#previewChangesBtn').prop('disabled', false);
        } else {
            $('#previewChangesBtn').prop('disabled', true);
            $('#confirmBulkUpdate').prop('disabled', true);
        }
    }

    // Confirm bulk update from modal
    $('#confirmFromModal').click(function() {
        $('#previewModal').modal('hide');
        performBulkUpdate();
    });

    // Confirm bulk update from footer
    $('#confirmBulkUpdate').click(function() {
        if (pendingUpdateIds.length == 0) {
            showMessage('<div class="alert alert-warning">Please preview changes first.</div>', 400);
            return;
        }
        performBulkUpdate();
    });

    function performBulkUpdate() {
        let newRate = $('#bulkNewRate').val() || pendingNewRate;
        
        if (pendingUpdateIds.length == 0) {
            $('.advance-checkbox:checked').each(function() {
                let row = $(this).closest('tr');
                let newRateInput = parseFloat(row.find('.single-rate-input').val());
                let oldRate = parseFloat(row.find('.old-rate').text());
                if (newRateInput != oldRate) {
                    pendingUpdateIds.push($(this).val());
                }
            });
            pendingNewRate = newRate;
        }
        
        if (pendingUpdateIds.length == 0) {
            showMessage('<div class="alert alert-warning">No records to update.</div>', 400);
            return;
        }

        $.ajax({
            url: '{{ route("admin.fuelRateUpdate.update") }}',
            method: 'POST',
            data: {
                new_fuel_rate: pendingNewRate,
                search_date: searchDateForBulk || $('#searchDate').val(),
                advance_ids: pendingUpdateIds,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#confirmBulkUpdate').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');
            },
            success: function(response) {
                showMessage(response.message, response.status);
                if (response.status == 300) {
                    pendingUpdateIds.forEach(function(id) {
                        $('tr[data-id="' + id + '"]').addClass('updated-row');
                    });
                    pendingUpdateIds = [];
                    pendingNewRate = 0;
                    $('#confirmBulkUpdate').prop('disabled', true);
                }
            },
            error: function(xhr) {
                showMessage('<div class="alert alert-danger">Error occurred.</div>', 400);
            },
            complete: function() {
                $('#confirmBulkUpdate').prop('disabled', false).html('<i class="fas fa-check"></i> Confirm Update');
            }
        });
    }

    // Confirm single update from modal
    $('#confirmSingleFromModal').click(function() {
        let id = $('#singleUpdateModal').data('advance-id');
        let rate = $('#singleUpdateModal').data('new-rate');
        
        $.ajax({
            url: '{{ route("admin.fuelRateUpdate.singleUpdate") }}',
            method: 'POST',
            data: {
                advance_id: id,
                fuel_rate: rate,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                $('#confirmSingleFromModal').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');
            },
            success: function(response) {
                showMessage(response.message, response.status);
                if (response.status == 300) {
                    let row = $('tr[data-id="' + id + '"]');
                    row.find('.old-rate').text(rate);
                    row.find('.old-fuel-amount').text(response.new_fuel_amount.toFixed(2));
                    row.find('.old-total').text(response.new_total_amount.toFixed(2));
                    row.find('.new-fuel-amount').text(response.new_fuel_amount.toFixed(2));
                    row.find('.new-total').text(response.new_total_amount.toFixed(2));
                    row.find('.single-rate-input').val(rate);
                    row.addClass('updated-row');
                }
                $('#singleUpdateModal').modal('hide');
            },
            error: function(xhr) {
                showMessage('<div class="alert alert-danger">Error occurred.</div>', 400);
            },
            complete: function() {
                $('#confirmSingleFromModal').prop('disabled', false).html('<i class="fas fa-check"></i> Update');
            }
        });
    });

    function showMessage(message, status) {
        let type = status == 300 || status == 200 ? 'success' : (status == 400 ? 'warning' : 'danger');
        let alertClass = 'alert-' + type;
        $('.ermsg').html('<div class="alert ' + alertClass + ' alert-dismissible fade show">' + 
            message.replace(/<div class="alert alert-\w+">/g, '').replace(/<\/div>/g, '') + 
            '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
        
        setTimeout(function() {
            $('.ermsg').html('');
        }, 5000);
    }
});
</script>
@endsection