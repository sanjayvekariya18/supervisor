@extends('layouts.admin.index')
@section('page_title','General Settings')
@section('content')

    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">
                    Machine Settings
                </h5>
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
                                    <form action="{{url('admin/setting/machine_setting')}}" method="post">
                                        @csrf
                                        <div class="card">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>TIME/SPEED</th>
                                                            <th>AFTER DURATION</th>
                                                            <th>BUZZ TIME</th>
                                                            <th>COLOR</th>
                                                            <th>NOTIFICATION</th>
                                                            <th>SMS</th>
                                                            <th>STATUS</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">
                                                            {{Form::hidden('settings[stop][machine_factor]','stop',['class'=>'js-primary form-control'])}}
                                                            MACHINE STOP
                                                            </th>
                                                            <td>{{Form::text('settings[stop][time_speed]',isset($settingData->stop->time_speed)?$settingData->stop->time_speed:null,['class'=>'js-primary form-control col-md-6'])}}</td>
                                                            <td>{{Form::text('settings[stop][after_duration]',isset($settingData->stop->after_duration)?$settingData->stop->after_duration:null,['class'=>'js-primary form-control col-md-6'])}}</td>
                                                            <td>{{Form::text('settings[stop][buzz_time]',isset($settingData->stop->buzz_time)?$settingData->stop->buzz_time:null,['class'=>'js-primary form-control col-md-6'])}}</td>
                                                            <td>{{Form::color('settings[stop][color]',isset($settingData->stop->color)?$settingData->stop->color:null,['class'=>''])}}</td>
                                                            <td>{{Form::checkbox('settings[stop][notification]',1,isset($settingData->stop->notification)?1:0,['class'=>'js-primary'])}}</td>
                                                            <td>{{Form::checkbox('settings[stop][sms]',1,isset($settingData->stop->sms)?1:0,['class'=>'js-primary'])}}</td>
                                                            <td>{{Form::checkbox('settings[stop][status]',1,isset($settingData->stop->status)?1:0,['class'=>'js-primary'])}}</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">
                                                            {{Form::hidden('settings[rpm][machine_factor]','rpm',['class'=>'js-primary form-control'])}}
                                                            MACHINE RPM
                                                            </th>
                                                            <td>{{Form::text('settings[rpm][time_speed]',isset($settingData->rpm->time_speed)?$settingData->rpm->time_speed:null,['class'=>'js-primary form-control col-md-6'])}}</td>
                                                            <td>{{Form::text('settings[rpm][after_duration]',isset($settingData->rpm->after_duration)?$settingData->rpm->after_duration:null,['class'=>'js-primary form-control col-md-6'])}}</td>
                                                            <td>{{Form::text('settings[rpm][buzz_time]',isset($settingData->rpm->buzz_time)?$settingData->rpm->buzz_time:null,['class'=>'js-primary form-control col-md-6'])}}</td>
                                                            <td>{{Form::color('settings[rpm][color]',isset($settingData->rpm->color)?$settingData->rpm->color:null,['class'=>''])}}</td>
                                                            <td>{{Form::checkbox('settings[rpm][notification]',1,isset($settingData->rpm->notification)?1:0,['class'=>'js-primary'])}}</td>
                                                            <td>{{Form::checkbox('settings[rpm][sms]',1,isset($settingData->rpm->sms)?1:0,['class'=>'js-primary'])}}</td>
                                                            <td>{{Form::checkbox('settings[rpm][status]',1,isset($settingData->rpm->status)?1:0,['class'=>'js-primary'])}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>                                            
                                        </div>
                                        {{ Form::submit("Save changes",['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    </form>
                                </div>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
@endsection