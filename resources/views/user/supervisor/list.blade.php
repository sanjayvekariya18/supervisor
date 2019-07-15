@extends('layouts.user.index')
@section('page_title','List of all Supervisors')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">List of all Supervisors</h5>
            </div>
            
            <div class="page-header-breadcrumb">
                @if(permissions()->total_user > getTotalUser())
                    <a href="{{ route('supervisors.add') }}" class="text-danger"><strong>Create a new Supervisor</strong></a>
                @else
                    <div class="alert alert-danger">
                        Your Total User Limit Reached
                    </div>
                @endif
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
                        {{ Form::model($supervisor_search,array('route' => 'supervisors.list')) }}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('id','Supervisor ID')}}
                                        {{Form::text('id',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-8 p-t-25">
                                    {{ Form::submit('Search',['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('supervisors.list',['reset'=>1]) }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Reset</a>
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
                                        <th>@sortablelink('id','Supervisor ID')</th>
                                        <th>@sortablelink('username')</th>
                                        <th>@sortablelink('first_name','Name')</th>
                                        <th>Contact No.</th>
                                        <th>Group</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($supervisors as $supervisor)
                                        <tr>
                                            <th>{{ $supervisor->id }}</th>
                                            <td>{{ $supervisor->username }}</td>
                                            <td>{{ $supervisor->first_name . ' ' . $supervisor->last_name}}</td>
                                            <td>{{ $supervisor->contact_number_1 }}</td>
                                            <td>
                                                @php 
                                                    $machineGroups = getSupervisorGroups($supervisor->id);
                                                @endphp
                                                @forelse ($machineGroups as $group)
                                                    <label class="label label-primary font-per-100">{{$group->group_name}}</label>
                                                @empty
                                                    <label class="label label-danger">No Group Assigned</label>
                                                @endforelse
                                            </td>
                                            <td>
                                                @if ($supervisor->status_id==1)
                                                    <a href="{{ route('supervisors.status.change',[$supervisor->id,0]) }}" class="supervisor-inactive" data-status="Inactivate">
                                                        <span class="label label-success font-per-100" data-toggle="tooltip" data-placement="top" data-original-title="Click here to Inactivate">Active</span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('supervisors.status.change',[$supervisor->id,1]) }}" class="supervisor-active" data-status="Activate">
                                                        <span class="label label-danger font-per-100" data-toggle="tooltip" data-placement="top" data-original-title="Click here to Activate">Inactive</span>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('supervisors.update',$supervisor->id) }}" data-toggle="tooltip" data-placement="top" data-original-title="Edit Supervisor"><i class="fa fa-lg fa-edit text-primary"></i></a>
                                                <a href="{{ url('supervisors/'.$supervisor->id.'/permission') }}" data-toggle="tooltip" data-placement="top" data-original-title="Permission"><i class="fa fa-lg fa-lock text-primary"></i></a>
                                                <form action="{{ route('supervisors.delete',$supervisor->id) }}" method="post" style="display: inline-flex;">
                                                    @csrf
                                                    <a href="javascript:void(0);" class="supervisor-delete" data-toggle="tooltip" data-placement="top" data-original-title="Delete Supervisor"><i class="fa fa-lg fa-trash text-danger"></i></a>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {!! $supervisors->appends(\Request::except('page'))->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
        
    <script type="text/javascript">
        $('a.supervisor-delete').confirm({
            title: 'Heads-up!',
            content: 'Are you sure want to <strong class="text-danger">Delete</strong> this Supervisor?',
            buttons: {
                confirm:{
                    btnClass: 'btn-warning',
                    action: function () {
                        this.$target.parent('form').submit();
                    }
                },
                cancel: function () {
                    
                }
            }
        });
        $('a.supervisor-active').confirm({
            title: 'Heads-up!',
            content: 'Are you sure want to <strong class="text-green">Activate</strong> this Supervisor?',
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

        $('a.supervisor-inactive').confirm({
            title: 'Heads-up!',
            content: ''+
            '<form>' +
            '<div class="form-group">' +
            '<label>Are you sure want to <strong class="text-red">Inactivate</strong> this Supervisor?</label>' +
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
    </script>
@endsection
