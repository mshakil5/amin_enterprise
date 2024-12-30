<div class="modal fade" id="mvessselModal" tabindex="-1" role="dialog" aria-labelledby="mvessselModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mvessselModalLabel">Add mother vessel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="mvesselForm">
                <div class="modal-body">
                    
                   
                <div class="ermsg"></div>
                
                
                  <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>Name*</label>
                        <input type="text" class="form-control" id="mothervesselname" name="name">
                      </div>
                    </div>

                    
                    <div class="col-sm-6">
                      <div class="form-group">
                        <label>Code</label>
                        <input type="text" class="form-control" id="mothervesselcode" name="code">
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Description</label>
                        <input type="text" class="form-control" id="mothervesseldescription" name="description">
                      </div>
                    </div>


                  </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="newmvBtn" class="btn btn-success">add</button>
                </div>
            </form>
        </div>
    </div>
</div>

