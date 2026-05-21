@extends('admin.layouts.admin')

@section('content')

<style>
    .form-checkbox {
        font-family: system-ui, sans-serif;
        font-size: 2rem;
        font-weight: bold;
        line-height: 1.1;
        display: grid;
        grid-template-columns: 1em auto;
        gap: 0.5em;
    }
    .custom-checkbox { height: 30px; }
</style>

<!-- Back Button Section -->
<section class="content mt-3" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <a href="{{ route('admin.programDetail', $programId) }}" class="btn btn-secondary my-3">Back</a>
            </div>
        </div>
    </div>
</section>

<!-- Add Document Form Section -->
<section class="content pt-1" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-8">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Add Vendor Document</h3>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.programVendorDocuments.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="program_id" value="{{ $programId }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Vendor</label>
                                        <select name="vendor_id" class="form-control select2" required>
                                            <option value="">Select Vendor</option>
                                            {{-- Populate this from your Vendor model --}}
                                            @foreach(\App\Models\Vendor::all() as $vendor)
                                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Document/File</label>
                                        <input type="file" name="document" class="form-control-file" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <input type="date" name="date" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Total Truck</label>
                                        <input type="number" name="total_truck" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Total Challan</label>
                                        <input type="number" name="total_challan" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Truck Numbers (comma separated)</label>
                                        <input type="text" name="truck_numbers" class="form-control" placeholder="e.g. DHK-123, CHT-456">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mother Vessel</label>
                                        <select name="mother_vassel_id" class="form-control select2">
                                            <option value="">Select Mother Vessel</option>
                                            @foreach(\App\Models\MotherVassel::all() as $vessel)
                                                <option value="{{ $vessel->id }}">{{ $vessel->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Client</label>
                                        <select name="client_id" class="form-control select2">
                                            <option value="">Select Client</option>
                                            @foreach(\App\Models\Client::all() as $client)
                                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary float-right">Submit Document</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Documents Table Section -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Submitted Documents for Program #{{ $programId }}</h3>
                    </div>
                    <div class="card-body">
                        <div style="overflow-x:auto;">
                            <table id="documentsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Date</th>
                                        <th>Vendor</th>
                                        <th>Document</th>
                                        <th>Total Truck</th>
                                        <th>Total Challan</th>
                                        <th>Truck Numbers</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documents as $key => $doc)
                                        <tr>
                                            <td style="text-align: center">{{ $key + 1 }}</td>
                                            <td style="text-align: center">{{ \Carbon\Carbon::parse($doc->date)->format('d/m/Y') }}</td>
                                            <td style="text-align: center">{{ $doc->vendor->name ?? 'N/A' }}</td>
                                            <td style="text-align: center">
                                                @if($doc->document)
                                                    <a href="{{ asset($doc->document) }}" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td style="text-align: center">{{ $doc->total_truck }}</td>
                                            <td style="text-align: center">{{ $doc->total_challan }}</td>
                                            <td style="text-align: center">{{ $doc->truck_numbers }}</td>

                                            
                                            <td style="text-align: center">
                                                <!-- Updated Edit Button -->
                                                <button type="button" class="btn btn-sm btn-warning editBtn" 
                                                        data-url="{{ route('admin.programVendorDocuments.edit', $doc->id) }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>

                                                <form action="{{ route('admin.programVendorDocuments.delete', $doc->id) }}" method="GET" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                                                    {{-- <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button> --}}
                                                </form>
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
    </div>
</section>

<!-- Edit Document Modal -->
<div class="modal fade" id="editDocumentModal" tabindex="-1" role="dialog" aria-labelledby="editDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editDocumentModalLabel">Edit Vendor Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.programVendorDocuments.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="program_id" value="{{ $programId }}">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Vendor</label>
                                <select name="vendor_id" id="edit_vendor_id" class="form-control select2" required>
                                    <option value="">Select Vendor</option>
                                    @foreach(\App\Models\Vendor::all() as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Current Document</label>
                                <input type="file" name="document" id="edit_document" class="form-control-file">
                                <small id="edit_current_doc" class="text-muted"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="date" id="edit_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Truck</label>
                                <input type="number" name="total_truck" id="edit_total_truck" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Challan</label>
                                <input type="number" name="total_challan" id="edit_total_challan" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Truck Numbers (comma separated)</label>
                                <input type="text" name="truck_numbers" id="edit_truck_numbers" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mother Vessel</label>
                                <select name="mother_vassel_id" id="edit_mother_vassel_id" class="form-control select2">
                                    <option value="">Select Mother Vessel</option>
                                    @foreach(\App\Models\MotherVassel::all() as $vessel)
                                        <option value="{{ $vessel->id }}">{{ $vessel->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Client</label>
                                <select name="client_id" id="edit_client_id" class="form-control select2">
                                    <option value="">Select Client</option>
                                    @foreach(\App\Models\Client::all() as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Document</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@section('script')
<script>
    $(document).ready(function () {
        // Initialize DataTable
        $("#documentsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 10,
            "buttons": ["copy", "csv", "excel", "pdf", "print"],
            "lengthMenu": [[20, 100, -1, 50, 25], [20, 100, "All", 50, 25]]
        }).buttons().container().appendTo('#documentsTable_wrapper .col-md-6:eq(0)');



        // Handle Edit Button Click
        $(document).on('click', '.editBtn', function() {
            var url = $(this).data('url'); // Gets the route URL
            
            $.get(url, function(data) {
                // Populate the modal fields with the JSON data
                $('#edit_id').val(data.id);
                $('#edit_vendor_id').val(data.vendor_id).trigger('change'); // .trigger('change') needed for Select2
                $('#edit_date').val(data.date);
                $('#edit_total_truck').val(data.total_truck);
                $('#edit_total_challan').val(data.total_challan);
                $('#edit_truck_numbers').val(data.truck_numbers);
                $('#edit_mother_vassel_id').val(data.mother_vassel_id).trigger('change');
                $('#edit_client_id').val(data.client_id).trigger('change');
                
                // Show current document name (optional)
                if(data.document) {
                    var fileName = data.document.split('/').pop();
                    $('#edit_current_doc').text('Current: ' + fileName);
                } else {
                    $('#edit_current_doc').text('No document uploaded');
                }

                // Clear the file input field (you can't pre-fill a file input for security reasons)
                $('#edit_document').val('');

                // Show the modal
                $('#editDocumentModal').modal('show');
            }).fail(function() {
                alert('Error fetching document data');
            });
        });


        // Auto-remove alerts after 3 seconds
        setTimeout(function () {
            let alert = document.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 3000);
    });
</script>
@endsection