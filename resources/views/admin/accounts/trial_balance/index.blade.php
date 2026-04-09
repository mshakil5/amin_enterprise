@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
  <div class="container-fluid">
      
      {{-- Breadcrumb --}}
      <div class="breadcrumb-area no-print">
          <ol class="breadcrumb float-left">
              <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
              <li class="breadcrumb-item active">Trial Balance</li>
          </ol>
      </div>

      {{-- Action Buttons --}}
      <div class="row mb-3 no-print">
          <div class="col-12 d-flex justify-content-between">
              <a href="{{ url()->previous() }}" class="btn btn-secondary">
                  <i class="fas fa-arrow-left mr-1"></i> Back
              </a>
              <div>
                  <a href="{{ route('admin.trialBalance') }}" class="btn btn-outline-secondary mr-2">
                      <i class="fas fa-redo mr-1"></i> Reset
                  </a>
                  <button onclick="window.print();" class="btn btn-info">
                      <i class="fas fa-print mr-1"></i> Print
                  </button>
              </div>
          </div>
      </div>

      {{-- Date Filter --}}
      <div class="row mb-3 no-print">
          <div class="col-md-4">
              <div class="card card-primary card-outline">
                  <div class="card-header py-2">
                      <h6 class="card-title m-0"><i class="fas fa-filter mr-1"></i> Date Filter</h6>
                  </div>
                  <div class="card-body py-3">
                      <form method="GET" action="{{ route('admin.trialBalance') }}">
                          <div class="row">
                              <div class="col-6">
                                  <label class="mb-1">End Date</label>
                                  <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm" required>
                              </div>
                              <div class="col-6 d-flex align-items-end">
                                  <button type="submit" class="btn btn-primary btn-sm btn-block">
                                      <i class="fas fa-search mr-1"></i> Filter
                                  </button>
                              </div>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
      </div>

      {{-- Trial Balance Report --}}
      <div class="row print-area">
          <div class="col-12">
              <div class="card card-secondary">
                  <div class="card-body">

                      {{-- ===== Header ===== --}}
                      <div class="text-center mb-3">
                          <h3 class="mb-0 font-weight-bold">Amin Enterprise - BSRM Program</h3>
                          <h4 class="mb-1">Trial Balance</h4>
                          <p class="text-muted mb-0">
                              From <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }}</strong> 
                              To <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M, Y') }}</strong>
                          </p>
                      </div>

                      {{-- ===== Trial Balance Table ===== --}}
                      <div class="table-responsive">
                          <table class="table table-bordered table-striped table-sm">
                              <thead class="thead-dark">
                                  <tr class="text-center">
                                      <th style="width: 100px;">Acc Code</th>
                                      <th>Particulars</th>
                                      <th style="width: 150px;" class="text-right">Debit (Dr.)</th>
                                      <th style="width: 150px;" class="text-right">Credit (Cr.)</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @php $headIndex = 0; @endphp
                                  @foreach($orderedData as $accountHead => $subHeads)
                                      @php $headIndex++; @endphp
                                      
                                      {{-- ===== Main Account Head Row ===== --}}
                                      <tr class="bg-secondary text-white font-weight-bold">
                                          <td></td>
                                          <td>{{ strtoupper($accountHead) }}</td>
                                          <td class="text-right"></td>
                                          <td class="text-right"></td>
                                      </tr>

                                      @foreach($subHeads as $subHead => $sectionData)
                                          {{-- ===== Sub Account Head Row ===== --}}
                                          <tr class="bg-light font-weight-bold">
                                              <td></td>
                                              <td class="pl-3">{{ $subHead }}</td>
                                              <td class="text-right"></td>
                                              <td class="text-right"></td>
                                          </tr>

                                          {{-- ===== Individual Accounts ===== --}}
                                          @foreach($sectionData['accounts'] as $account)
                                          <tr>
                                              <td class="text-center">{{ $account['serial'] ?? '' }}</td>
                                              <td class="pl-4">{{ $account['account_name'] }}</td>
                                              <td class="text-right">
                                                  {{ $account['debit'] > 0 ? number_format($account['debit'], 2) : '' }}
                                              </td>
                                              <td class="text-right">
                                                  {{ $account['credit'] > 0 ? number_format($account['credit'], 2) : '' }}
                                              </td>
                                          </tr>
                                          @endforeach

                                          {{-- ===== Sub-Total Row ===== --}}
                                          <tr class="bg-info text-white font-weight-bold">
                                              <td colspan="2" class="text-right pl-4">Sub-Total {{ $subHead }}</td>
                                              <td class="text-right">
                                                  {{ $sectionData['subtotal_debit'] > 0 ? number_format($sectionData['subtotal_debit'], 2) : '' }}
                                              </td>
                                              <td class="text-right">
                                                  {{ $sectionData['subtotal_credit'] > 0 ? number_format($sectionData['subtotal_credit'], 2) : '' }}
                                              </td>
                                          </tr>
                                      @endforeach

                                      {{-- ===== Separator between sections ===== --}}
                                      @if($headIndex < count($orderedData))
                                      <tr>
                                          <td colspan="4" style="border:none; height: 8px; background: white;"></td>
                                      </tr>
                                      @endif
                                  @endforeach

                                  {{-- ===== No Data Message ===== --}}
                                  @if(empty($orderedData))
                                  <tr>
                                      <td colspan="4" class="text-center text-danger py-5">
                                          <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                                          <br>
                                          <strong>No transactions found from {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }}</strong>
                                      </td>
                                  </tr>
                                  @endif
                              </tbody>
                              
                              @if(!empty($orderedData))
                              <tfoot>
                                  {{-- Total Row --}}
                                  <tr class="bg-dark text-white font-weight-bold">
                                      <td colspan="2" class="text-right">TOTAL</td>
                                      <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                                      <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
                                  </tr>
                                  
                                  {{-- Difference Row --}}
                                  @if(abs($difference) > 0.01)
                                  <tr class="bg-danger text-white font-weight-bold">
                                      <td colspan="2" class="text-right">DIFFERENCE</td>
                                      <td colspan="2" class="text-center">
                                          {{ number_format(abs($difference), 2) }} 
                                          ({{ $difference > 0 ? 'Debit Side Higher' : 'Credit Side Higher' }})
                                      </td>
                                  </tr>
                                  @else
                                  <tr class="bg-success text-white font-weight-bold text-center">
                                      <td colspan="4">
                                          <i class="fas fa-check-circle mr-1"></i> TRIAL BALANCE IS BALANCED
                                      </td>
                                  </tr>
                                  @endif
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

@section('style')
<style>
    @media print {
        .no-print,
        .breadcrumb-area,
        .content-header,
        .main-header,
        .main-footer,
        .sidebar {
            display: none !important;
        }
        .content-wrapper {
            margin-left: 0 !important;
        }
        .print-area {
            margin: 0 !important;
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
        }
        .card-body {
            padding: 10px !important;
        }
        .table {
            font-size: 10px;
        }
        .table td, .table th {
            padding: 3px 6px !important;
        }
    }
</style>
@endsection