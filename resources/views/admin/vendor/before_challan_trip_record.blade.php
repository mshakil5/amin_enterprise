@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">

        {{-- ============================================= --}}
        {{-- PAGE HEADER --}}
        {{-- ============================================= --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h3 class="mb-0">
                    <i class="fas fa-ship mr-2 text-primary"></i> Before Challan Trips: {{ $vendor->name ?? 'Unknown Vendor' }}
                </h3>
                @if(!empty($vendor->phone))
                    <small class="text-muted font-weight-bold"><i class="fas fa-phone mr-1"></i> {{ $vendor->phone }}</small>
                @endif
            </div>
            
            <div class="d-flex flex-wrap gap-2">
                {{-- Export Buttons --}}
                <button class="btn btn-sm btn-success" id="btn-excel"><i class="fas fa-file-excel"></i> Excel</button>
                <button class="btn btn-sm btn-danger" id="btn-pdf"><i class="fas fa-file-pdf"></i> PDF</button>
                <button class="btn btn-sm btn-warning" id="btn-summary"><i class="fas fa-file-alt"></i> Summary</button>
                
                {{-- WhatsApp Button --}}
                @php
                    $totalTrips = 0;
                    foreach ($data as $motherVasselId => $trips) {
                        $totalTrips += $trips->count();
                    }
                    // Format phone number for WhatsApp (Removes hyphens/spaces, converts 0 to 880)
                    $rawPhone = preg_replace('/\D/', '', $vendor->phone ?? '');
                    if (strlen($rawPhone) > 0 && $rawPhone[0] === '0') {
                        $rawPhone = '880' . substr($rawPhone, 1);
                    }
                    $waMessage = "Hello {$vendor->name}, your pending trip summary:\nTotal Pending Trips: {$totalTrips}\nTotal Mother Vessels: {$data->count()}";
                    $waUrl = "https://wa.me/{$rawPhone}?text=" . urlencode($waMessage);
                @endphp
                <a href="{{ $waUrl }}" target="_blank" class="btn btn-sm btn-primary"><i class="fab fa-whatsapp"></i> WhatsApp</a>

                <a href="{{ url()->previous() }}" class="btn btn-outline-dark btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- ALERTS --}}
        {{-- ============================================= --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- ============================================= --}}
        {{-- SUMMARY INFO BOX (Total Count) --}}
        {{-- ============================================= --}}
        <div class="row mb-3">
            <div class="col-lg-3 col-md-6">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-truck-loading"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pending Trips</span>
                        <span class="info-box-number">{{ $totalTrips }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-ship"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Mother Vessels</span>
                        <span class="info-box-number">{{ $data->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- MOTHER VESSEL WISE GROUPED DATA --}}
        {{-- ============================================= --}}
        @foreach ($data as $motherVasselId => $trips)
            @php 
                $vesselName = $trips->first()->motherVassel->name ?? 'Unknown Vessel'; 
                $tripCount = $trips->count();
            @endphp
            
            <div class="card card-primary card-outline mb-3 collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-ship mr-1"></i> {{ $vesselName }}
                        <span class="badge badge-light ml-2 p-2">{{ $tripCount }} Trips</span>
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm mb-0" style="font-size: 12px;">
                            <thead>
                                <tr class="bg-dark text-white text-center">
                                    <th style="width:35px">#</th>
                                    <th style="width:100px">Date</th>
                                    <th style="width:120px">Truck Number</th>
                                    <th>Challan No</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trips as $key => $detail)
                                <tr>
                                    <td class="text-center align-middle">{{ $key + 1 }}</td>
                                    <td class="text-center align-middle">
                                        {{ $detail->date ? \Carbon\Carbon::parse($detail->date)->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="text-center align-middle font-weight-bold">{{ strtoupper($detail->truck_number ?? 'N/A') }}</td>
                                    <td class="text-center align-middle">{{ $detail->challan_no ?? 'N/A' }}</td>
                                    <td class="text-center align-middle font-weight-bold">{{ $detail->dest_qty ?? 'Pending' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        @if ($data->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle mr-2"></i> No before challan trip records found for this vendor.
            </div>
        @endif

        {{-- ============================================= --}}
        {{-- HIDDEN TABLE FOR GLOBAL EXPORT --}}
        {{-- ============================================= --}}
        <div class="d-none">
            <table id="exportTable" class="table table-bordered table-striped table-sm" style="font-size: 12px;">
                <thead>
                    <tr class="bg-dark text-white text-center">
                        <th>Mother Vessel</th>
                        <th>Date</th>
                        <th>Truck Number</th>
                        <th>Challan No</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $motherVasselId => $trips)
                        @foreach ($trips as $detail)
                        <tr>
                            <td>{{ $trips->first()->motherVassel->name ?? 'N/A' }}</td>
                            <td>{{ $detail->date ? \Carbon\Carbon::parse($detail->date)->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ strtoupper($detail->truck_number ?? 'N/A') }}</td>
                            <td>{{ $detail->challan_no ?? 'N/A' }}</td>
                            <td>{{ $detail->dest_qty ?? 'Pending' }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</section>
@endsection

@section('style')
<style>
    .info-box .info-box-number { font-size: 18px !important; }
    .card-title .badge { font-size: 12px; }
</style>
@endsection

@section('script')
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    $(document).ready(function () {
        
        // Pass PHP variables to JavaScript
        var vendorName = '{{ $vendor->name ?? "Unknown Vendor" }}';
        var vendorPhone = '{{ $vendor->phone ?? "N/A" }}';
        var totalTrips = '{{ $totalTrips }}';
        var totalVessels = '{{ $data->count() }}';

        // Initialize DataTable on the hidden table for exporting purposes
        var exportTbl = $('#exportTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: 'Export Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Before Challan Trips Report', // Main Title
                    messageTop: 'Vendor Name: ' + vendorName + '\nPhone: ' + vendorPhone + '\nTotal Pending Trips: ' + totalTrips + '\nTotal Mother Vessels: ' + totalVessels
                },
                {
                    extend: 'pdf',
                    text: 'Export PDF',
                    className: 'btn btn-danger btn-sm',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'Before Challan Trips Report', // Main Title
                    messageTop: 'Vendor Name: ' + vendorName + ' | Phone: ' + vendorPhone + ' | Total Trips: ' + totalTrips + ' | Total Vessels: ' + totalVessels
                },
                { 
                    extend: 'print', 
                    text: 'Print Summary', 
                    className: 'btn btn-warning btn-sm',
                    title: 'Before Challan Trips Summary', // Main Title
                    messageTop: '<h4>Vendor Name: ' + vendorName + '</h4><h5>Phone: ' + vendorPhone + '</h5><p><strong>Total Pending Trips:</strong> ' + totalTrips + ' | <strong>Total Mother Vessels:</strong> ' + totalVessels + '</p>'
                }
            ]
        });

        $('.dt-buttons').hide(); // Hide default DataTable buttons

        // Trigger exports via custom header buttons
        $('#btn-excel').on('click', function() { exportTbl.button(0).trigger(); });
        $('#btn-pdf').on('click', function() { exportTbl.button(1).trigger(); });
        $('#btn-summary').on('click', function() { exportTbl.button(2).trigger(); });
    });
</script>
@endsection