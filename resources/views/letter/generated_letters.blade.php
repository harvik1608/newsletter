@extends('include.header')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
<style>
    .image-upload .image-uploads {
        top: 20px;
    }
</style>
<div class="page-header">
    <div class="add-item d-flex">
        <div class="page-title">
            <h4 class="fw-bold">{{ $letter->title }}</h4>
            <h6></h6>
        </div>
    </div>
    <div class="page-btn">
        <a href="{{ route('admin.letter.generate',['letter_id' => $letter->id ]) }}" class="btn btn-primary text-white">Generate</a>
    </div>
</div>
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
        <div class="search-set">
            <div class="search-input">
                <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table" id="tblList">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">Client Name</th>
                        <th width="15%">Created On</th>
                        <th width="10%" class="no-sort"></th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
<script>
	var page_title = "Letter List";
    var letter_id = "{{ $letter->id }}";
    
	$(document).ready(function(){
        $('#tblList').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('admin.generated_letter.load') }}",
                "type": "GET",
                "data": function(d) {
                    d.letter_id = letter_id;
                }
            },
            "bFilter": true,
            "sDom": 'fBtlpi',
            "ordering": true,
            "columns": [
                { data: 'id' },
                { data: 'client' },
                { data: 'created_on' },
                { 
                    data: 'actions', 
                    orderable: false, 
                    searchable: false,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('action-table-data'); // Add custom class to <td>
                    }
                }
            ],
            "language": {
                search: ' ',
                sLengthMenu: '_MENU_',
                searchPlaceholder: "Search",
                // sLengthMenu: 'Row Per Page _MENU_ Entries',
                info: "_START_ - _END_ of _TOTAL_ items",
                paginate: {
                    next: ' <i class="fa fa-angle-right"></i>',
                    previous: '<i class="fa fa-angle-left"></i>'
                },
            },
            initComplete: (settings, json) => {
                $('.dataTables_filter').appendTo('#tableSearch');
                $('.dataTables_filter').appendTo('.search-input');
            }  
        });
    });
    function open_modal()
    {
    	$("#import-sites").modal("show");
    }
</script>
@endsection
