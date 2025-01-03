@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content mt-3" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <a href="{{route('admin.programDetail', $programId)}}" class="btn btn-secondary my-3">Back</a>
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Vendor Ledger</h3>
                        
                        <div class="card-tools">
                            <a href="{{ route('export.template') }}" class="btn btn-tool">
                                <i class="fas fa-envelope"></i>
                            </a>
                      </div>
                    </div>
                    
                    <div class="card-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form action="{{route('billGeneratingStore')}}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="upload">Uploads </label>
                                            <input type="file" name="file" required>
                                            <input type="hidden" name="programId" value="{{$programId}}" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Action</label> <br>
                                            <button type="submit" class="btn btn-secondary">Upload</button>
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


@endsection

@section('script')
<script>
    setTimeout(function() {
        let alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 3000);
</script>
@endsection
