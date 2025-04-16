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
                                  @php
                                      $total_dr = 0;
                                      $total_cr = 0;
                                  @endphp
                                  <tr>
                                      <td>101</td>
                                      <td>Vendor 1</td>
                                      <td>5000</td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td>102</td>
                                      <td>Vendor 2</td>
                                      <td></td>
                                      <td>2000</td>
                                  </tr>
                                  <tr>
                                      <td>103</td>
                                      <td>Vendor 3</td>
                                      <td>3000</td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td>104</td>
                                      <td>Vendor 4</td>
                                      <td></td>
                                      <td>4000</td>
                                  </tr>

                                  @php
                                      $total_dr = 5000 + 3000;
                                      $total_cr = 2000 + 4000;
                                  @endphp

                                  <tr>
                                      <td colspan="2"><strong>Total</strong></td>
                                      <td><strong>{{ $total_dr }}</strong></td>
                                      <td><strong>{{ $total_cr }}</strong></td>
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