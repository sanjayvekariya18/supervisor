@extends('layouts.user.index')
@section('page_title','Fixed Bonus')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">
                    @if ($action=='create')
                        Create a new Fixed Bonus
                        @php 
                            $disabled='';
                            $form_route = ['bonuses.fixed.create'];
                            $submit_text = 'Create';
                        @endphp
                    @else
                        Update Fixed Bonus
                        @php 
                            $disabled='readonly';
                            $form_route=['bonuses.fixed.update',$request->id];
                            $submit_text = 'Update';
                        @endphp
                    @endif
                </h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ route('bonuses.fixed.list') }}" class="text-danger"><strong>Back to List</strong></a>
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
                            {{ Form::model($request,array('route' => $form_route,'id'=>'frm_save')) }}
                            

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {{Form::label('assigned_machines','List of all Not assigned Machines, Select one of them to apply Bonus')}}<b class="err-asterisk"></b>
                                        <div class="border-checkbox-section">
                                            @php ($found_machine = false)
                                                
                                            @foreach ($machines_list as $machine_number => $machine_name)
                                                
                                                @if (!in_array($machine_number,$assigned_machines))
                                                    @if (in_array($machine_number,$selected_machines))
                                                        @php ($_checked = 'checked')
                                                        @php ($checked_cls = 'primary')
                                                    @else
                                                        @php ($_checked = '')
                                                        @php ($checked_cls = 'danger')
                                                    @endif

                                                    @php ($found_machine=true)
                                                        
                                                    <div class="border-checkbox-group border-checkbox-group-{{$checked_cls}} col-md-1">
                                                        <input class="border-checkbox" {{ $_checked }} name="machine_id[]" type="checkbox" value="{{ $machine_number }}" id="machine_{{ $machine_number }}">
                                                        <label class="border-checkbox-label" for="machine_{{ $machine_number }}">{{ $machine_name }}</label>
                                                    </div>
                                                @endif

                                            @endforeach

                                            @if ($found_machine==false)
                                                <p class="text-red f-20">No Machines are available to assign bonus</p>
                                            @endif

                                            @if ($errors->has('machine_id'))
                                                <p class="error">{{ $errors->first('machine_id') }}</p>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                                
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('min_stitches','Minimum Stitches')}}<b class="err-asterisk"></b>
                                        {{Form::number('min_stitches',null,['class'=>'form-control form-control-capitalize'])}}
                                        @if ($errors->has('min_stitches'))
                                            <p class="error">{{ $errors->first('min_stitches') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('min_stitches_bonus','Minimum Stitches Bonus')}}<b class="err-asterisk"></b>
                                        {{Form::number('min_stitches_bonus',null,['class'=>'form-control'])}}
                                        @if ($errors->has('min_stitches_bonus'))
                                            <p class="error">{{ $errors->first('min_stitches_bonus') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('after_min_per_stitches','After Minimum Per Stitches')}}<b class="err-asterisk"></b>
                                        {{Form::number('after_min_per_stitches',null,['class'=>'form-control'])}}
                                        @if ($errors->has('after_min_per_stitches'))
                                            <p class="error">{{ $errors->first('after_min_per_stitches') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('after_min_per_stitches_bonus','After Minimum Per Stitches Bonus')}}<b class="err-asterisk"></b>
                                        {{Form::number('after_min_per_stitches_bonus',null,['class'=>'form-control'])}}
                                        @if ($errors->has('after_min_per_stitches_bonus'))
                                            <p class="error">{{ $errors->first('after_min_per_stitches_bonus') }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::submit($submit_text,['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('bonuses.fixed.list') }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Back</a>
                                    
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
    
 {!! JsValidator::formRequest('App\Http\Requests\FixedBonusRequest','#frm_save'); !!}

@endsection