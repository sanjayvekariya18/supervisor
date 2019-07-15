@extends('layouts.user.index')
@section('page_title','Supervisor')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">
                    @if ($action=='create')
                        Create a new Supervisor
                        @php 
                            $disabled='';
                            $form_route = ['supervisors.add'];
                            $submit_text = 'Create';
                        @endphp
                    @else
                        Update Supervisor
                        @php 
                            $disabled='readonly';
                            $form_route=['supervisors.update',$user->id];
                            $submit_text = 'Update';
                            $next_supervisor_id = null;
                        @endphp
                    @endif
                </h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ route('supervisors.list') }}" class="text-danger"><strong>Back to List</strong></a>
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
                            {{ Form::model($user,array('route' => $form_route,'id'=>'frm_supervisor')) }}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('id','Supervisor ID')}}<b class="err-asterisk"></b>
                                        {{Form::text('id',$next_supervisor_id,['class'=>'form-control','readonly' => true])}}
                                        @if ($errors->has('id'))
                                            <p class="error">{{ $errors->first('id') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('username','Username')}}<b class="err-asterisk"></b>
                                        {{Form::text('username',null,['class'=>'form-control',$disabled])}}
                                        @if ($errors->has('username'))
                                            <p class="error">{{ $errors->first('username') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('password_disp','Password')}}<b class="err-asterisk"></b>
                                        {{Form::text('password_disp',null,['class'=>'form-control'])}}
                                        @if ($errors->has('password_disp'))
                                            <p class="error">{{ $errors->first('password_disp') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('first_name','Name')}}<b class="err-asterisk"></b>
                                        {{Form::text('first_name',null,['class'=>'form-control'])}}
                                        @if ($errors->has('first_name'))
                                            <p class="error">{{ $errors->first('first_name') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{Form::label('contact_number_1','Contact Number 1')}}<b class="err-asterisk"></b>
                                        {{Form::text('contact_number_1',null,['class'=>'form-control'])}}
                                        @if ($errors->has('contact_number_1'))
                                            <p class="error">{{ $errors->first('contact_number_1') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{Form::label('contact_number_2','Contact Number 2')}}
                                        {{Form::text('contact_number_2',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{Form::label('email','Email')}}
                                        {{Form::text('email',null,['class'=>'form-control'])}}
                                        @if ($errors->has('email'))
                                            <p class="error">{{ $errors->first('email') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{Form::label('address_1','Address Line 1')}}
                                        {{Form::text('address_1',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{Form::label('address_2','Address Line 2')}}
                                        {{Form::text('address_2',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {{Form::label('assigned_machines','List of all Not assigned Group, Please select of them to assign to Supervisor')}}<b class="err-asterisk"></b>
                                        <div class="border-checkbox-section">
                                            <div class="border-checkbox-group border-checkbox-group-primary col-md-2">
                                                <input class="border-checkbox" id="checkAll" type="checkbox">
                                                <label class="border-checkbox-label" for="checkAll">Select All</label>
                                            </div>
                                             @php 
                                                /* $machineGroups = getSupervisorGroups($user->id);
                                                echo "<pre>";
                                                print_r($group_list);
                                                print_r($machineGroups->toarray());
                                                die; */
                                            @endphp
                                            @forelse ($group_list as $key => $group_details)
                                                @if (in_array($user->id,$group_details['supervisor_id']))
                                                    @php ($_checked = 'checked')
                                                    @php ($checked_cls = 'primary')
                                                @else
                                                    @php ($_checked = '')
                                                    @php ($checked_cls = 'danger')
                                                @endif
                                                <div class="border-checkbox-group border-checkbox-group-{{$checked_cls}} col-md-2">
                                                    <input class="border-checkbox" {{ $_checked }} name="group_ids[]" type="checkbox" value="{{ $group_details['id'] }}" id="group_{{ $group_details['id'] }}">
                                                    <label class="border-checkbox-label" for="group_{{ $group_details['id'] }}">{{ $group_details['group_name'] }}</label>
                                                </div>
                                            @empty
                                                <p class="text-warning">All groups are assigned to other Supervisor</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::submit($submit_text,['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('supervisors.list') }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Back</a>
                                    
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
 {!! JsValidator::formRequest('App\Http\Requests\UserRequest','#frm_supervisor'); !!}
<script>
    $(document).ready(function(){
        $('#checkAll').click(function(){
            if($(this).is(':checked')){
                $('input[name="group_ids[]"]').prop('checked',true);
            }else{
                $('input[name="group_ids[]"]').prop('checked',false);
            }
        });

        var totalGroup = $('input[name="group_ids[]"]').length;
        var totalCheckGroup = $('input[name="group_ids[]"]:checked').length;
        
        if(totalGroup === totalCheckGroup){
            $('#checkAll').prop('checked',true);
        }
    });
</script>
@endsection