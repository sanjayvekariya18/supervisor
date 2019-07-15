@php 
    use Illuminate\Support\Str;
@endphp
@extends('layouts.user.dashboard')

@section('page_title','Dashboard')
@section('content')
<style type="text/css">
    .card{
        background-color: rgba(255, 255, 255, 0.6) !important;
        color: black !important;
    }
    .card a{
        color: black !important;
    }
</style>
    <meta name="csrf-token" content="{{ csrf_token() }}">	
	<div class="page-body machineTable">
        <div class="row">
            @foreach ($machines as $machine)
                <div id="{{ $machine->machine_id }}" class="machine-element sup-wrapper" data-group="{{ $machine->group_id }}">
                    <div class="card">
                        <div class="row text-center sup-block">
                            <div class="text-left sup-total-nabar"><span class="machine-no">{{$machine->machine_number}}</span></div>
                            <div class="col-md-6 col-sm-8 text-left sup-timmer">
                                <div class="sup-timmer-inner">
                                    @if(hasAccess('last_stop_time'))
                                        <span class="f-22 stop_time">{{ $machine->stop_time }}</span> &nbsp; 
                                    @endif
                                    @if(hasAccess('buzzer'))
                                        <span class=""><i class="fa fa-bell f-22 text-info alert-machine" data-idchange="{{ $machine->machine_id }}" data-id="{{ $machine->machine_id }}"></i></span>
                                    @endif
                                </div>
                            </div>
                            @if(hasAccess('buzzer'))
                                <div class="col-md-6 col-sm-4 sup-menu"><i class="fa fa-lg fa-ellipsis-v"></i></div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-12 supheer-inner">
                                <div class="row machine_running" style="display: flex;">
                                    <div class="col-md-6 text-center supheer-title">
                                        <div class="row">
                                            <div class="col-md-12 sup-title">
                                                <span class="f-18">
                                                    @if(hasAccess('worker_account'))
                                                <a href="javascript:void(0)" class="change-machine-worker f-18" data-machine_number="{{$machine->machine_number}}" data-shift="{{$machine->shift}}" data-id="{{$machine->id}}" data-toggle="modal" data-day="{{$machine->day_worker_id}}" data-night="{{$machine->night_worker_id}}">
                                                            @if ($machine->worker)
                                                                {{ $machine->worker->first_name}}
                                                            @else
                                                                {{ 'Select' }}
                                                            @endif
                                                        </a>
                                                    @else
                                                        @if ($machine->worker)
                                                            {{ $machine->worker->first_name}}
                                                        @else
                                                            {{ '' }}
                                                        @endif
                                                    @endif
                                                </span>
                                            </div>
                                            @if($machine->shift==1)
                                                <div class="col-md-12 sup-img"><span class="f-17 shift-icon">{{ HTML::image('icon/day_ic.svg', 'Shift', array('class'=>'shift-icon')) }}</span></div>
                                            @else
                                                <div class="col-md-12"><span class="f-17 shift-icon">{{ HTML::image('icon/night_ic.svg', 'Shift', array('class'=>'shift-icon')) }}</span></div>
                                            @endif
                                            <div class="col-md-12 sup-dec">
                                                <span class="f-18"  data-toggle="tooltip" data-placement="top" data-original-title="{{ $machine->machine_name }}">
                                                    @if(hasAccess('machine_name'))
                                                        {{Str::limit($machine->machine_name, 8)}} 
                                                    @endif
                                                    @if(hasAccess('head'))
                                                        @if ($show_working_head==1)
                                                            (<span class="working_head" >{{ !empty($machine->working_head) && $machine->working_head > 3 ? 'On' : "Off"}}</span>)
                                                        @endif
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 text-right sup-left-title">
                                        <div class="row">
                                            <div class="col-md-12 sup-left-title-block">
                                                @php ($color_code = '#fff')

                                                @foreach ($color_range as $key => $range)
                                                    @if ($machine->stitches >= $range['to'])
                                                        @php ($color_code = $range['color_code'])
                                                    @endif
                                                    @if (($machine->stitches >= $range['from']) && ($machine->stitches <= $range['to']))
                                                        @php ($color_code = $range['color_code'])
                                                    @endif
                                                @endforeach
                                                <span class="f-22 stitches f-w-600" style="color: {{$color_code}}">{{ !empty($machine->stitches) ? $machine->stitches : 0}}</span>

                                            </div>
                                            <div class="col-md-12 sup-left-dec-block">
                                                @if(hasAccess('live_rpm'))
                                                    <span class="f-18 rpm">{{ !empty($machine->rpm) ? $machine->rpm : 0}}</span>
                                                @endif
                                                @if(hasAccess('max_rpm'))
                                                    (<span class="f-16 max_rpm">{{ !empty($machine->max_rpm) ? $machine->max_rpm : 0}}</span>)
                                                @endif
                                            </div>
                                            @if(hasAccess('tb'))
                                                @if ($show_thread_break==1)
                                                    <div class="col-md-12 sup-left-dec-block"><span class="f-16 thred_break">{{ !empty($machine->thred_break) ? $machine->thred_break : 0}}</span></div>
                                                @endif
                                            @endif
                                            <div class="col-md-12 sup-left-timmer-block"><span class="f-16 total_stop_time">{{ !empty($machine->total_stop_time) ? date("H:i:s",strtotime($machine->total_stop_time)) : '00:00:00'}}</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row machine_disconnected" style="display: none;height: 109px;">
                                    <div class="col-md-12  m-t-25 text-center machine-stop">
                                        <div class="">
                                            <span class="f-18 ">Machine is disconnected since</span>
                                            <br>
                                            <span class="f-22 f-w-600 disconnected_time machine-stop">00-00-0000 00:00:00 --</span>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center sup-total-timmer">
                            <div class="col-md-12 sup-total-timmer-inner"><span class="f-17 current_time">{{ date('Y-m-d h:i:s A',strtotime($machine->updated_at)) }}</span></div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
{{ Form::model([],array('route' => 'customer.dashboard','id' => "changeWorkerForm")) }}
<input type="hidden" name="machine_id" id="machine_id">
<div class="modal fade" id="worker-change" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Change Worker</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Shift</span>
                                <select name="shift" class="form-control" id="shift">
                                    <option value="1">Day</option>
                                    <option value="2">Night</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon">Worker</span>
                                {{Form::select('worker_id',$worker,0,['class'=>'form-control','id'=>'worker_id'])}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" id="changeWorkerBtn">Save changes</button>
            </div>
        </div>
    </div>
</div>
{{ Form::close() }}
@include('elements.dashboard.group_setting')
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>
<script type="text/javascript">
    
    var color_range = <?php echo json_encode($color_range);?>;
    var _host = '{{ env('SOCKET_IO_HOST') }}';
    var socket = io.connect(_host);
    var room = "{{$cust_id}}";
    
    socket.on('connect', function() {
       // Ask server to connect with Customer for Real time data sync
       socket.emit('room', room);
    //    console.log("Connected...");
    });

    setInterval(() => {
        socket.emit('disconnected_machine', room);
    }, 5000);

    socket.on('disconnected_machine', function(data) {
        $.each(data, function(index, val) {
            var m_id = val.machine_id;
            $("#"+m_id).find(".machine_running").hide();
            $("#"+m_id).find(".disconnected_time").html(val.last_connected);
            $("#"+m_id).find(".machine_disconnected").show();
        });
    });

    socket.on('machine_details', function(data) {
        if (typeof data.machine_detail.machine_id !== 'undefined') {

            var m_id = data.machine_detail.machine_id;

            $("#"+m_id).find(".machine_running").show();
            $("#"+m_id).find(".disconnected_time").html('');
            $("#"+m_id).find(".machine_disconnected").hide();

            var stitches = parseInt(data.machine_detail.stitches);
            $("#"+m_id).find(".stitches").html(stitches);
            $("#"+m_id).find(".rpm").html(data.machine_detail.rpm);
            $("#"+m_id).find(".max_rpm").html(data.machine_detail.max_rpm);
            $("#"+m_id).find(".total_stop_time").html(data.machine_detail.total_stop_time);
            $("#"+m_id).find(".stop_time").html(data.machine_detail.stop_time);
            $("#"+m_id).find(".thred_break").html(data.machine_detail.thred_break);

            var worker_name = "Select";
            if (data.machine_detail.worker_name != null) {
                worker_name = data.machine_detail.worker_name;
            }
            $("#"+m_id).find(".change-machine-worker").html(worker_name);
            $("#"+m_id).find(".current_time").html(data.machine_detail.current_date_time);

            // Change
            $("#"+m_id).find(".working_head").html(data.machine_detail.working_head_str);
            $("#"+m_id).find(".rpm").html(data.machine_detail.new_rpm);

            $.each(color_range, function(color_code, range) {
                // Apply last formula to stitches
                if (stitches  >= range.to) {
                    $("#"+m_id).find(".stitches").css('color', range.color_code)
                }

                if ((stitches >= range.from) && (stitches  <= range.to)) {
                    $("#"+m_id).find(".stitches").css('color', range.color_code)
                }

            });

            if (data.machine_detail.shift==1) {
                $("#"+m_id).find(".shift-icon").html('{{ HTML::image('icon/day_ic.svg', 'Shift', array('class'=>'shift-icon')) }}');
            }else{
                $("#"+m_id).find(".shift-icon").html('{{ HTML::image('icon/night_ic.svg', 'Shift', array('class'=>'shift-icon')) }}');
            }

            // Check if device is buzzing
            if (typeof data.machine_detail.is_buzzing != 'undefined' && data.machine_detail.is_buzzing == 1) {
                $("#"+m_id).find('.card').addClass('buzzer-alert');
                // $("#"+m_id).find(".alert-machine").addClass('buzzer-alert');
            }else{
                // $("#"+m_id).find(".alert-machine").removeClass('buzzer-alert');
                $("#"+m_id).find('.card').removeClass('buzzer-alert');
            }

            if (data.machine_detail.stop_time !='Running...') {
                $("#"+m_id).find('.card').addClass('machine-card');
            }else{
                $("#"+m_id).find('.card').removeClass('machine-card');
            }

        }
    });

    var worker = <?php echo json_encode($worker)?>;
    var timeoutId = 0;
    $(function(){

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });

        $('.change-machine-worker').click(function() {
            
            var machine_id = $(this).data('id');
            var day_id = $(this).data('day');
            var night_id = $(this).data('night');
            var shift = $(this).data('shift');
            var worker_id = (shift == 1) ? day_id : night_id;
            $("#machine_id").val(machine_id);
            $("#worker_id").val(worker_id);
            $("#shift").val(shift);

            $("#worker-change").modal('show');

        });
        $('#changeWorkerBtn').on('click',function(e){
            var workerId = $("#worker_id").val();
            var day_worker = $('.machineTable').find('a[data-day="'+workerId+'"]');
            var night_worker = $('.machineTable').find('a[data-night="'+workerId+'"]');
            
            if(day_worker.length > 0){
                // console.log(day_worker);
                var machineId = day_worker.data('machine_number');
            }else if(night_worker.length > 0){
                // console.log(night_worker);
                var machineId = night_worker.data('machine_number');
            }
            /* console.log(workerId);
            console.log(machineId);
            console.log(day_worker.length);
            console.log(night_worker.length); */
            if(day_worker.length != 0 || night_worker.length != 0){
                var r = confirm("This worker already assign to machine number "+machineId+" \nAre you sure to remove from their ?");
                if (r == true) {
                    $('#changeWorkerForm').submit();
                } else {
                    return false;
                }
            }else{
                $('#changeWorkerForm').submit();
            }
            /* $.ajax({
                type:"POST",
                url : $(this).attr('action'),
                dataType : "json",
                data:$(this).serialize(),
                success:function(res){
                    if(!res.status){
                        
                    }
                }
            }); */
        });
        
        $('.alert-machine').on('mousedown', function() {
            var machine_no = $(this).attr('data-id');
            var buzz = 1;
            socket.emit('buzzer_on', machine_no);
        }).on('mouseup mouseleave', function() {
            var machine_no = $(this).attr('data-id');
            var buzz = 0;
            socket.emit('buzzer_off', machine_no);
        });
        
    });
</script>        
@endsection