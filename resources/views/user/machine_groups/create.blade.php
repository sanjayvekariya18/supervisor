@extends('layouts.user.index')
@section('page_title','Machine Groups')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">
                    @if ($action=='create')
                        Create a new Machine Group
                        @php 
                            $form_route = ['machine.groups.add'];
                            $submit_text = 'Create';
                        @endphp
                    @else
                        Update Machine Group
                        @php 
                            $form_route=['machine.groups.update',$request->id];
                            $submit_text = 'Update';
                        @endphp
                    @endif
                </h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ route('machine.groups.list') }}" class="text-danger"><strong>Back to List</strong></a>
            </div>
        </div>
    </div>
    <!-- Page header end -->

    <!-- Page body start -->
    <div class="page-body">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-right"><i class="fa fa-window-maximize full-card"></i></div>
                    </div>
                    <div class="card-block">
                        <div class="bs-example grid-layout">
                            {{ Form::model($request,array('route' => $form_route,'id'=>'frm_machine_group')) }}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{Form::label('group_name','Group Name')}}<b class="err-asterisk"></b>
                                        {{Form::text('group_name',null,['class'=>'form-control'])}}
                                        @if ($errors->has('group_name'))
                                            <p class="error">{{ $errors->first('group_name') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {{Form::label('assigned_machines','List of all Not assigned Machines, Please select of them to assign to Group')}}<b class="err-asterisk"></b>
                                        <div class="border-checkbox-section">

                                            @forelse ($machines_list as $key => $machine_details)
                                                @if ($machine_details['group_id']==$machine_group_id AND $machine_group_id != '0')
                                                    @php ($_checked = 'checked')
                                                    @php ($checked_cls = 'primary')
                                                @else
                                                    @php ($_checked = '')
                                                    @php ($checked_cls = 'danger')
                                                @endif
                                                <div class="border-checkbox-group border-checkbox-group-{{$checked_cls}} col-md-2">
                                                    <input class="border-checkbox" {{ $_checked }} name="assigned_machines[]" type="checkbox" value="{{ $machine_details['id'] }}" id="machine_{{ $machine_details['id'] }}">
                                                    <label class="border-checkbox-label" for="machine_{{ $machine_details['id'] }}">{{ $machine_details['machine_number'] }} ({{ $machine_details['machine_name'] }})</label>
                                                </div>
                                            @empty
                                                <p class="text-warning">All Machines are assigned to other groups</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::submit($submit_text,['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('machine.groups.list') }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Back</a>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
 {!! JsValidator::formRequest('App\Http\Requests\MachineGroupRequest','#frm_machine_group'); !!}

@endsection