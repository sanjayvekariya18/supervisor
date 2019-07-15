@extends('layouts.admin.index')
@section('page_title','Create a new Machine')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">
                    @if ($action=='create')
                        Create a new Machine
                        @php 
                            $disabled='';
                            $form_route = ['admin.machines.create'];
                            $submit_text = 'Create';
                        @endphp
                    @else
                        Update Machine
                        @php 
                            $disabled='readonly';
                            $form_route=['admin.machines.update',$form_data->id];
                            $submit_text = 'Update';
                        @endphp
                    @endif
                </h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ route('admin.machines.list') }}" class="text-danger"><strong>Back to List</strong></a>
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
                            {{ Form::model($form_data,array('route' => $form_route,'id'=>'frm_save')) }}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('cust_id','Select Customer')}}<b class="err-asterisk"></b>
                                        {{Form::select('cust_id',$customers,$selected_cust,['class'=>'form-control form-control-uppercase',$disabled])}}
                                        @if ($errors->has('cust_id'))
                                            <p class="error">{{ $errors->first('cust_id') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('machine_id','Machine ID')}}<b class="err-asterisk"></b>
                                        {{Form::text('machine_id',null,['class'=>'form-control form-control-uppercase'])}}
                                        @if ($errors->has('machine_id'))
                                            <p class="error">{{ $errors->first('machine_id') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('machine_number','Machine Number')}}<b class="err-asterisk"></b>
                                        {{Form::text('machine_number',null,['class'=>'form-control'])}}
                                        @if ($errors->has('machine_number'))
                                            <p class="error">{{ $errors->first('machine_number') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::submit($submit_text,['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('admin.machines.list') }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Back</a>
                                    
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
 {!! JsValidator::formRequest('App\Http\Requests\AdminMachineRequest','#frm_save'); !!}

@endsection