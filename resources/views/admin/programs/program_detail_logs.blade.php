@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">

        {{-- Last 7 Days Logs --}}
        <div class="card card-success">
          <div class="card-header">
            <h3 class="card-title">
              Last 7 Days' Logs
              <small class="d-block mt-1">
                ({{ optional($start)->format('d M Y') }} – {{ optional($end)->format('d M Y') }})
              </small>
            </h3>
          </div>
          <div class="card-body">
            @php
              // Ensure descending date order (newest first)
              $orderedLogsByDate = collect($logsByDate ?? [])->sortKeysDesc();
            @endphp

            @forelse ($orderedLogsByDate as $date => $groupsByCauser)
              <div class="mb-3 p-2 bg-light rounded border">
                <h5 class="mb-2">{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</h5>

                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="width:60px;">Sl</th>
                      <th>Performed By</th>
                      <th style="width:120px;">Counter</th>
                      <th style="width:420px;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($groupsByCauser as $causerId => $log)
                      @php
                        $causerName        = $log->first()['causer_name'] ?? 'Unknown';
                        $afterChallanLogs  = $log->filter(fn($item) => $item['headerid'] !== '-')->values();
                        $beforeChallanLogs = $log->filter(fn($item) => $item['headerid'] === '-')->values();
                        $deletedLogsGroup  = ($deletedLogsByDate[$date][$causerId] ?? collect())->values();
                      @endphp
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $causerName }}</td>
                        <td>{{ $log->count() }}</td>
                        <td>
                          @if($afterChallanLogs->isNotEmpty())
                            <button class="btn btn-sm btn-primary show-changes-btn my-1"
                              data-logs='@json($afterChallanLogs)'
                              data-causer="{{ $causerName }}"
                              data-type="after"
                              data-date="{{ $date }}"
                              data-toggle="modal"
                              data-target="#changesModal">After Challan ({{ $afterChallanLogs->count() }})</button>
                          @endif

                          @if($beforeChallanLogs->isNotEmpty())
                            <button class="btn btn-sm btn-secondary show-changes-btn my-1"
                              data-logs='@json($beforeChallanLogs)'
                              data-causer="{{ $causerName }}"
                              data-type="before"
                              data-date="{{ $date }}"
                              data-toggle="modal"
                              data-target="#changesModal">Before Challan ({{ $beforeChallanLogs->count() }})</button>
                          @endif

                          @if($deletedLogsGroup->isNotEmpty())
                            <button class="btn btn-sm btn-danger show-changes-btn my-1"
                              data-logs='@json($deletedLogsGroup)'
                              data-causer="{{ $causerName }}"
                              data-type="deleted"
                              data-date="{{ $date }}"
                              data-toggle="modal"
                              data-target="#changesModal">Deleted ({{ $deletedLogsGroup->count() }})</button>
                          @endif
                        </td>
                      </tr>
                    @empty
                      <tr><td colspan="4" class="text-center">No logs for this date</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            @empty
              <div class="alert alert-info mb-0">No logs found for the last 7 days.</div>
            @endforelse
          </div>
        </div>

        {{-- Challan Register Report - Last 7 Days --}}
        <div class="card card-info">
          <div class="card-header">
            <h3 class="card-title">Challan Register Report (Last 7 Days)</h3>
          </div>
          <div class="card-body">
            <table class="table table-bordered table-striped challan-receiving-register w-100">
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
              @forelse($vsnumbersLast7Days as $row)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }}</td>
                  <td>{{ $row->vendor->name ?? '' }}</td>
                  <td>{{ $row->unique_id ?? '' }}</td>
                  <td>{{ $row->qty ?? '' }}</td>
                  <td>{{ $row->programDetail->flatten()->count() }}</td>
                  <td></td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center">No data</td></tr>
              @endforelse
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="3" class="text-right">Total:</th>
                  <th>{{ $vsnumbersLast7Days->sum('qty') }}</th>
                  <th>{{ $vsnumbersLast7Days->sum(fn($i) => $i->programDetail->flatten()->count()) }}</th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

{{-- Changes Modal --}}
<div class="modal fade" id="changesModal" tabindex="-1" role="dialog" aria-labelledby="changesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <span id="changesModalTitle"></span>
          <small class="ml-3 text-muted" id="changedByInfo"></small>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="changesTable">
          <thead>
            <tr id="modalHeaderRow"></tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Single Log Details Modal --}}
<div class="modal fade" id="logDetailModal" tabindex="-1" role="dialog" aria-labelledby="logDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logDetailModalLabel">Log Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tbody id="logDetailBody"></tbody>
        </table>
        <h6 class="mt-3">Properties</h6>
        <pre id="logDetailProperties" class="bg-light p-2 rounded" style="white-space: pre-wrap;"></pre>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script>
  // DataTable only for the challan register table
  $('.challan-receiving-register').DataTable({
    dom: 'Bfrtip',
    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
  });

  // Global in-memory map for the current modal's logs (id -> log object)
  window.__modalLogs = {};

  // Open "Changes" modal and build its table
  $(document).on('click', '.show-changes-btn', function () {
    const logs   = $(this).data('logs') || [];
    const causer = $(this).data('causer') || 'Unknown';
    const type   = $(this).data('type') || '';
    const date   = $(this).data('date') || '';

    // Rebuild map
    window.__modalLogs = {};
    logs.forEach(l => { if (l && l.id) { window.__modalLogs[l.id] = l; } });

    // Header text
    $('#changesModalTitle').text(type === 'after' ? 'After Challan Logs'
                                : type === 'before' ? 'Before Challan Logs'
                                : type === 'deleted' ? 'Deleted Logs'
                                : 'Logs');
    $('#changedByInfo').text(`Date: ${date} | Performed By: ${causer}`);

    // Prepare table
    const thead = $('#modalHeaderRow');
    const tbody = $('#changesTable tbody');
    tbody.empty();
    thead.empty();

    // Build columns based on type + an Action column
    if (type === 'after') {
      thead.append('<th>Header ID</th><th>Dest Qty</th><th>Challan Number</th><th>Action</th>');
      logs.forEach(log => {
        // Skip if fully blank
        if (log.headerid === '-' && log.dest_qty === '-' && log.challan_no === '-') return;
        tbody.append(`
          <tr>
            <td>${log.headerid}</td>
            <td>${log.dest_qty}</td>
            <td>${log.challan_no}</td>
            <td><button class="btn btn-xs btn-outline-dark show-log-details" data-id="${log.id}">Details</button></td>
          </tr>
        `);
      });
    } else if (type === 'before') {
      thead.append('<th>Challan Number</th><th>Action</th>');
      logs.forEach(log => {
        if (!log.challan_no || log.challan_no === '-') return;
        tbody.append(`
          <tr>
            <td>${log.challan_no}</td>
            <td><button class="btn btn-xs btn-outline-dark show-log-details" data-id="${log.id}">Details</button></td>
          </tr>
        `);
      });
    } else if (type === 'deleted') {
      thead.append('<th>Header ID</th><th>Dest Qty</th><th>Challan Number</th><th>Action</th>');
      logs.forEach(log => {
        tbody.append(`
          <tr>
            <td>${log.headerid ?? '-'}</td>
            <td>${log.dest_qty ?? '-'}</td>
            <td>${log.challan_no ?? '-'}</td>
            <td><button class="btn btn-xs btn-outline-dark show-log-details" data-id="${log.id}">Details</button></td>
          </tr>
        `);
      });
    }

    // Empty state
    const colSpan = (type === 'after' || type === 'deleted') ? 4 : 2;
    if (tbody.children().length === 0) {
      tbody.append(`<tr><td class="text-center" colspan="${colSpan}">No relevant changes found</td></tr>`);
    }
  });

  // Reset modal on hide
  $('#changesModal').on('hidden.bs.modal', function () {
    $('#changesModalTitle').text('');
    $('#changedByInfo').text('');
    $('#changesTable tbody').empty();
    $('#modalHeaderRow').empty();
    window.__modalLogs = {};
  });

  // Show single log Details modal
  $(document).on('click', '.show-log-details', function () {
    const id  = $(this).data('id');
    const log = window.__modalLogs[id];

    if (!log) return;

    const tbody = $('#logDetailBody').empty();

    const rows = [
      ['Log ID', log.id],
      ['Event', log.event || '-'],
      ['Description', log.description || '-'],
      ['Date/Time', log.created_at || '-'],
      ['Causer', log.causer_name || 'Unknown'],
      ['Subject ID', (log.subject_id ?? '-')],
      ['Header ID', (log.headerid ?? '-')],
      ['Dest Qty', (log.dest_qty ?? '-')],
      ['Challan No', (log.challan_no ?? '-')],
    ];

    rows.forEach(([k, v]) => {
      tbody.append(`<tr><th style="width:180px;">${k}</th><td>${(v === null || v === undefined || v === '') ? '-' : v}</td></tr>`);
    });

    const propsText = (() => {
      try { return JSON.stringify(log.properties || {}, null, 2); }
      catch (e) { return '-'; }
    })();

    $('#logDetailProperties').text(propsText);
    $('#logDetailModal').modal('show');
  });
</script>
@endsection
