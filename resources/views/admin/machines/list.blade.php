@extends('layouts.admin.index')
@section('page_title','List of all Machines')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">List of all Machines</h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ route('admin.machines.create') }}" class="text-danger"><strong>Create a new Machine</strong></a>
            </div>
        </div>
    </div>
    <!-- Page header end -->

    <!-- Page body start -->
    <div class="page-body">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header p-b-5">
                        {{ Form::model($machine_search,array('route' => 'admin.machines.list')) }}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('cust_id','Customer ID')}}
                                        {{Form::select('cust_id',$customer_list,null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('machine_number','Machine Number')}}
                                        {{Form::text('machine_number',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-5 p-t-25">
                                    {{ Form::submit('Search',['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('admin.machines.list',['reset'=>1]) }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Reset</a>
                                </div>

                            </div>
                        {{ Form::close() }}
                        <div class="card-header-right">
                            <i class="fa fa-window-maximize full-card"></i>
                        </div>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>@sortablelink('machine_id','Machine ID')</th>
                                        <th>@sortablelink('machine_number','Machine Number')</th>
                                        <th>@sortablelink('machine_name','Machine Name')</th>
                                        <th>Worker</th>
                                        <th>Group</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($machines as $machine)
                                        <tr>
                                            <td>{{ $machine->cust_id}}</td>
                                            <th>{{ $machine->machine_id }}</th>
                                            <td>{{ $machine->machine_number }}</td>
                                            <td>{{ $machine->machine_name }}</td>
                                            <td>
                                                @if ($machine->worker)
                                                    {{ $machine->worker->first_name . ' ' . $machine->worker->last_name }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($machine->machine_group)
                                                    {{ $machine->machine_group->group_name }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($machine->status_id==1)
                                                    <a href="{{ route('admin.machines.status.change',[$machine->id,0]) }}" class="machine-inactive" data-status="Inactivate">
                                                        <span class="label label-success font-per-100" data-toggle="tooltip" data-placement="top" data-original-title="Click here to Inactivate">Active</span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.machines.status.change',[$machine->id,1]) }}" class="machine-active" data-status="Activate">
                                                        <span class="label label-danger font-per-100" data-toggle="tooltip" data-placement="top" data-original-title="Click here to Activate">Inactive</span>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.machines.update',$machine->id) }}" data-toggle="tooltip" data-placement="top" data-original-title="Edit Machine"><i class="fa fa-lg fa-edit text-primary"></i></a>
                                                <form action="{{ route('admin.machines.delete',$machine->id) }}" method="POST" style="display: inline-flex">
                                                    @csrf
                                                    <button class="btn btn-default p-0 bg-white ml-2 delete" type="button" data-toggle="tooltip" data-placement="top" data-original-title="Edit Machine">
                                                        <i class="fa fa-lg fa-trash text-danger"></i>
                                                    </button>    
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if (!empty($machines))
                                {!! $machines->appends(\Request::except('page'))->render() !!}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
        
    <script type="text/javascript">
    
        jQuery(document).ready(function($) {

            $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });

            $('.change-rpm-cal').editable({
                success: function(response, newValue) {
                    $.notify(response,'success');
                },
                error: function(response, newValue) {
                    $(".editable-error-block").html('');
                    $.notify(response.responseJSON,'error');
                 },
            });

        });

        $('a.machine-active').confirm({
            title: 'Heads-up!',
            content: 'Are you sure want to <strong class="text-green">Activate</strong> this Machine?',
            buttons: {
                confirm:{
                    btnClass: 'btn-warning',
                    action: function () {
                        location.href = this.$target.attr('href');
                    }
                },
                cancel: function () {
                    
                }
            }
        });

        $('a.machine-inactive').confirm({
            title: 'Heads-up!',
            content: ''+
            '<form>' +
            '<div class="form-group">' +
            '<label>Are you sure want to <strong class="text-red">Inactivate</strong> this Machine?</label>' +
            '<input type="text" placeholder="Enter reason (Max 100 Characters)" id="inactivate_reason" class="form-control" required />' +
            '</div>' +
            '</form>',
            buttons: {
                confirm:{
                    btnClass: 'btn-warning',
                    action: function () {
                        var inactivate_reason = this.$content.find('#inactivate_reason').val().trim();
                        if(!inactivate_reason){
                            $.alert('Please provide a valid reason');
                            return false;
                        }else if(inactivate_reason.length > 100){
                            $.alert('Max 100 characters are allowed');
                            return false;
                        }
                        location.href = this.$target.attr('href') + "/" + inactivate_reason;
                    }
                },
                cancel: function () {
                    
                }
            }
        });

        $(document).on('click','.delete',function(){
            $form  = $(this).parent('form');
            swal({
					title: "Are you sure?",
					text: "You will not be able to recover this Machine!",
					type: "warning",
					showCancelButton: true,
					confirmButtonClass: "btn-danger",
					confirmButtonText: "Yes, delete it!",
					cancelButtonText: "No, cancel Please!",
					closeOnConfirm: false,
					closeOnCancel: false
				},
				function(isConfirm) {
					if (isConfirm) {
                        $form.submit();
                        swal("Deleted!", "Your Machine has been deleted.", "success");
					} else {
						swal("Cancelled", "Your Machine is safe :)", "error");
					}
				});
        });
    </script>
@endsection
