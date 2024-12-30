

<div class="modal fade" id="vendorModal" tabindex="-1" role="dialog" aria-labelledby="vendorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vendorModalLabel">Add Vendor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="vendorForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                          <div class="form-group">
                            <label>Name*</label>
                            <input type="text" class="form-control" id="name" name="name">
                          </div>
                        </div>
    
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label>Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                          </div>
                        </div>
    
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                          </div>
                        </div>
    
                        
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label>Address</label>
                            <input type="text" class="form-control" id="address" name="address">
                          </div>
                        </div>
    
                        
                        <div class="col-sm-6">
                          <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" class="form-control" id="company" name="company">
                          </div>
                        </div>
    
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="newVendorBtn" class="btn btn-success">add</button>
                </div>
            </form>
        </div>
    </div>
</div>
