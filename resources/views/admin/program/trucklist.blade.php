@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content mt-3">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <a href="{{route('admin.allProgram')}}" class="btn btn-secondary my-3">
          <i class="fas fa-arrow-left"></i> Back
        </a>
        <span class="badge badge-primary my-3 p-2" style="font-size: 14px;">
          Program ID: {{ $programId }}
        </span>
      </div>
    </div>
  </div>
</section>

<!-- Tabs Section -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title">Truck List Report</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <!-- Custom Tabs -->
            <ul class="nav nav-tabs" id="customTabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="vendor-tab" data-toggle="tab" href="#vendor-wise" role="tab" aria-controls="vendor-wise" aria-selected="true">
                  <i class="fas fa-truck mr-1"></i> Vendor Wise
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="truck-tab" data-toggle="tab" href="#truck-wise" role="tab" aria-controls="truck-wise" aria-selected="false">
                  <i class="fas fa-list-ol mr-1"></i> Truck Number Wise
                </a>
              </li>
            </ul>

            <div class="tab-content" id="customTabContent">

              <!-- ==================== TAB 1: VENDOR WISE ==================== -->
              <div class="tab-pane fade show active" id="vendor-wise" role="tabpanel" aria-labelledby="vendor-tab">
                <div class="row mt-3 mb-3">
                  <div class="col-md-4">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                      </div>
                      <input type="text" id="vendorSearch" class="form-control" placeholder="Search by Vendor Name...">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                      </div>
                      <input type="text" id="vendorTruckSearch" class="form-control" placeholder="Search by Truck Number...">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                      </div>
                      <input type="text" id="vendorChallanSearch" class="form-control" placeholder="Search by Challan No...">
                    </div>
                  </div>
                </div>

                @php
                  $vendorWiseData = $data->groupBy('vendor_id');
                @endphp

                @foreach($vendorWiseData as $vendorId => $vendorItems)
                  <div class="vendor-group" data-vendor-name="{{ $vendorItems->first()->vendor->name ?? 'Unknown' }}">
                    <!-- Vendor Header -->
                    <div class="card card-info mb-3 vendor-card">
                      <div class="card-header" style="cursor: pointer;" data-toggle="collapse" data-target="#vendorCollapse_{{ $vendorId }}">
                        <h5 class="mb-0">
                          <i class="fas fa-user-tie mr-2"></i>
                          <strong>Vendor: {{ $vendorItems->first()->vendor->name ?? 'Unknown' }}</strong>
                          <span class="badge badge-light ml-2">Total Trucks: {{ $vendorItems->count() }}</span>
                          <span class="badge badge-success ml-2">Total Carrying Bill: {{ number_format($vendorItems->sum('carrying_bill'), 2) }}</span>
                          <span class="badge badge-warning ml-2">Total Transport Cost: {{ number_format($vendorItems->sum('transportcost'), 2) }}</span>
                          <span class="badge badge-danger ml-2">Total Advance: {{ number_format($vendorItems->sum('advance'), 2) }}</span>
                          <i class="fas fa-chevron-down float-right mt-1"></i>
                        </h5>
                      </div>
                      <div class="collapse show" id="vendorCollapse_{{ $vendorId }}">
                        <div class="card-body p-0">
                          <table class="table table-bordered table-striped table-sm mb-0 vendor-table">
                            <thead class="bg-info text-white">
                              <tr>
                                <th style="width: 50px; text-align: center;">Sl</th>
                                <th style="text-align: center;">Date</th>
                                <th style="text-align: center;">Truck Number</th>
                                <th style="text-align: center;">Challan No</th>
                                <th style="text-align: center;">Carrying Bill</th>
                                <th style="text-align: center;">Old Carrying Bill</th>
                                <th style="text-align: center;">Transport Cost</th>
                                <th style="text-align: center;">Additional Cost</th>
                                <th style="text-align: center;">Advance</th>
                                <th style="text-align: center;">Ghat</th>
                                <th style="text-align: center;">Client</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($vendorItems as $key => $item)
                                <tr class="vendor-row" 
                                    data-truck-number="{{ strtolower($item->truck_number ?? '') }}" 
                                    data-challan-no="{{ strtolower($item->challan_no ?? '') }}">
                                  <td style="text-align: center;">{{ $key + 1 }}</td>
                                  <td style="text-align: center;">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                                  <td style="text-align: center;">
                                    <span class="badge badge-secondary">{{ $item->truck_number ?? '' }}</span>
                                  </td>
                                  <td style="text-align: center;">{{ $item->challan_no ?? '' }}</td>
                                  <td style="text-align: right;">{{ number_format($item->carrying_bill, 2) }}</td>
                                  <td style="text-align: right;">{{ number_format($item->old_carrying_bill, 2) }}</td>
                                  <td style="text-align: right;">{{ number_format($item->transportcost, 2) }}</td>
                                  <td style="text-align: right;">{{ number_format($item->additional_cost, 2) }}</td>
                                  <td style="text-align: right;">{{ number_format($item->advance, 2) }}</td>
                                  <td style="text-align: center;">{{ $item->ghat->name ?? '' }}</td>
                                  <td style="text-align: center;">{{ $item->client->name ?? '' }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                            <tfoot>
                              <tr class="bg-gray-light">
                                <th colspan="4" style="text-align: right;">Total:</th>
                                <th style="text-align: right;">{{ number_format($vendorItems->sum('carrying_bill'), 2) }}</th>
                                <th style="text-align: right;">{{ number_format($vendorItems->sum('old_carrying_bill'), 2) }}</th>
                                <th style="text-align: right;">{{ number_format($vendorItems->sum('transportcost'), 2) }}</th>
                                <th style="text-align: right;">{{ number_format($vendorItems->sum('additional_cost'), 2) }}</th>
                                <th style="text-align: right;">{{ number_format($vendorItems->sum('advance'), 2) }}</th>
                                <th colspan="2"></th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach

                <div id="vendorNoResult" class="alert alert-warning d-none">
                  <i class="fas fa-exclamation-triangle mr-2"></i> No vendor found matching your search criteria.
                </div>
              </div>

              <!-- ==================== TAB 2: TRUCK NUMBER WISE ==================== -->
              <div class="tab-pane fade" id="truck-wise" role="tabpanel" aria-labelledby="truck-tab">
                <div class="row mt-3 mb-3">
                  <div class="col-md-4">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                      </div>
                      <input type="text" id="truckNumberSearch" class="form-control" placeholder="Search by Truck Number...">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                      </div>
                      <input type="text" id="truckVendorSearch" class="form-control" placeholder="Search by Vendor Name...">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                      </div>
                      <input type="text" id="truckChallanSearch" class="form-control" placeholder="Search by Challan No...">
                    </div>
                  </div>
                </div>

                @php
                  $truckWiseData = $data->groupBy('truck_number');
                @endphp

                @foreach($truckWiseData as $truckNumber => $truckItems)
                  <div class="truck-group" data-truck-number="{{ strtolower($truckNumber) }}">
                    <!-- Truck Header -->
                    <div class="card card-warning mb-3 truck-card">
                      <div class="card-header" style="cursor: pointer;" data-toggle="collapse" data-target="#truckCollapse_{{ \Str::slug($truckNumber) }}">
                        <h5 class="mb-0">
                          <i class="fas fa-truck mr-2"></i>
                          <strong>Truck: {{ $truckNumber ?? 'Unknown' }}</strong>
                          <span class="badge badge-light ml-2">Total Trips: {{ $truckItems->count() }}</span>
                          <span class="badge badge-success ml-2">Total Carrying Bill: {{ number_format($truckItems->sum('carrying_bill'), 2) }}</span>
                          <span class="badge badge-warning ml-2">Total Transport Cost: {{ number_format($truckItems->sum('transportcost'), 2) }}</span>
                          <span class="badge badge-danger ml-2">Total Advance: {{ number_format($truckItems->sum('advance'), 2) }}</span>
                          <i class="fas fa-chevron-down float-right mt-1"></i>
                        </h5>
                      </div>
                      <div class="collapse show" id="truckCollapse_{{ \Str::slug($truckNumber) }}">
                        <div class="card-body p-0">
                          <table class="table table-bordered table-striped table-sm mb-0 truck-table">
                            <thead class="bg-warning text-dark">
                              <tr>
                                <th style="width: 50px; text-align: center;">Sl</th>
                                <th style="text-align: center;">Date</th>
                                <th style="text-align: center;">Vendor</th>
                                <th style="text-align: center;">Challan No</th>
                                <th style="text-align: center;">Carrying Bill</th>
                                <th style="text-align: center;">Old Carrying Bill</th>
                                <th style="text-align: center;">Transport Cost</th>
                                <th style="text-align: center;">Additional Cost</th>
                                <th style="text-align: center;">Advance</th>
                                <th style="text-align: center;">Ghat</th>
                                <th style="text-align: center;">Client</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($truckItems as $key => $item)
                                <tr class="truck-row" 
                                    data-vendor-name="{{ strtolower($item->vendor->name ?? '') }}" 
                                    data-challan-no="{{ strtolower($item->challan_no ?? '') }}">
                                  <td style="text-align: center;">{{ $key + 1 }}</td>
                                  <td style="text-align: center;">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                                  <td style="text-align: center;">
                                    <span class="badge badge-secondary">{{ $item->vendor->name ?? '' }}</span>
                                  </td>
                                  <td style="text-align: center;">{{ $item->challan_no ?? '' }}</td>
                                  <td style="text-align: right;">{{ number_format($item->carrying_bill, 2) }}</td>
                                  <td style="text-align: right;">{{ number_format($item->old_carrying_bill, 2) }}</td>
                                  <td style="text-align: right;">{{ number_format($item->transportcost, 2) }}</td>
                                  <td style="text-align: right;">{{ number_format($item->additional_cost, 2) }}</td>
                                  <td style="text-align: right;">{{ number_format($item->advance, 2) }}</td>
                                  <td style="text-align: center;">{{ $item->ghat->name ?? '' }}</td>
                                  <td style="text-align: center;">{{ $item->client->name ?? '' }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                            <tfoot>
                              <tr class="bg-gray-light">
                                <th colspan="4" style="text-align: right;">Total:</th>
                                <th style="text-align: right;">{{ number_format($truckItems->sum('carrying_bill'), 2) }}</th>
                                <th style="text-align: right;">{{ number_format($truckItems->sum('old_carrying_bill'), 2) }}</th>
                                <th style="text-align: right;">{{ number_format($truckItems->sum('transportcost'), 2) }}</th>
                                <th style="text-align: right;">{{ number_format($truckItems->sum('additional_cost'), 2) }}</th>
                                <th style="text-align: right;">{{ number_format($truckItems->sum('advance'), 2) }}</th>
                                <th colspan="2"></th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach

                <div id="truckNoResult" class="alert alert-warning d-none">
                  <i class="fas fa-exclamation-triangle mr-2"></i> No truck found matching your search criteria.
                </div>
              </div>

            </div>
            <!-- /.tab-content -->

            <!-- Summary Box -->
            <div class="row mt-4">
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-info"><i class="fas fa-truck"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Total Entries</span>
                    <span class="info-box-number">{{ $data->count() }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-success"><i class="fas fa-users"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Total Vendors</span>
                    <span class="info-box-number">{{ $data->unique('vendor_id')->count() }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-warning"><i class="fas fa-truck-moving"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Unique Trucks</span>
                    <span class="info-box-number">{{ $data->unique('truck_number')->count() }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-box">
                  <span class="info-box-icon bg-danger"><i class="fas fa-money-bill-wave"></i></span>
                  <div class="info-box-content">
                    <span class="info-box-text">Total Carrying Bill</span>
                    <span class="info-box-number">{{ number_format($data->sum('carrying_bill'), 2) }}</span>
                  </div>
                </div>
              </div>
            </div>

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

@endsection

@section('script')
<script>
  $(document).ready(function() {

    // ==================== VENDOR WISE SEARCH ====================
    
    // Search by Vendor Name
    $('#vendorSearch').on('keyup', function() {
      var searchTerm = $(this).val().toLowerCase();
      var hasResult = false;
      
      $('.vendor-group').each(function() {
        var vendorName = $(this).data('vendor-name').toLowerCase();
        
        if (vendorName.includes(searchTerm)) {
          $(this).show();
          hasResult = true;
        } else {
          $(this).hide();
        }
      });
      
      if (hasResult) {
        $('#vendorNoResult').addClass('d-none');
      } else {
        $('#vendorNoResult').removeClass('d-none');
      }
    });

    // Search by Truck Number (within vendor tab)
    $('#vendorTruckSearch').on('keyup', function() {
      var searchTerm = $(this).val().toLowerCase();
      var hasResult = false;
      
      $('.vendor-group').each(function() {
        var groupHasMatch = false;
        
        $(this).find('.vendor-row').each(function() {
          var truckNumber = $(this).data('truck-number');
          
          if (truckNumber.includes(searchTerm)) {
            $(this).show();
            groupHasMatch = true;
          } else {
            $(this).hide();
          }
        });
        
        if (groupHasMatch) {
          $(this).show();
          hasResult = true;
        } else {
          $(this).hide();
        }
      });
      
      if (hasResult) {
        $('#vendorNoResult').addClass('d-none');
      } else {
        $('#vendorNoResult').removeClass('d-none');
      }
    });

    // Search by Challan No (within vendor tab)
    $('#vendorChallanSearch').on('keyup', function() {
      var searchTerm = $(this).val().toLowerCase();
      var hasResult = false;
      
      $('.vendor-group').each(function() {
        var groupHasMatch = false;
        
        $(this).find('.vendor-row').each(function() {
          var challanNo = $(this).data('challan-no');
          
          if (challanNo.includes(searchTerm)) {
            $(this).show();
            groupHasMatch = true;
          } else {
            $(this).hide();
          }
        });
        
        if (groupHasMatch) {
          $(this).show();
          hasResult = true;
        } else {
          $(this).hide();
        }
      });
      
      if (hasResult) {
        $('#vendorNoResult').addClass('d-none');
      } else {
        $('#vendorNoResult').removeClass('d-none');
      }
    });

    // ==================== TRUCK WISE SEARCH ====================
    
    // Search by Truck Number
    $('#truckNumberSearch').on('keyup', function() {
      var searchTerm = $(this).val().toLowerCase();
      var hasResult = false;
      
      $('.truck-group').each(function() {
        var truckNumber = $(this).data('truck-number');
        
        if (truckNumber.includes(searchTerm)) {
          $(this).show();
          hasResult = true;
        } else {
          $(this).hide();
        }
      });
      
      if (hasResult) {
        $('#truckNoResult').addClass('d-none');
      } else {
        $('#truckNoResult').removeClass('d-none');
      }
    });

    // Search by Vendor Name (within truck tab)
    $('#truckVendorSearch').on('keyup', function() {
      var searchTerm = $(this).val().toLowerCase();
      var hasResult = false;
      
      $('.truck-group').each(function() {
        var groupHasMatch = false;
        
        $(this).find('.truck-row').each(function() {
          var vendorName = $(this).data('vendor-name');
          
          if (vendorName.includes(searchTerm)) {
            $(this).show();
            groupHasMatch = true;
          } else {
            $(this).hide();
          }
        });
        
        if (groupHasMatch) {
          $(this).show();
          hasResult = true;
        } else {
          $(this).hide();
        }
      });
      
      if (hasResult) {
        $('#truckNoResult').addClass('d-none');
      } else {
        $('#truckNoResult').removeClass('d-none');
      }
    });

    // Search by Challan No (within truck tab)
    $('#truckChallanSearch').on('keyup', function() {
      var searchTerm = $(this).val().toLowerCase();
      var hasResult = false;
      
      $('.truck-group').each(function() {
        var groupHasMatch = false;
        
        $(this).find('.truck-row').each(function() {
          var challanNo = $(this).data('challan-no');
          
          if (challanNo.includes(searchTerm)) {
            $(this).show();
            groupHasMatch = true;
          } else {
            $(this).hide();
          }
        });
        
        if (groupHasMatch) {
          $(this).show();
          hasResult = true;
        } else {
          $(this).hide();
        }
      });
      
      if (hasResult) {
        $('#truckNoResult').addClass('d-none');
      } else {
        $('#truckNoResult').removeClass('d-none');
      }
    });

    // ==================== CLEAR SEARCH ON TAB SWITCH ====================
    
    $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
      // Clear all search inputs when switching tabs
      $('#vendorSearch, #vendorTruckSearch, #vendorChallanSearch, #truckNumberSearch, #truckVendorSearch, #truckChallanSearch').val('');
      
      // Show all groups again
      $('.vendor-group, .truck-group').show();
      $('.vendor-row, .truck-row').show();
      $('.vendorNoResult, .truckNoResult').addClass('d-none');
    });

    // ==================== CLEAR SEARCH BUTTONS ====================
    
    // Add clear buttons dynamically
    $('.input-group').each(function() {
      var input = $(this).find('input[type="text"]');
      if (input.length) {
        $(this).append(
          '<div class="input-group-append">' +
            '<button class="btn btn-danger clear-search" type="button"><i class="fas fa-times"></i></button>' +
          '</div>'
        );
      }
    });

    // Clear search functionality
    $('.clear-search').on('click', function() {
      var input = $(this).closest('.input-group').find('input[type="text"]');
      input.val('').trigger('keyup');
    });

  });
</script>
@endsection