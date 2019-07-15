@extends('layouts.admin.index')
@section('page_title','General Settings')
@section('content')

    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">
                    Create New Model </b>
                </h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ url('admin/permission') }}" class="text-danger"><strong>Back to List</strong></a>
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
                            {{ Form::model([],array('url' => ['admin/permission'],'id'=>'frm_customer')) }}
                            <div class="row">
                                <div class="width-100">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('name','Model Name')}}
                                                {{Form::text('name',null,['class'=>'js-primary form-control'])}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('total_user','Total User')}}
                                                    {{Form::number('total_user',null,['class'=>'js-primary form-control'])}}
                                                </div>
                                            </div>
                                        </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('machine_name','Machine Name')}}
                                                    {{Form::checkbox('machine_name',1,1,['class'=>'js-primary'])}}
                                                </div>
                                            </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('machine_no','Machine Number')}}
                                                {{Form::checkbox('machine_no',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('worker_account','Worker Account')}}
                                                {{Form::checkbox('worker_account',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('machine_group','Machine Group')}}
                                                {{Form::checkbox('machine_group',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                            {{Form::label('bonus','Bonus')}}
                                                {{Form::checkbox('bonus',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('disconnect_machine','Disconnect Machine')}}
                                                {{Form::checkbox('disconnect_machine',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('range_color','Range Color')}}
                                                {{Form::checkbox('range_color',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('report_hour_12','Report Hour 12')}}
                                                {{Form::checkbox('report_hour_12',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('report_hour_6','Report Hour 6')}}
                                                {{Form::checkbox('report_hour_6',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('report_hour_3','Report Hour 3')}}
                                                {{Form::checkbox('report_hour_3',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('report_min_5','Report Minute 5')}}
                                                {{Form::checkbox('report_min_5',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('report_avg','Report Average')}}
                                                {{Form::checkbox('report_avg',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('report_avg_weekly','Report Average Weekly')}}
                                                {{Form::checkbox('report_avg_weekly',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('report_total','Report_total')}}
                                                {{Form::checkbox('report_total',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('report_salary','Report Salary')}}
                                                {{Form::checkbox('report_salary',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('setting','Setting')}}
                                                {{Form::checkbox('setting',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('head','Head')}}
                                                {{Form::checkbox('head',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('tb','Thread Break')}}
                                                {{Form::checkbox('tb',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('max_rpm','Max RPM')}}
                                                {{Form::checkbox('max_rpm',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('live_rpm','Live RPM')}}
                                                {{Form::checkbox('live_rpm',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('stop_time','Stop Time')}}
                                                {{Form::checkbox('stop_time',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('last_stop_time','Last Stop Time')}}
                                                {{Form::checkbox('last_stop_time',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('buzzer','Buzzer')}}
                                                {{Form::checkbox('buzzer',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('shift','Shift')}}
                                                {{Form::checkbox('shift',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('supervisor','Supervisor')}}
                                                {{Form::checkbox('supervisor',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('remark','Remark')}}
                                                {{Form::checkbox('remark',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('phone','Phone')}}
                                                {{Form::checkbox('phone',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('camera','Camera')}}
                                                {{Form::checkbox('camera',1,1,['class'=>'js-primary'])}}
                                            </div>
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::submit("Save changes",['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ url('admin/permission') }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Back</a>
                                    
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
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var elemsingle = document.querySelector('.js-single');
        var switchery = new Switchery(elemsingle, { color: '#4099ff', jackColor: '#fff' });
    });
</script>

@endsection