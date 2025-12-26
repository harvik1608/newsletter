@extends('include.header')
@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Site List</h4>
        <h6>(<span class='mandadory'>*</span>) indicates required field.</h6>
    </div>
</div>
<form action="{{ is_null($site) ? url('admin/sites') : url('admin/sites/'.$site->id) }}" method="POST" enctype="multipart/form-data" id="mainForm">
    @csrf
    @if(!is_null($site))
        <input type="hidden" name="_method" value="PUT" />
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ is_null($site) ? "New" : "Edit" }} Site</h4>
                </div>
                <div class="card-body profile-body">
                    <div class="row">
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Project No.<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="project_no" id="project_no" value="{{ is_null($site) ? '' : $site->project_no }}" autofocus />
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Project Type<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="project_type" id="project_type" value="{{ is_null($site) ? '' : $site->project_type }}" />
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Project Start Date<span class="text-danger ms-1">*</span></label>
                            <input type="date" class="form-control" name="project_start_date" id="project_start_date" value="{{ is_null($site) ? '' : $site->project_start_date }}" />
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Project Status<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="project_status" id="project_status" value="{{ is_null($site) ? '' : $site->project_status }}" />
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Invoice Status<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="invoice_status" id="invoice_status" value="{{ is_null($site) ? '' : $site->invoice_status }}" />
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Status</label>
                            <select class="select" name="is_active" id="is_active">
                                <option value="1" {{ !is_null($site) && $site->is_active == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !is_null($site) && $site->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-8 mb-3">
                            <label class="form-label">Project Address<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="address" id="address" value="{{ is_null($site) ? '' : $site->address }}" />
                        </div>
                        <div class="col-lg-2 mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="text" class="form-control" name="lat" id="lat" value="{{ is_null($site) ? '' : $site->lat }}" />
                        </div>
                        <div class="col-lg-2 mb-3">
                            <label class="form-label">Longitude<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="lng" id="lng" value="{{ is_null($site) ? '' : $site->lng }}" />
                        </div>
                    </div>
                    <div class="text-end mt-2">
                        <button type="submit" class="btn btn-primary">SUBMIT</button>
                        <a href="{{ url('admin/sites') }}" class="btn btn-secondary" id="backBtn">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
	var page_title = "Site List";
    $(document).ready(function(){
        $("#mainForm").validate({
            rules:{
                project_no:{
                    required: true
                },
                project_type:{
                    required: true
                },
                project_start_date:{
                    required: true
                },
                project_status:{
                    required: true
                },
                invoice_status:{
                    required: true
                },
                address:{
                    required: true
                }
            },
            messages:{
                project_no:{
                    required: "<small class='text-danger'><b>Project No. is required.</b></small>"
                },
                project_type:{
                    required: "<small class='text-danger'><b>Project Type is required.</b></small>"
                },
                project_start_date:{
                    required: "<small class='text-danger'><b>Project Start Date is required.</b></small>"
                },
                project_status:{
                    required: "<small class='text-danger'><b>Project Status is required.</b></small>"
                },
                invoice_status:{
                    required: "<small class='text-danger'><b>Invoice Status is required.</b></small>"
                },
                address:{
                    required: "<small class='text-danger'><b>Address is required.</b></small>"
                }
            }
        });
        $("#mainForm").submit(function(e){
            e.preventDefault();

            if($("#mainForm").valid()) {
                $.ajax({
                    url: $("#mainForm").attr("action"),
                    type: $("#mainForm").attr("method"),
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    beforeSend:function(xhr){
                        xhr.setRequestHeader("csrf-token", $("input[name=_csrf]").val());
                        $("#mainForm button[type=submit]").html('<div class="spinner-border spinner-border-sm text-secondary" role="status"><span class="visually-hidden">Loading...</span></div>').attr("disabled",true);
                    },
                    success:function(response){
                        if(response.success) {
                            show_toast("Success!",response.message,"success");
                            setTimeout(function(){
                                window.location.href = $("#backBtn").attr("href");
                            },3000);
                        }
                    },
                    error: function(xhr, status, error) {
                        $("#mainForm button[type=submit]").html("SUBMIT").attr("disabled",false);
                        if (xhr.status === 400) {
                            const res = xhr.responseJSON;
                            show_toast("Oops!",res.message,"error");
                        } else {
                            show_toast("Oops!","Something went wrong","error");
                        }
                    }
                });
            }
        });
    });
</script>
@endsection
