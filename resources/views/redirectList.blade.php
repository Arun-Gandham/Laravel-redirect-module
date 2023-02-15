<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet">   
<script src="https://code.jquery.com/jquery-3.5.1.js" ></script>
<script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js" ></script>
<link rel="stylesheet" 
href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap5.min.css"></style>
<script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap5.min.js"></script><script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" ></script>
<div class="container-fluied">
    <div style="display:none" id="spin_loader" class="position-fixed w-100 h-100 flex-column align-items-center  justify-content-center bg-light opacity-50">
        <div class="spinner-grow" role="status">
        </div>
    </div>
    <button type="button" class="btn btn-outline-dark add_redirect_button">Add Redirect</button>
    <div class="row m-2 border bg-light" id="add_redirect" style="display:none">
        <div class="col-md-12">
            <form id="add_redirect_form" method="post">
                <div class="form-group  mt-2">
                    <lable for="source_url">Source Url</lable>
                    <input class="form-control" id="source_url" placeholder="Source Url" required>
                </div>
                <div class="form-group  mt-2">
                    <lable for="target_url">Target Url</lable>
                    <input class="form-control" id="target_url" placeholder="Target Url" required>
                </div>
                <div class="row  mt-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <lable for="match_type">Match Type</lable>
                            <select class="form-control" id="match_type">
                                <option value="exact_match">Exact Match</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mt-3 ">
                    <button class="btn btn-danger add_redirect_button" type="button">Cancle</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <div class="row bg-light m-2 border">
        <div class="col-md-2 my-2">
            <select class="form-select" name="bulk_action_type"  id="bulk_action_type" aria-label="Default select example">
                <option selected value="">Bulk Action</option>
                <option value="Disable">Disable</option>
                <option value="Enable">Enable</option>
                <option value="Delete">Delete</option>
            </select>
        </div>
        <div class="col-md-2 my-2">
            <button type="button" class="btn btn-primary" name="apply_bulk_action" id="apply_bulk_action">Apply</button>
        </div>
        <div class="col-md-12">
            <table id="redirectLinksTable" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th class="active" data-orderable="false" width="30">
                            <input type="checkbox" class="select-all checkbox" name="select-all" />
                        </th>
                        <th class="success" width="300">Url</th>
                        <th class="success" width="300">Code</th>
                        <th class="success" width="300">Hits</th>
                        <th class="success" width="300">Last Access</th>

                    </tr>
                </thead>
                <tbody id="add_new_redirect_after_submit">
                </tbody>
                
            </table>
        </div>
        
    </div>
</div>

<!-- Redirect Form Edit Modal -->
<div class="modal fade" id="editRedirectLink" tabindex="-1" role="dialog" aria-labelledby="editRedirectLinkLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editRedirectLinkLabel">Update redirect url</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="edit_redirect_form" method="post">
                    <div class="form-group  mt-2">
                        <lable for="source_url">Source Url</lable>
                        <input class="form-control" id="edit_source_url"  name="edit_source_url" placeholder="Source Url" required>
                        <input type="hidden" id="edit_redirect_id" name="edit_redirect_id" placeholder="Source Url" >
                    </div>
                    <div class="form-group  mt-2">
                        <lable for="target_url">Target Url</lable>
                        <input class="form-control" id="edit_target_url"  name="edit_target_url" placeholder="Target Url" required>
                    </div>
                    <div class="row  mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <lable for="match_type">Match Type</lable>
                                <select class="form-control" id="edit_match_type" name="edit_match_type">
                                    <option value="exact_match">Exact Match</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary submit_edit_redirect_form">Update</button>
      </div>
    </div>
  </div>
</div>



<script>
    // bulk action functionality
    $('#apply_bulk_action').on('click',function(){
        var checkedBoxes = [];
        var selectedAction = $('#bulk_action_type').val();
        if(selectedAction == "")
        {
            alert("Please select action type");
            return "";
        }
        $('.checkbox').each(function () {
            if(this.checked && $(this).val() != "on")
            {
                checkedBoxes.push($(this).val());
            }
        });
        if(checkedBoxes.length <= 0)
        {
            alert("Please select checkboxes");
            return "";
        }
        $.ajax({
            type: "POST",
            url: "{{ route('redirectlink.bulkaction.update') }}",
            dataType: "json",
            data: {"_token": "{{ csrf_token() }}",selected_checkboxes: checkedBoxes, bulk_action: selectedAction},
            beforeSend: function () {
                // do something before sending
                $('#spin_loader').css('display',"flex");
            },
            error: function (errMessage) {
                // if error console log message
                console.log(errMessage);
                alert("Something went wrong");
            },
            success: function (data) {
                if(data.status == 200)
                {
                    var oTable = $('#redirectLinksTable').dataTable( );
                    // to reload
                    oTable.api().ajax.reload();
                    get = document.getElementsByClassName('checkbox');

                    for(var i=0; i<get.length; i++) {
                        get[i].checked = false;
                    }

                }
                else
                {
                    alert("Something went wrong");
                }
                
            },
            complete: function () {
                $('#spin_loader').css('display',"none");
            }
        });

    });

    // toggle edit form 
    function toggleEditForm(id,from,to)
    {
        $('#edit_redirect_id').val(id);
        $('#edit_source_url').val(from);
        $('#edit_target_url').val(to);
        $('#editRedirectLink').modal('show');
    }

    // Edit from submit modal
    $(".submit_edit_redirect_form").on('click', function () {
        $('#edit_redirect_form').submit();
    });
    $('#edit_redirect_form').on('submit',function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "{{ route('redirectlink.update') }}",
            dataType: "json",
            data: {"_token": "{{ csrf_token() }}",id:e.target.edit_redirect_id.value,from_url: e.target.edit_source_url.value, to_url: e.target.edit_target_url.value, match_type: e.target.edit_match_type.value},
            beforeSend: function () {
                // do something before sending
                $('#editRedirectLink').modal('hide');
                $('#spin_loader').css('display',"flex");
                
            },
            error: function (errMessage) {
                alert("Something went wrong");
                console.log(errMessage);
            },
            success: function (data) {
                $('#spin_loader').css('display',"none");
                var checkDisabled = data?.insertedData?.is_disabled == 1 ? "none" : "inline";
                var checkEnabled = data?.insertedData?.is_disabled == 0 ? "none" : "inline";
                if(data.status == 200)
                {
                    var oTable = $('#redirectLinksTable').dataTable( );
                    // to reload
                    oTable.api().ajax.reload();
                }
            },
            complete: function () {
                $('#spin_loader').css('display',"none");
            }
        });
    });
    
    // add redirect link
    $('#add_redirect_form').on('submit',function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "{{ route('create.pageredirect') }}",
            dataType: "json",
            data: {"_token": "{{ csrf_token() }}",from_url: e.target.source_url.value, to_url: e.target.target_url.value, match_type: e.target.match_type.value},
            beforeSend: function () {
                // do something before sending
                $('#spin_loader').css('display',"flex");
            },
            error: function (errMessage) {
                // if error console log message
                alert("Something went wrong");
                console.log(errMessage);
            },
            success: function (data) {
                if(data.status == 201)
                {
                    var oTable = $('#redirectLinksTable').dataTable();
                    oTable.api().ajax.reload();
                    $("#add_redirect_form").trigger('reset');
                    
                }
                else
                {
                    alert("Redirect exists for this url");
                }
            },
            complete: function () {
                $('#spin_loader').css('display',"none");
            }
        });
    });
    
    // 
    $(".add_redirect_button").on('click', function () {
        $("#add_redirect").css("display",$("#add_redirect").css("display") == "none" ? "block" : "none");
        $("#add_redirect_form").trigger('reset');
    });

    // To show the enable and disable buttons
    function toggleDisable(toggle,id)
    {
        $.ajax({
            type: "POST",
            url: "{{ route('redirectlink.enable.disable') }}",
            dataType: "json",
            data: {"_token": "{{ csrf_token() }}",disable_val: toggle, id: id},
            beforeSend: function () {
                // do something before sending
                $('#spin_loader').css('display',"flex");
            },
            error: function (errMessage) {
                // if error console log message
                console.log(errMessage);
                alert("Something went wrong");
            },
            success: function (data) {
                $('#spin_loader').css('display',"none");
                if(data.status == 200)
                {
                    if(toggle == "1")
                    {
                        $(".disable_link_"+ id).css("display","none");
                        $(".enable_link_"+ id).css("display","inline");
                    }
                    else
                    {
                        $(".disable_link_"+ id).css("display","inline");
                        $(".enable_link_"+ id).css("display","none");
                    }
                }
            },
            complete: function () {
                
            }
        });
    }

    // To delete redirect link
    function delteRediretcLink(linkId)
    {
        var id = linkId;
        $.ajax({
            type: "POST",
            url: "{{ route('redirectlink.delete') }}",
            dataType: "json",
            data: {"_token": "{{ csrf_token() }}", id: id},
            beforeSend: function () {
                // do something before sending
                $('#spin_loader').css('display',"flex");
            },
            error: function (errMessage) {
                // if error console log message
                console.log(errMessage);
                alert("Something went wrong");
            },
            success: function (data) {
                if(data.status == 200)
                {
                    var oTable = $('#redirectLinksTable').dataTable();
                    $('#spin_loader').css('display',"none");
                    // to reload
                    oTable.api().ajax.reload();
                }
            },
            complete: function () {
            }
        });
    }

    // Checkboxs check all
    $(function(){
        //button select all or cancel
        $("#select-all").click(function () {
            var all = $("input.select-all")[0];
            all.checked = !all.checked
            var checked = all.checked;
            $("input.select-item").each(function (index,item) {
                item.checked = checked;
            });
        });
        //column checkbox select all or cancel
        $("input.select-all").click(function () {
            var checked = this.checked;
            $("input.select-item").each(function (index,item) {
                item.checked = checked;
            });
        });
        //check selected items
        $("input.select-item").click(function () {
            var checked = this.checked;
            var all = $("input.select-all")[0];
            var total = $("input.select-item").length;
            var len = $("input.select-item:checked:checked").length;
            all.checked = len===total;
        });
        
    });

    // Inititalize datatable
    $(document).ready(function () {
        $('#redirectLinksTable').DataTable({
            "processing":true,
            "serverSide":true,
            "ajax": "{{ route('get.redirectlinks') }}",
            "columns": [
                {"data": "checkboxes","searchable": false,"sortable":false},
                {"data": "from_data"},
                {"data": "code","searchable": false},
                {"data": "hits","searchable": false},
                {"data": "formated_lastseen","searchable": false},
            ],
            'columnDefs': [ {
                'targets': [0,2,4], // column index (start from 0)
                'orderable': false, // set orderable false for selected columns
            }],
            "order": []
        });
    });
</script>
