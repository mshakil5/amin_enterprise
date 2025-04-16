@extends('admin.layouts.admin')

@section('content')

@if (!isset($motherVassel))
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Trial Balance</h3>
                    </div>
                              
                    <div class="card-body">
                      <div class="ermsg"> </div>
                      <form action="{{ route('admin.trialBalance') }}" method="POST">
                          @csrf
                  
                          <div class="form-row">
                              <div class="form-group col-md-6">
                                  <label for="mv_id">Mother Vassel</label>
                                  <select name="mv_id" id="mv_id" class="form-control select2">
                                      <option value="">Select</option>
                                      @foreach ($mvassels as $mvassel)
                                          <option value="{{ $mvassel->id }}" {{ isset($selectedId) && $selectedId == $mvassel->id ? 'selected' : '' }}>
                                              {{ $mvassel->name }}
                                          </option>
                                      @endforeach
                                  </select>
                              </div>
                  
                              <div class="form-group col-md-6 d-flex align-items-end">
                                  <button type="submit" class="btn btn-secondary">Check</button>
                              </div>
                          </div>
                      </form>
                    </div>
                  
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@if (isset($motherVassel))

<section class="content pt-3" id="contentContainer">
  <div class="container-fluid">
      <div class="page-header d-flex justify-content-between">
          <a href="{{ url()->previous() }}" class="btn btn-secondary mb-2">Back</a>
          <button onclick="window.print();" class="btn btn-info mb-2">Print</button>
      </div>

      <div class="row print-area">
          <div class="col-12">
              <div class="card card-secondary">

                  <div class="card-body">
                      <h1 class="text-center">Amin Enterprise-BSRM Program</h1>
                      <h3 class="text-center">Trial Balance</h3>
                      <h3 class="text-center">Mother Vessel-{{ $motherVassel->name }}</h3>
                      <div class="card card-primary mt-3">
                          <table class="table table-bordered table-hover">
                              <thead>
                                  <tr>
                                      <th>Acc Code</th>
                                      <th>Particulars</th>
                                      <th>Dr.</th>
                                      <th>Cr.</th>
                                  </tr>
                              </thead>
                              <tbody>

                                  <tr>
                                      <td></td>
                                      <td class="text-bold">Openings</td>
                                      <td></td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td></td>
                                      <td>Cash</td>
                                      <td></td>
                                      <td>0</td>
                                  </tr>
                                  <tr>
                                      <td></td>
                                      <td>Bank</td>
                                      <td></td>
                                      <td>0</td>
                                  </tr>
                                  <tr>
                                      <td></td>
                                      <td class="text-bold">NON CURRENT ASSETS</td>
                                      <td></td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td></td>
                                      <td class="text-bold">INVESTMENTS</td>
                                      <td></td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td></td>
                                      <td class="text-bold">CURRENT ASSETS</td>
                                      <td></td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td></td>
                                      <td>Receivables from BSRM</td>
                                      <td>2529680</td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                  </tr>

                                  <tr>
                                      <td></td>
                                      <td>EXPENSES</td>
                                      <td></td>
                                      <td></td>
                                  </tr>

                                  @foreach ($expenses as $expense)
                                    <tr>
                                        <td></td>
                                        <td>{{ $expense->chartOfAccount->account_name }}</td>
                                        <td></td>
                                        <td>{{ $expense->amount }}</td>
                                    </tr>
                                  @endforeach

                                  <tr>
                                      <td></td>
                                      <td></td>
                                      <td><strong> 50000 </strong></td>
                                      <td><strong>10000</strong></td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>

<style>
  @media print {
      body * {
          visibility: hidden;
      }
      .print-area, .print-area * {
          visibility: visible;
      }
      .print-area {
          position: absolute;
          left: 0;
          top: 0;
          width: 100%;
      }
  
      .no-print {
          display: none !important;
      }
  
      body {
          -webkit-print-color-adjust: exact;
          print-color-adjust: exact;
          font-size: 12px;
      }
  }
  </style>
    
@endif

@endsection

@section('script')

@endsection