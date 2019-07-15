@extends('layouts.user.index')
@section('page_title','List of all Machine Groups')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">List of all Machine Groups</h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ route('machine.groups.add') }}" class="text-danger"><strong>Create a new Machine Groups</strong></a>
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
                        {{ Form::model($machine_group_search,array('route' => 'machine.groups.list')) }}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('group_name','Group Name')}}
                                        {{Form::text('group_name',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-8 p-t-25">
                                    {{ Form::submit('Search',['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('machine.groups.list',['reset'=>1]) }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Reset</a>
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
                                        <th>@sortablelink('group_name','Group Name')</th>
                                        <th>Assigned Machines</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($machine_groups as $machine_group)
                                        <tr>
                                            <th>{{ $machine_group->group_name }}</th>
                                            <td>
                                                @forelse ($machine_group->machine as $machine)
                                                    <label class="label label-primary font-per-100">{{$machine->machine_number}} ({{$machine->machine_name}})</label>
                                                @empty
                                                    <label class="label label-danger">No Machines Assigned</label>
                                                @endforelse
                                            </td>
                                            <td>
                                                <a href="{{ route('machine.groups.update',$machine_group->id) }}" data-toggle="tooltip" data-placement="top" data-original-title="Edit Machine Group"><i class="fa fa-lg fa-edit text-primary"></i></a>
                                                <a href="{{ route('machine.groups.delete',$machine_group->id) }}" class="group-delete" data-toggle="tooltip" data-placement="top" data-original-title="Delete Machine Group"><i class="fa fa-lg fa-trash text-danger"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {!! $machine_groups->appends(\Request::except('page'))->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
    <script type="text/javascript">
        $('a.group-delete').confirm({
            title: 'Heads-up!',
            content: 'Are you sure want to <strong class="text-danger">Delete</strong> this Group?',
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
    </script>
@endsection
