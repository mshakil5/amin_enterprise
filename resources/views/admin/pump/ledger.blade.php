@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                
                <!-- Back Button -->
                <div class="mb-3">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>

                <!-- Main Card -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-book mr-1"></i> 
                            Ledger: {{ $pump->name }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center" width="5%">#</th>
                                        <th class="text-center" width="10%">Date</th>
                                        <th width="30%">Description</th>
                                        <th class="text-center" width="10%">Qty</th>
                                        <th class="text-right" width="15%">Debit</th>
                                        <th class="text-right" width="15%">Credit</th>
                                        <th class="text-right" width="15%">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $runningBalance = 0; 
                                        $sl = 1; 
                                    @endphp
                                    
                                    @forelse($fuelBills as $bill)
                                        @php
                                            // Use the accessors we created in the model
                                            $fuelQty = $bill->total_fuel_qty;
                                            $fuelAmount = $bill->total_fuel_amount;
                                            
                                            // Debit increases the balance
                                            $runningBalance += $fuelAmount;
                                        @endphp
                                        
                                        <tr>
                                            <td class="text-center">{{ $sl++ }}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($bill->date)->format('d-m-Y') }}</td>
                                            <td>
                                                Fuel Bill #{{ $bill->bill_number }}
                                                <br>
                                                <small class="text-muted">Unique ID: {{ $bill->unique_id }}</small>
                                            </td>
                                            <td class="text-center">{{ number_format($fuelQty, 2) }}</td>
                                            <td class="text-right text-info">
                                                {{ $fuelAmount > 0 ? number_format($fuelAmount, 2) : '0.00' }}
                                            </td>
                                            <td class="text-right text-success">
                                                0.00
                                            </td>
                                            <td class="text-right font-weight-bold">
                                                {{ number_format($runningBalance, 2) }} Dr
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                No transactions found for this pump.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($fuelBills->isNotEmpty())
                                <tfoot>
                                    <tr class="font-weight-bold bg-light">
                                        <td colspan="3" class="text-right">Total:</td>
                                        <td class="text-center">{{ number_format($fuelBills->sum(function($b) { return $b->total_fuel_qty; }), 2) }}</td>
                                        <td class="text-right text-info">{{ number_format($runningBalance, 2) }}</td>
                                        <td class="text-right text-success">0.00</td>
                                        <td class="text-right">{{ number_format($runningBalance, 2) }} Dr</td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection