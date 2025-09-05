@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
  <div class="container-fluid">
      <div class="page-header d-flex justify-content-between align-items-center">
          <a href="{{ url()->previous() }}" class="btn btn-secondary mb-2">Back</a>
          <button onclick="window.print();" class="btn btn-info mb-2">Print</button>
      </div>

      <div class="row print-area">
          <div class="col-12">
              <div class="card card-secondary">
                  <div class="card-body">

                      {{-- ===== Header ===== --}}
                      <h2 class="text-center mb-0">Amin Enterprise - BSRM Program</h2>
                      <h4 class="text-center mb-0">Trial Balance</h4>
                      <p class="text-center">As on {{ now()->format('d M, Y') }}</p>

                      {{-- ===== Trial Balance Table ===== --}}
                      <div class="table-responsive mt-3">
                          <table class="table table-bordered table-striped">
                              <thead class="thead-dark text-center">
                                  <tr>
                                      <th style="width: 120px;">Acc Code</th>
                                      <th>Particulars</th>
                                      <th style="width: 150px;">Debit (Dr.)</th>
                                      <th style="width: 150px;">Credit (Cr.)</th>
                                  </tr>
                              </thead>
                              <tbody>

                                  {{-- ===== Assets Section ===== --}}
                                  <tr class="table-secondary">
                                      <td></td>
                                      <td class="text-bold">ASSETS</td>
                                      <td></td>
                                      <td></td>
                                  </tr>

                                  {{-- Fixed Assets --}}
                                  <tr class="table-light">
                                      <td></td>
                                      <td class="text-bold pl-3">Fixed Assets</td>
                                      <td></td>
                                      <td></td>
                                  </tr>
                                  @foreach ($fixedAssets as $fixedAsset)
                                    <tr>
                                        <td>
                                            {{$fixedAsset->serial ?? "" }}
                                        </td>
                                        <td class="pl-5">{{$fixedAsset->account_name ?? "" }}</td>
                                        <td class="text-right">{{ number_format($fixedAsset->net, 2)}}</td>
                                        <td class="text-right"></td>
                                    </tr>
                                  @endforeach
                                  
                                  

                                  {{-- Current Assets --}}
                                  <tr class="table-light">
                                      <td></td>
                                      <td class="text-bold pl-3">Current Assets</td>
                                      <td></td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td>103</td>
                                      <td class="pl-5">Cash</td>
                                      <td>50,000</td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td>104</td>
                                      <td class="pl-5">Bank</td>
                                      <td>100,000</td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td>105</td>
                                      <td class="pl-5">Receivables from BSRM</td>
                                      <td>2,529,680</td>
                                      <td></td>
                                  </tr>

                                  {{-- ===== Liabilities Section ===== --}}
                                  <tr class="table-secondary">
                                      <td></td>
                                      <td class="text-bold">LIABILITIES</td>
                                      <td></td>
                                      <td></td>
                                  </tr>

                                  {{-- Long-Term Liabilities --}}
                                  <tr class="table-light">
                                      <td></td>
                                      <td class="text-bold pl-3">Long-Term Liabilities</td>
                                      <td></td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td>201</td>
                                      <td class="pl-5">Bank Loan</td>
                                      <td></td>
                                      <td>500,000</td>
                                  </tr>

                                  {{-- Short-Term Liabilities --}}
                                  <tr class="table-light">
                                      <td></td>
                                      <td class="text-bold pl-3">Short-Term Liabilities</td>
                                      <td></td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td>202</td>
                                      <td class="pl-5">Accounts Payable</td>
                                      <td></td>
                                      <td>200,000</td>
                                  </tr>

                                  {{-- ===== Equity Section ===== --}}
                                  <tr class="table-secondary">
                                      <td></td>
                                      <td class="text-bold">EQUITY / CAPITAL</td>
                                      <td></td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td>301</td>
                                      <td class="pl-5">Owner’s Equity</td>
                                      <td></td>
                                      <td>1,000,000</td>
                                  </tr>

                                  {{-- ===== Income Section ===== --}}
                                  <tr class="table-secondary">
                                      <td></td>
                                      <td class="text-bold">INCOME / REVENUE</td>
                                      <td></td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td>401</td>
                                      <td class="pl-5">Sales Revenue</td>
                                      <td></td>
                                      <td>750,000</td>
                                  </tr>

                                  {{-- ===== Expenses Section ===== --}}
                                  <tr class="table-secondary">
                                      <td></td>
                                      <td class="text-bold">EXPENSES</td>
                                      <td></td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td>501</td>
                                      <td class="pl-5">Operating Expenses</td>
                                      <td>100,000</td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td>502</td>
                                      <td class="pl-5">Salaries & Wages</td>
                                      <td>150,000</td>
                                      <td></td>
                                  </tr>

                              </tbody>
                              <tfoot>
                                  <tr class="table-dark text-bold text-center">
                                      <td colspan="2" class="text-right">TOTAL</td>
                                      <td>4,129,680</td>
                                      <td>2,450,000</td>
                                  </tr>
                              </tfoot>
                          </table>
                      </div>

                  </div>
              </div>
          </div>
      </div>
  </div>
</section>

@endsection

@section('script')
@endsection
