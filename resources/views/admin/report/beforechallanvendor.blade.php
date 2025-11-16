@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-8">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">After challan receive posting program</h3>
                    </div>
                    
                    
                    <div class="card-body">
                        <div class="ermsg"> </div>
                        <form action="{{route('challanPostingVendorReportshow')}}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label for="mv_id">Mother Vassel </label>
                                            <select name="mv_id" id="mv_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($mvassels as $mvassel)
                                              <option value="{{$mvassel->id}}"  @if ($mvassel->id == $mid) selected @endif>{{$mvassel->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-3">
                                            <label for="ghat_id">Ghat </label>
                                            <select name="ghat_id" id="ghat_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach (\App\Models\Ghat::where('status', 1)->orderby('id', 'DESC')->get() as $ghat)
                                              <option value="{{$ghat->id}}">{{$ghat->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>


                                        <div class="form-group col-md-6">
                                            <label>Action</label> <br>
                                            <button type="submit" class="btn btn-secondary">Check</button>
                                        </div>
                                        
                                    </div>
                                </div>

                            </div>
                            
                        </form>
                    </div>
                    <div class="card-footer"> </div>
                </div>
            </div>
        </div>
    </div>
</section>


@if (isset($data))

<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Mother Vassel wise challan-vendor statement</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              @php
              if ($mid) {
                $mvesselName = \App\Models\MotherVassel::where('id', $mid)->first()->name;        
              }
              @endphp

              <h3 class="text-center">{{$mvesselName}}</h3>
              


              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Vendor</th>
                  <th>Total Challan</th>
                  <th>Challan Received</th>
                  <th>Challan Not Received</th>
                  <th>Notes</th>
                </tr>
                </thead>
                @php
                    $total_challan = 0;
                    $total_challan_received = 0;
                    $total_challan_not_received = 0;
                @endphp
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">{{$data->vendor->name}}</td>
                    <td style="text-align: center"><a href="{{route('challanPostingReport',['mid'=>$mid, 'vid' => $data->vendor->id])}}" class="btn btn-xs btn-success">{{$data->total_records}}</a></td>
                    <td style="text-align: center">{{$data->challan_received}}</td>
                    <td style="text-align: center">{{$data->challan_not_received}}</td>
                    <td>
                        <!-- Add Note Button -->
                        <button type="button" class="btn btn-success btn-xs" 
                                data-toggle="modal" 
                                data-target="#addNoteModal{{ $data->program_id }}_{{ $data->vendor_id }}">
                            + Add
                        </button>

                        <!-- View Notes Button -->
                        <button type="button" class="btn btn-info btn-xs" 
                                data-toggle="modal" 
                                data-target="#viewNoteModal{{ $data->program_id }}_{{ $data->vendor_id }}">
                            View
                        </button>

                        <!-- Add Note Modal -->
                        <div class="modal fade" id="addNoteModal{{ $data->program_id }}_{{ $data->vendor_id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add Note</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <form class="addNoteForm">
                                        @csrf
                                        <input type="hidden" name="program_id" value="{{ $data->program_id }}">
                                        <input type="hidden" name="vendor_id" value="{{ $data->vendor_id }}">

                                        <div class="modal-body">
                                            <div class="ermsg"></div>
                                            <div class="form-group">
                                                <label style="text-align:left; display:block;">Date</label>
                                                <input type="date" class="form-control" name="date" required value="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="form-group">
                                                <label style="text-align:left; display:block;">Note</label>
                                                <textarea class="form-control" name="note" rows="3" required></textarea>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-warning">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- View Notes Modal -->
                        <div class="modal fade" id="viewNoteModal{{ $data->program_id }}_{{ $data->vendor_id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Existing Notes</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <div class="modal-body">
                                        @php
                                            $notes = \App\Models\ReportNote::where('program_id', $data->program_id)
                                                        ->where('vendor_id', $data->vendor_id)
                                                        ->orderBy('date','DESC')
                                                        ->get();
                                        @endphp

                                        @if($notes->count() > 0)
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Note</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($notes as $note)
                                                        <tr>
                                                            <td>{{ $note->date }}</td>
                                                            <td>{{ $note->note }}</td>
                                                            <td>
                                                                <!-- Edit Note Button -->
                                                                <button type="button" class="btn btn-sm btn-primary" 
                                                                        data-toggle="modal" 
                                                                        data-target="#editNoteModal{{ $note->id }}">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>

                                                                <!-- Edit Note Modal -->
                                                                <div class="modal fade" id="editNoteModal{{ $note->id }}" tabindex="-1" aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title">Edit Note</h5>
                                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                            </div>
                                                                            <form class="editNoteForm" data-id="{{ $note->id }}">
                                                                                @csrf
                                                                                <input type="hidden" name="program_id" value="{{ $note->program_id }}">
                                                                                <input type="hidden" name="vendor_id" value="{{ $note->vendor_id }}">

                                                                                <div class="modal-body">
                                                                                    <div class="ermsg"></div>
                                                                                    <div class="form-group">
                                                                                        <label style="text-align:left; display:block;">Date</label>
                                                                                        <input type="date" class="form-control" name="date" required value="{{ $note->date }}">
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label style="text-align:left; display:block;">Note</label>
                                                                                        <textarea class="form-control" name="note" rows="3" required>{{ $note->note }}</textarea>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                                                    <button type="submit" class="btn btn-warning">Update</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p>No notes found.</p>
                                        @endif
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </td>
                  </tr>
                  @php
                      $total_challan += $data->total_records;
                      $total_challan_received += $data->challan_received;
                      $total_challan_not_received += $data->challan_not_received;
                  @endphp
                  @endforeach
                
                </tbody>

                <tfoot>
                  <tr>
                    <th style="text-align: center"></th>
                    <th style="text-align: center">Total: </th>
                    <th style="text-align: center">{{$total_challan}}</th>
                    <th style="text-align: center">{{$total_challan_received}}</th>
                    <th style="text-align: center">{{$total_challan_not_received}}</th>
                  </tr>
                </tfoot>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
    
@endif



@endsection

@section('script')

<script>
    // $(function () {
    //   $("#example1").DataTable({
    //     "responsive": true,
    //     "autoWidth": false,
    //   });
    // });


</script>

<script>
$(document).ready(function(){

    // Add Note
    $('.addNoteForm').on('submit', function(e){
        e.preventDefault();
        let form = $(this);
        $.ajax({
            url: "{{ route('reportNotes.store') }}",
            method: "POST",
            data: form.serialize(),
            success: function(d){
                form.find('.ermsg').html(d.message);
                if(d.status == 300){
                    window.setTimeout(()=> location.reload(), 2000);
                }
            },
            error: function(err){
                form.find('.ermsg').html("<div class='alert alert-danger'>Something went wrong!</div>");
            }
        });
    });

    // Edit Note
    $('.editNoteForm').on('submit', function(e){
        e.preventDefault();
        let form = $(this);
        let noteId = form.data('id');
        $.ajax({
            url: '/admin/report-notes/'+noteId,
            method: "PUT",
            data: form.serialize(),
            success: function(d){
                form.find('.ermsg').html(d.message);
                if(d.status == 300){
                    window.setTimeout(()=> location.reload(), 2000);
                }
            },
            error: function(err){
                form.find('.ermsg').html("<div class='alert alert-danger'>Something went wrong!</div>");
            }
        });
    });

});
</script>

@endsection
