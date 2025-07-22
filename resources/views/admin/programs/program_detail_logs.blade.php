@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">

        <div class="card card-success">
          <div class="card-header">
            <h3 class="card-title">Today's Logs</h3>
          </div>
          <div class="card-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Sl</th>
                  <th>Performed By</th>
                  <th>Counter</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($todayLogs as $key => $log)
                <tr>
                  <td>{{ $key + 1 }}</td>
                  <td>{{ $log->first()['causer_name'] ?? '' }}</td>
                  <td>{{ $log->count() }}</td>
                  <td>
                    @php
                      $causerId = $log->first()['causer_id'] ?? null;
                      $afterChallanLogs = $log->filter(fn($item) => $item['headerid'] !== '-')->values();
                      $beforeChallanLogs = $log->filter(fn($item) => $item['headerid'] === '-')->values();
                      $deletedLogs = $todayDeletedLogs[$causerId] ?? collect();
                    @endphp

                    @if($afterChallanLogs->isNotEmpty())
                    <button class="btn btn-sm btn-primary show-changes-btn my-1"
                      data-logs='@json($afterChallanLogs)'
                      data-causer="{{ $log->first()['causer_name'] ?? '' }}"
                      data-type="after"
                      data-toggle="modal"
                      data-target="#changesModal">After Challan({{ $afterChallanLogs->count() }})</button>
                    @endif

                    @if($beforeChallanLogs->isNotEmpty())
                    <button class="btn btn-sm btn-secondary show-changes-btn my-1"
                      data-logs='@json($beforeChallanLogs)'
                      data-causer="{{ $log->first()['causer_name'] ?? '' }}"
                      data-type="before"
                      data-toggle="modal"
                      data-target="#changesModal">Before Challan({{ $beforeChallanLogs->count() }})</button>
                    @endif

                    @if($deletedLogs->isNotEmpty())
                    <button class="btn btn-sm btn-danger show-changes-btn my-1"
                      data-logs='@json($deletedLogs)'
                      data-causer="{{ $log->first()['causer_name'] ?? '' }}"
                      data-type="deleted"
                      data-toggle="modal"
                      data-target="#changesModal">Deleted</button>
                    @endif
                  </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">No logs for today</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <div class="card card-warning">
          <div class="card-header">
            <h3 class="card-title">Yesterday's Logs</h3>
          </div>
          <div class="card-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Sl</th>
                  <th>Performed By</th>
                  <th>Counter</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($yesterdayLogs as $key => $log)
                <tr>
                  <td>{{ $key + 1 }}</td>
                  <td>{{ $log->first()['causer_name'] ?? '' }}</td>
                  <td>{{ $log->count() }}</td>
                  <td>
                  @php
                    $causerId = $log->first()['causer_id'] ?? null;
                    $afterChallanLogs = $log->filter(fn($item) => $item['headerid'] !== '-')->values();
                    $beforeChallanLogs = $log->filter(fn($item) => $item['headerid'] === '-')->values();
                    $deletedLogs = $yesterdayDeletedLogs[$causerId] ?? collect();
                  @endphp

                    @if($afterChallanLogs->isNotEmpty())
                    <button class="btn btn-sm btn-primary show-changes-btn my-1"
                      data-logs='@json($afterChallanLogs)'
                      data-causer="{{ $log->first()['causer_name'] ?? '' }}"
                      data-type="after"
                      data-toggle="modal"
                      data-target="#changesModal">After Challan({{ $afterChallanLogs->count() }})</button>
                    @endif

                    @if($beforeChallanLogs->isNotEmpty())
                    <button class="btn btn-sm btn-secondary show-changes-btn my-1"
                      data-logs='@json($beforeChallanLogs)'
                      data-causer="{{ $log->first()['causer_name'] ?? '' }}"
                      data-type="before"
                      data-toggle="modal"
                      data-target="#changesModal">Before Challan({{ $beforeChallanLogs->count() }})</button>
                    @endif

                    @if($deletedLogs->isNotEmpty())
                    <button class="btn btn-sm btn-danger show-changes-btn my-1"
                      data-logs='@json($deletedLogs)'
                      data-causer="{{ $log->first()['causer_name'] ?? '' }}"
                      data-type="deleted"
                      data-toggle="modal"
                      data-target="#changesModal">Deleted</button>
                    @endif
                  </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">No logs for yesterday</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <div class="card card-success">
          <div class="card-header">
            <h3 class="card-title">Today's Challan Register Report</h3>
          </div>
          <div class="card-body">
            <table class="table table-bordered table-striped challan-receiving-register">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Vendor's Name</th>
                  <th>Sequence ID</th>
                  <th>Challan Receiving Register</th>
                  <th>Challan Posting Register</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody>
              @foreach($vsnumbersToday as $key => $data)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                  <td>{{ $data->vendor->name ?? '' }}</td>
                   <td>{{ $data->unique_id  ?? '' }}</td>
                  <td>{{ $data->qty ?? '' }}</td>
                  <td>{{ $data->programDetail->flatten()->count() }}</td>
                  <td>

                  </td>
                </tr>
              @endforeach

              

              </tbody>
            </table>
          </div>
        </div>

        <div class="card card-warning">
          <div class="card-header">
            <h3 class="card-title">Yesterday's Challan Register Report</h3>
          </div>
          <div class="card-body">
            <table class="table table-bordered table-striped challan-receiving-register">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Vendor's Name</th>
                  <th>Sequence ID</th>
                  <th>Challan Receiving Register</th>
                  <th>Challan Posting Register</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody>
              @foreach($vsnumbersYesterday as $key => $data)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                  <td>{{ $data->vendor->name ?? '' }}</td>
                   <td>{{ $data->unique_id  ?? '' }}</td>
                  <td>{{ $data->qty ?? '' }}</td>
                  <td>{{ $data->programDetail->flatten()->count() }}</td>
                  <td>

                  </td>
                </tr>
              @endforeach

              

              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- Changes Modal -->
<div class="modal fade" id="changesModal" tabindex="-1" role="dialog" aria-labelledby="changesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changesModalLabel">
          <small class="ml-3 text-muted" id="changedByInfo"></small>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="changesTable">
          <thead>
            <tr id="modalHeaderRow">
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
  $('.challan-receiving-register').DataTable({
    dom: 'Bfrtip',
    buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
    ]
  });
  $(document).ready(function () {
    $('.show-changes-btn').on('click', function () {
      const logs = $(this).data('logs');
      const causer = $(this).data('causer');
      const type = $(this).data('type');

      $('#changedByInfo').text(`Performed By: ${causer} (${type.charAt(0).toUpperCase() + type.slice(1)} Logs)`);

      const thead = $('#modalHeaderRow');
      const tbody = $('#changesTable tbody');
      tbody.empty();
      thead.empty();

      if(type === 'after'){
        thead.append('<th>Header ID</th><th>Dest Qty</th><th>Challan Number</th>');
        logs.forEach(log => {
          if (log.headerid === '-' && log.dest_qty === '-' && log.challan_no === '-') return;
          tbody.append(`
            <tr>
              <td>${log.headerid}</td>
              <td>${log.dest_qty}</td>
              <td>${log.challan_no}</td>
            </tr>
          `);
        });
      } else if(type === 'before') {
        thead.append('<th>Challan Number</th>');
        logs.forEach(log => {
          if (!log.challan_no || log.challan_no === '-') return;
          tbody.append(`
            <tr>
              <td>${log.challan_no}</td>
            </tr>
          `);
        });
      } else if(type === 'deleted') {
        thead.append('<th>Header ID</th><th>Dest Qty</th><th>Challan Number</th>');
        logs.forEach(log => {
          tbody.append(`
            <tr>
              <td>${log.headerid}</td>
              <td>${log.dest_qty}</td>
              <td>${log.challan_no}</td>
            </tr>
          `);
        });
      }

      if (tbody.children().length === 0) {
        tbody.append('<tr><td class="text-center" colspan="'+ (type === 'after' ? 3 : 1) +'">No relevant changes found</td></tr>');
      }
    });

    $('#changesModal').on('hidden.bs.modal', function () {
      $('#changedByInfo').text('');
      $('#changesTable tbody').empty();
      $('#modalHeaderRow').empty();
    });
  });
</script>
@endsection