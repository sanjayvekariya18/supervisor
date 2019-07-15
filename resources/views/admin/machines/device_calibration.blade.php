@extends('layouts.admin.index')
@section('page_title','Device Calibration')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">Device Calibration</h5>
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
                        {{ Form::model($search,array('route' => 'admin.machines.calibration')) }}
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        {{Form::label('cust_id','Customer ID')}}
                                        {{Form::select('cust_id',$customers,null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-5 p-t-25">
                                    {{ Form::submit('Search',['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('admin.machines.calibration',['reset'=>1]) }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Reset</a>
                                </div>

                            </div>
                        {{ Form::close() }}
                        <div class="card-header-right">
                            <i class="fa fa-window-maximize full-card"></i>
                        </div>
                    </div>
                    <div class="card-block">
                        {{ Form::model($search,array('route' => 'admin.machines.calibration.save')) }}
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center"><label id="connect-status"></label></th>
                                        <th colspan="4" class="text-center bg-info">Head Calibration</th>
                                        <th colspan="4" class="text-center bg-warning">RPM Calibration</th>
                                        <th colspan="3" class="text-center bg-danger">Stop Time Calibration</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">M No</th>
                                        <th class="text-center">Input Head</th>
                                        <th class="text-center">X</th>
                                        <th class="text-center">Y</th>
                                        <th class="text-center">Display Head</th>
                                        <th class="text-center">Input RPM</th>
                                        <th class="text-center">Target RPM</th>
                                        <th class="text-center">X</th>
                                        <th class="text-center">Display RPM</th>
                                        <th class="text-center">Input ST</th>
                                        <th class="text-center">X</th>
                                        <th class="text-center">Display ST</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($machines as $key => $machine)
                                        <tr id="{{$machine->machine_id}}" class="text-center machine-row">
                                            <td>{{ $machine->machine_number }}</td>
                                            <td><label class="input_head">-</label></td>
                                            <td>{{Form::text('machines['.$machine->id.'][head_cal_x]',$machine->head_cal_x,['class'=>'head_cal_x form-control width-65'])}}</td>
                                            <td>{{Form::text('machines['.$machine->id.'][head_cal_y]',$machine->head_cal_y,['class'=>'head_cal_y form-control width-65'])}}</td>
                                            <td><label class="display_head">-</label> &nbsp; (<label class="working_head">-</label>)</td>
                                            <td><label class="input_rpm">-</label></td>
                                            <td>{{Form::text('target_rpm',0,['class'=>'target_rpm form-control width-65'])}}</td>
                                            <td>{{Form::text('machines['.$machine->id.'][rpm_cal]',$machine->rpm_cal,['class'=>'rpm_cal form-control width-65'])}}</td>
                                            <td><label class="display_rpm">-</label></td>
                                            <td><label class="input_stop_time">-</label></td>
                                            <td>{{Form::text('machines['.$machine->id.'][stop_time_cal]',$machine->stop_time_cal,['class'=>'stop_time_cal form-control width-65'])}}</td>
                                            <td><label class="display_stop_time">-</label></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ Form::submit('Save Calibration',['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>
    <script type="text/javascript">
        var _host = '{{ env('SOCKET_IO_HOST') }}';
        var socket = io.connect(_host);

        var room = "{{$cust_id}}";

        // $("#connect-status").html('Connecting...').addClass('text-warning');
        socket.on('connect', function() {
           // Ask server to connect with Customer for Real time data sync
           socket.emit('room', room);
           console.log("Connected...");
           // $("#connect-status").html('Connected...').removeClass('text-warning').addClass('text-green');
        });

        socket.on('machine_details', function(data) {
        console.log(data);
        if (typeof data.machine_detail.machine_id !== 'undefined') {

            var m_id = data.machine_detail.machine_id;

            var head = data.machine_detail.head_from_device;
            var rpm = data.machine_detail.rpm_from_device;
            $("#"+m_id).find(".input_head").html(head).attr('data-vol', head);;
            $("#"+m_id).find(".input_rpm").html(rpm).attr('data-vol',rpm);
            $("#"+m_id).find(".working_head").html(data.machine_detail.working_head);

            var stop_time_from_device = parseInt(data.machine_detail.stop_time_from_device);
            const formatted_stop_time = moment.utc(stop_time_from_device*1000).format('HH:mm:ss');
            $("#"+m_id).find(".input_stop_time").html(formatted_stop_time).attr('data-vol',stop_time_from_device);


            // Trigger Calculation
            $("#"+m_id).find(".head_cal_x").trigger('change');
            $("#"+m_id).find(".rpm_cal").trigger('change');
            $("#"+m_id).find(".stop_time_cal").trigger('change');
            
        }
    });

    jQuery(document).ready(function($) {
        $('.head_cal_x, .head_cal_y').on('change keyup', function () {
                
            var input_head = parseInt($(this).parents('.machine-row').find('.input_head').attr('data-vol'));
            var head_cal_x = parseFloat($(this).parents('.machine-row').find('.head_cal_x').val());
            var head_cal_y = parseFloat($(this).parents('.machine-row').find('.head_cal_y').val());


            // var display_head_1 = Math.ceil((input_head - head_cal_x + (head_cal_y/2)) / head_cal_y);
            var display_head = parseFloat((input_head - head_cal_x + (head_cal_y/2)) / head_cal_y);
            if (display_head=='Infinity') {
                display_head = input_head;
            }
            
            $(this).parents('.machine-row').find('.display_head').html(display_head.toFixed(2));

        });

        $('.rpm_cal').on('change keyup', function () {

            var input_rpm = parseInt($(this).parents('.machine-row').find('.input_rpm').attr('data-vol'));
            var rpm_cal = parseFloat($(this).parents('.machine-row').find('.rpm_cal').val());
            var display_rpm = Math.ceil((input_rpm * rpm_cal) / 10) * 10;

            // var target_rpm = rpm_cal * input_rpm;
            // $(this).parents('.machine-row').find('.target_rpm').val(target_rpm);

            $(this).parents('.machine-row').find('.display_rpm').html(display_rpm);
        });

        $('.target_rpm').on('change keyup', function () {
            var target_rpm = parseInt($(this).parents('.machine-row').find('.target_rpm').val());
            var input_rpm = parseInt($(this).parents('.machine-row').find('.input_rpm').attr('data-vol'));

            var rpm_cal = target_rpm / input_rpm;
            $(this).parents('.machine-row').find('.rpm_cal').val(rpm_cal.toFixed(3)).trigger('change');
        });

        $('.stop_time_cal').on('change keyup', function () {

            var input_stop_time = parseInt($(this).parents('.machine-row').find('.input_stop_time').attr('data-vol'));
            var stop_time_cal = parseFloat($(this).parents('.machine-row').find('.stop_time_cal').val());
            var display_stop_time = input_stop_time * stop_time_cal;

            const formatted_stop_time = moment.utc(display_stop_time*1000).format('HH:mm:ss');

            $(this).parents('.machine-row').find('.display_stop_time').html(formatted_stop_time);
        });
    });




    </script>
@endsection
