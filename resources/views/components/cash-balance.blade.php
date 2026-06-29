@props([
    'date' => null,
    'cashInHandOpening' => 0,
    'cashInFieldOpening' => 0,
    'cashInHandClosing' => 0,
    'cashInFieldClosing' => 0,
    'type' => 'rows', // 'rows' | 'closing-rows' | 'cards' | 'compact'
])

@switch($type)

@case('rows')
    {{-- Opening Balance Table Rows --}}
    <tr>
        <td class="text-center">{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</td>
        <td class="font-weight-bold">Cash In Hand (Opening Balance)</td>
        <td></td><td></td>
        <td class="text-right text-success font-weight-bold">{{ number_format($cashInHandOpening, 2) }}</td>
        <td></td><td></td><td></td>
    </tr>
    <tr>
        <td class="text-center">{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</td>
        <td class="font-weight-bold">Cash In Field (Opening Balance)</td>
        <td></td><td></td>
        <td class="text-right text-success font-weight-bold">{{ number_format($cashInFieldOpening, 2) }}</td>
        <td></td><td></td><td></td>
    </tr>
    @break

@case('closing-rows')
    {{-- Closing Balance Table Rows --}}
    <tr class="font-italic">
        <td class="text-center">{{ $date }}</td>
        <td>Cash In Hand (Closing Balance)</td>
        <td colspan="4"></td>
        <td class="text-right text-info font-weight-bold">{{ number_format($cashInHandClosing, 2) }}</td>
        <td></td>
    </tr>
    <tr class="font-italic">
        <td class="text-center">{{ $date }}</td>
        <td>Cash In Field (Closing Balance)</td>
        <td colspan="4"></td>
        <td class="text-right text-info font-weight-bold">{{ number_format($cashInFieldClosing, 2) }}</td>
        <td></td>
    </tr>
    @break

@case('cards')
    {{-- Dashboard Card Layout --}}
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-success shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="small text-muted text-uppercase">Cash In Hand (Opening)</div>
                    <div class="h5 font-weight-bold text-success mb-0">{{ number_format($cashInHandOpening, 2) }}</div>
                    <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="small text-muted text-uppercase">Cash In Field (Opening)</div>
                    <div class="h5 font-weight-bold text-primary mb-0">{{ number_format($cashInFieldOpening, 2) }}</div>
                    <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="small text-muted text-uppercase">Cash In Hand (Closing)</div>
                    <div class="h5 font-weight-bold text-info mb-0">{{ number_format($cashInHandClosing, 2) }}</div>
                    <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-warning shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="small text-muted text-uppercase">Cash In Field (Closing)</div>
                    <div class="h5 font-weight-bold text-warning mb-0">{{ number_format($cashInFieldClosing, 2) }}</div>
                    <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>
    @break

@case('compact')
    {{-- Inline Compact Style (for sidebars, headers, etc.) --}}
    <div class="d-flex flex-wrap gap-2">
        <span class="badge badge-success p-2">
            Hand Open: <strong>{{ number_format($cashInHandOpening, 2) }}</strong>
        </span>
        <span class="badge badge-primary p-2">
            Field Open: <strong>{{ number_format($cashInFieldOpening, 2) }}</strong>
        </span>
        <span class="badge badge-info p-2">
            Hand Close: <strong>{{ number_format($cashInHandClosing, 2) }}</strong>
        </span>
        <span class="badge badge-warning p-2">
            Field Close: <strong>{{ number_format($cashInFieldClosing, 2) }}</strong>
        </span>
    </div>
    @break

@endswitch