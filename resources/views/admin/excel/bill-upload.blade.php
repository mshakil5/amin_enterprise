@extends('admin.layouts.admin')

@section('content')
    <section class="content pt-4">
        <div class="container-fluid">
            <div class="card shadow-sm col-md-6 mx-auto border-secondary">
                <div class="card-header d-flex justify-content-between align-items-center"
                    style="background-color:#28a745; color:#fff;">
                    <h3 class="card-title mb-0">Upload Excel for Bill Update</h3>
                    <a href="{{ route('excel.template') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-file-excel"></i> Download Template
                    </a>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Please upload an Excel file with columns: <strong>Date, Mother Vessel Name,
                            Bill Number</strong>.</p>
                    <form action="{{ route('excel.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="file" class="form-label">Select Excel File</label>
                            <input type="file" name="file" id="file" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </form>
                </div>

                <div class="card-footer text-center text-muted">
                    Only .xlsx, .xls, or .csv files allowed (max 20MB)
                </div>
            </div>
        </div>
    </section>
@endsection