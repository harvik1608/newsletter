@extends('include.header')
@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Site List</h4>
        <h6>(<span class='mandadory'>*</span>) indicates required field.</h6>
    </div>
</div>
<form action="{{ is_null($letter) ? url('letters') : url('letters/'.$letter->id) }}" method="POST" enctype="multipart/form-data" id="mainForm">
    @csrf
    @if(!is_null($letter))
        <input type="hidden" name="_method" value="PUT" />
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ is_null($letter) ? "New" : "Edit" }} Letter</h4>
                </div>
                <div class="card-body profile-body">
                    <div class="row">
                        <div class="col-lg-9 mb-3">
                            <label class="form-label">Title<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="title" id="title" value="{{ is_null($letter) ? '' : $letter->title }}" autofocus />
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label">Status</label>
                            <select class="select" name="is_active" id="is_active">
                                <option value="1" {{ !is_null($letter) && $letter->is_active == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ !is_null($letter) && $letter->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="description">{{ is_null($letter) ? '' : $letter->description }}</textarea>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label class="form-label">File</label>
                            <div class="file-drop mb-3 text-center">
                                <span class="avatar avatar-sm bg-primary text-white mb-2">
                                    <i class="ti ti-upload fs-16"></i>
                                </span>
                                <h6 class="mb-2"><small>( Only .docx file allow)</small></h6>
                                <p class="fs-12 mb-0"></p>
                                <input type="file" name="file" id="file" />
                                @if(!is_null($letter) && $letter->file != "")
                                    <input type="hidden" name="old_logo" value="" />
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-2">
                        <button type="submit" class="btn btn-primary">SUBMIT</button>
                        <a href="{{ url('letters') }}" class="btn btn-secondary" id="backBtn">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
	var page_title = "Letter List";
    $(document).ready(function(){
        $("#mainForm").validate({
            rules:{
                title:{
                    required: true
                },
                file:{
                    required: true
                }
            },
            messages:{
                title:{
                    required: "<small class='text-danger'><b>Title is required.</b></small>"
                },
                file:{
                    required: "<small class='text-danger'><b>File is required.</b></small>"
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
