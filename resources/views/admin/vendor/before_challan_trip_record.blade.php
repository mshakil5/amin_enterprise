@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">

        {{-- ============================================= --}}
        {{-- PAGE HEADER --}}
        {{-- ============================================= --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">
                <i class="fas fa-ship mr-2 text-primary"></i> Before Challan Trips: {{ $vendor->name ?? 'Unknown Vendor' }}
            </h3>
            <a href="{{ url()->previous() }}" class="btn btn-outline-dark btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
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
        @php $totalTrips = 0; @endphp
        @foreach ($data as $motherVasselId => $trips)
            @php $totalTrips += $trips->count(); @endphp
        @endforeach

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
                // Get the Mother Vessel name from the first record of the group
                $vesselName = $trips->first()->motherVassel->name ?? 'Unknown Vessel'; 
                $tripCount = $trips->count();
            @endphp
            
            {{-- Added 'collapsed-card' class to minimize by default --}}
            <div class="card card-primary card-outline mb-3 collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-ship mr-1"></i> {{ $vesselName }}
                        <span class="badge badge-light ml-2 p-2">{{ $tripCount }} Trips</span>
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            {{-- Changed icon to 'fa-plus' to match the collapsed state --}}
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
                                    {{-- Removed Destination & Action Columns --}}
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
                                    {{-- Removed Destination & Action Columns --}}
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
<script>
    $(document).ready(function () {
        // Cards are minimized by default via the 'collapsed-card' class.
        // AdminLTE handles the toggle automatically.
    });
</script>
@endsection