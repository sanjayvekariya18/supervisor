@extends('layouts.user.index')
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
                            <div class="col-lg-12">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs  tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#stoptab" role="tab">MACHINE STOP</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#rpmtab" role="tab">MACHINE RPM</a>
                                    </li>
                                </ul>
                                <!-- Tab panes -->
                                <form action="{{url('settings/machine_setting')}}" method="post">
                                @csrf
                                <div class="tab-content tabs card-block">
                                    <div class="tab-pane active" id="stoptab" role="tabpanel">                                        
                                        <div class="card">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>MACHINE #</th>
                                                            <th><a href="javascript:void(0)" onclick="$('.s_time_speed').val($('.s_time_speed:first').val());">TIME <sub>(minute)</sub></a></th>
                                                            <th><a href="javascript:void(0)" onclick="$('.s_after_duration').val($('.s_after_duration:first').val());">AFTER DURATION <sub>(minute)</sub></a></th>
                                                            <th><a href="javascript:void(0)" onclick="$('.s_buzz_time').val($('.s_buzz_time:first').val());">BUZZ TIME <sub>(minute)</sub></a></th>
                                                            <th><a href="javascript:void(0)" onclick="$('.s_color').val($('.s_color:first').val());">COLOR</th>
                                                            <th>NOTIFICATION</th>
                                                            <th>SMS</th>
                                                            <th>STATUS</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($machine_setting_list as $machine_number => $machine)
                                                        <tr>
                                                            <th scope="row">                                                                
                                                            {{$machine_number}}
                                                            </th>
                                                            <td>{{Form::text("settings[$machine_number][stop][time_speed]",isset($machine['settings']->stop->time_speed)?$machine['settings']->stop->time_speed:null,["class"=>"js-primary form-control col-md-6 s_time_speed"])}}</td>
                                                            <td>{{Form::text("settings[$machine_number][stop][after_duration]",isset($machine['settings']->stop->after_duration)?$machine['settings']->stop->after_duration:null,["class"=>"js-primary form-control col-md-6 s_after_duration"])}}</td>
                                                            <td>{{Form::text("settings[$machine_number][stop][buzz_time]",isset($machine['settings']->stop->buzz_time)?$machine['settings']->stop->buzz_time:null,["class"=>"js-primary form-control col-md-6 s_buzz_time"])}}</td>
                                                            <td>{{Form::color("settings[$machine_number][stop][color]",isset($machine['settings']->stop->color)?$machine['settings']->stop->color:null,["class"=>"s_color"])}}</td>
                                                            <td>{{Form::checkbox("settings[$machine_number][stop][notification]",1,isset($machine['settings']->stop->notification)?1:0,["class"=>"js-primary s_notification"])}}</td>
                                                            <td>{{Form::checkbox("settings[$machine_number][stop][sms]",1,isset($machine['settings']->stop->sms)?1:0,["class"=>"js-primary"])}}</td>
                                                            <td>{{Form::checkbox("settings[$machine_number][stop][status]",1,isset($machine['settings']->stop->status)?1:0,["class"=>"js-primary"])}}</td>
                                                        </tr>  
                                                    @endforeach                                                          
                                                    </tbody>
                                                </table>
                                            </div>                                            
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="rpmtab" role="tabpanel">
                                        <div class="card">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>MACHINE #</th>
                                                            <th><a href="javascript:void(0)" onclick="$('.r_time_speed').val($('.r_time_speed:first').val());">SPEED</a></th>
                                                            <th><a href="javascript:void(0)" onclick="$('.r_after_duration').val($('.r_after_duration:first').val());">AFTER DURATION <sub>(minute)</sub></a></th>
                                                            <th><a href="javascript:void(0)" onclick="$('.r_buzz_time').val($('.r_buzz_time:first').val());">BUZZ TIME <sub>(minute)</sub></a></th>
                                                            <th><a href="javascript:void(0)" onclick="$('.r_color').val($('.r_color:first').val());">COLOR</th>
                                                            <th>NOTIFICATION</th>
                                                            <th>SMS</th>
                                                            <th>STATUS</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($machine_setting_list as $machine_number => $machine)
                                                        <tr>
                                                            <th scope="row">
                                                            {{$machine_number}}
                                                            </th>
                                                            <td>{{Form::text("settings[$machine_number][rpm][time_speed]",isset($machine['settings']->rpm->time_speed)?$machine['settings']->rpm->time_speed:null,["class"=>"js-primary form-control col-md-6 r_time_speed"])}}</td>
                                                            <td>{{Form::text("settings[$machine_number][rpm][after_duration]",isset($machine['settings']->rpm->after_duration)?$machine['settings']->rpm->after_duration:null,["class"=>"js-primary form-control col-md-6 r_after_duration"])}}</td>
                                                            <td>{{Form::text("settings[$machine_number][rpm][buzz_time]",isset($machine['settings']->rpm->buzz_time)?$machine['settings']->rpm->buzz_time:null,["class"=>"js-primary form-control col-md-6 r_buzz_time"])}}</td>
                                                            <td>{{Form::color("settings[$machine_number][rpm][color]",isset($machine['settings']->rpm->color)?$machine['settings']->rpm->color:null,["class"=>"r_color"])}}</td>
                                                            <td>{{Form::checkbox("settings[$machine_number][rpm][notification]",1,isset($machine['settings']->rpm->notification)?1:0,["class"=>"js-primary"])}}</td>
                                                            <td>{{Form::checkbox("settings[$machine_number][rpm][sms]",1,isset($machine['settings']->rpm->sms)?1:0,["class"=>"js-primary"])}}</td>
                                                            <td>{{Form::checkbox("settings[$machine_number][rpm][status]",1,isset($machine['settings']->rpm->status)?1:0,["class"=>"js-primary"])}}</td>
                                                        </tr>  
                                                    @endforeach                                                          
                                                    </tbody>
                                                </table>
                                            </div>                                            
                                        </div>
                                    </div>                                                                            
                                </div>
                                {{ Form::submit("Save changes",["class"=>"btn hor-grd btn-grd-info btn-round"]) }}  
                                </form>  
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
@endsection
