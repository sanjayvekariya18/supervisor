@extends('layouts.user.index')
@section('page_title','Machine Settings')
@section('content')

    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">
                    Machine Settings : <b class="text-danger">{{ $machine->machine_name . ' (' . $machine->machine_number . ')' }}</b>
                </h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ route('machines.list') }}" class="text-danger"><strong>Back to List</strong></a>
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
                            <div class="row">
                                <div class="width-100">
                                    @include('user.machines.settings.nav_tabs', ['current_tab' => $current_tab])
                                    <!-- Tab panes -->
                                    <div class="tab-content card-block">
                                        {{ Form::model($machine,array('route' => ['machines.settings.buzzer',encrypt_str($machine->id)],'id'=>'frm_color_range')) }}
                                        <div class="tab-pane {{ $current_tab=='setting_buzzer' ? 'active' : '' }}" id="setting_buzzer" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    
                                                <label class="text-info"> <i class="fa fa-info-circle"></i> Machine will buzz when below time will match while machine is stopped</label>
                                                </div>
                                            </div> 
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>After stopped for 10 Minutes</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {{Form::select('10_min',buzz_time(),$buzzer_time_data['10_min'],['class'=>'form-control'])}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>After stopped for 20 Minutes</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {{Form::select('20_min',buzz_time(),$buzzer_time_data['20_min'],['class'=>'form-control'])}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>After stopped for 30 Minutes</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {{Form::select('30_min',buzz_time(),$buzzer_time_data['30_min'],['class'=>'form-control'])}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::submit("Save changes",['class'=>'btn hor-grd btn-grd-info btn-round']) }}
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
@endsection