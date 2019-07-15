@extends('layouts.user.index')
@section('page_title','List of all Machines')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">List of all Machines</h5>
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
                        {{ Form::model($machine_search,array('route' => 'machines.list')) }}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('machine_number','Machine Number')}}
                                        {{Form::text('machine_number',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-8 p-t-25">
                                    {{ Form::submit('Search',['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('machines.list',['reset'=>1]) }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Reset</a>
                                </div>

                            </div>
                        {{ Form::close() }}
                        <div class="card-header-right">
                            <i class="fa fa-window-maximize full-card"></i>
                        </div>
                    </div>
                    <div class="card-block">
                        {{ Form::model([],array('route' => ['machines.list'],'id'=>'frm_machine')) }}
                        <div class="table-responsive">
                            <table class="table machineTable">
                                <thead>
                                    <tr>
                                        @if(hasAccess('buzzer'))
                                        {{-- <th>
                                            <div class="border-checkbox-section">
                                                <div class="border-checkbox-group border-checkbox-group-primary">
                                                    <input id="chk_all" type="checkbox" class="border-checkbox">
                                                    <label for="chk_all" class="border-checkbox-label"></label>
                                                </div>
                                            </div>
                                        </th> --}}
                                        @endif
                                        <th>@sortablelink('machine_number','Machine Number')</th>
                                        @if(hasAccess('machine_name'))
                                            <th>@sortablelink('machine_name','Machine Name')</th>
                                        @endif
                                        @if(hasAccess('machine_group'))
                                            <th>Group</th>
                                        @endif
                                        @if(hasAccess('worker_account'))
                                            <th>Day Worker</th>
                                            <th>Night Worker</th>
                                        @endif
                                        {{-- <th>Actions</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($machines as $key => $machine)
                                        <tr>
                                            @if(hasAccess('buzzer'))
                                            {{-- <td>
                                                <div class="border-checkbox-section">
                                                    <div class="border-checkbox-group border-checkbox-group-primary">
                                                        <input id="chk_{{$key}}" type="checkbox" name="selected_machine[]" value="{{$machine->id}}" class="border-checkbox select-chk">
                                                        <label for="chk_{{$key}}" class="border-checkbox-label"></label>
                                                    </div>
                                                </div>
                                            </td> --}}
                                            @endif
                                            <th>
                                                {{-- <a href="#" class="change-machine-name" data-type="number" data-value="{{ $machine->machine_number }}" data-name='type' data-pk="{{ $machine->id }}" data-url="{{ route('change.machine.number') }}" data-title="Enter Machine Number">
                                                    {{ $machine->machine_number }}
                                                </a> --}}
                                                {{ $machine->machine_number }}
                                                
                                            </th>
                                             @if(hasAccess('machine_name'))
                                                 <td>
                                                    <a href="#" class="change-machine-name" data-type="text" data-name='type' data-pk="{{ $machine->id }}" data-url="{{ route('change.machine.name') }}" data-title="Enter Machine Name">
                                                        {{ $machine->machine_name }}
                                                    </a>
                                                </td>
                                            @endif
                                            @if(hasAccess('machine_group'))
                                                <td>
                                                    @if ($machine->machine_group)
                                                        <a href="#" class="change-machine-group" data-type="select" data-name='type' data-value="{{ $machine->machine_group->id }}" data-pk="{{ $machine->id }}" data-url="{{ route('change.machine.group') }}" data-title="Select Machine Group">
                                                            {{ $machine->machine_group->group_name }}
                                                        </a>
                                                    @else
                                                        <a href="#" class="change-machine-group" data-type="select" data-name='type' data-value="0" data-pk="{{ $machine->id }}" data-url="{{ route('change.machine.group') }}" data-title="Select Machine Group">
                                                            
                                                        </a>
                                                    @endif
                                                </td>
                                            @endif
                                            @if(hasAccess('worker_account'))
                                                <td>
                                                    @if ($machine->day_worker)
                                                        <a href="#" class="change-machine-day_worker" data-type="select" data-name='type' data-machine_number="{{ $machine->machine_number }}" data-value="{{ $machine->day_worker->id }}" data-pk="{{ $machine->id }}" data-url="{{ route('change.machine.worker',[1]) }}" data-title="Select Worker">
                                                            {{ $machine->day_worker->first_name . ' ' . $machine->day_worker->last_name }}
                                                        </a>
                                                    @else
                                                        <a href="#" class="change-machine-day_worker" data-type="select" data-name='type' data-machine_number="{{ $machine->machine_number }}" data-value="0" data-pk="{{ $machine->id }}" data-url="{{ route('change.machine.worker',[1]) }}" data-title="Select Worker">
                                                            
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($machine->night_worker)
                                                        <a href="#" class="change-machine-night_worker" data-type="select" data-name='type' data-machine_number="{{ $machine->machine_number }}" data-value="{{ $machine->night_worker->id }}" data-pk="{{ $machine->id }}" data-url="{{ route('change.machine.worker',[2]) }}" data-title="Select Worker">
                                                            {{ $machine->night_worker->first_name . ' ' . $machine->night_worker->last_name }}
                                                        </a>
                                                    @else
                                                        <a href="#" class="change-machine-night_worker" data-type="select" data-name='type' data-machine_number="{{ $machine->machine_number }}" data-pk="{{ $machine->id }}" data-value="0" data-url="{{ route('change.machine.worker',[2]) }}" data-title="Select Worker">
                                                            
                                                        </a>
                                                    @endif
                                                </td>
                                            @endif
                                            {{-- <td> --}}
                                                {{-- <a href="{{ route('machines.settings.buzzer',encrypt_str($machine->id)) }}" data-toggle="tooltip" data-placement="top" data-original-title="Machine Settings"><i class="fa fa-lg fa-cog text-warning"></i></a> --}}
                                            {{-- </td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {!! $machines->appends(\Request::except('page'))->render() !!}
                        </div>
                        @if(hasAccess('buzzer'))
                           {{--  <div class="row">
                                <div class="col-md-12">
                                    <button type="button" id="buzzer-settings" class="btn btn-grd-primary btn-sm md-trigger" data-modal="modal-11">Buzzer Setting</button>
                                </div>
                            </div> --}}
                        
                            {{-- Buzzer Setting Modal Pop-up (for right side -> button-page)--}}
                            <div class="md-modal md-effect-11" id="modal-11">
                                <div class="md-content">
                                    <h3>Buzzer Setting</h3>
                                    <div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="text-info"> <i class="fa fa-info-circle"></i> Machine will buzz when below time will match while machine is stopped</label>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    {{ Form::label('default_stop','Default') }}
                                                    <div class="input-group">
                                                        <span class="input-group-addon">Minutes</span>
                                                        {{Form::select('default_stop_minutes',default_buzz_time_minutes(),0,['class'=>'form-control'])}}
                                                        <span class="input-group-addon">Seconds</span>
                                                        {{Form::select('default_stop_seconds',default_buzz_time_seconds(),0,['class'=>'form-control'])}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    {{ Form::label('10_min','After stopped for 10 Minutes') }}
                                                    {{Form::select('10_min',buzz_time(),$buzzer_time_data['10_min'],['class'=>'form-control'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    {{ Form::label('20_min','After stopped for 20 Minutes') }}
                                                    {{Form::select('20_min',buzz_time(),$buzzer_time_data['20_min'],['class'=>'form-control'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    {{ Form::label('30_min','After stopped for 30 Minutes') }}
                                                    {{Form::select('30_min',buzz_time(),$buzzer_time_data['30_min'],['class'=>'form-control'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <button id="save_buzzer_setting" class="btn hor-grd btn-grd-info btn-round">Save Changes</button>
                                    </div>
                                </div>
                            </div>
                            <div class="md-overlay"></div>
                            {{-- Buzzer Setting Modal Pop-up --}}
                        @endif
                        {{ Form::close() }}

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
        
    <script type="text/javascript">
        var groups = <?php echo json_encode($machine_group)?>;
        var worker = <?php echo json_encode($worker)?>;
        var assignWorker = 0;
        console.log(worker);
        $(function(){

            $.ajaxSetup({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });



            $(document).on('click','.editable-submit',function(){
                $form = $(this).parents('form');
                var formType = $(this).parents('.editable-popup').find('.popover-header').text();
                if(formType == "Select Worker"){
                    var workerId = $form.find('select').val();
                    $worker = $('.machineTable').find('a[data-value="'+workerId+'"]');
                    if($worker.length != 0){
                        var machine_number = $worker.data('machine_number');
                        var r = confirm("This worker already assign to machine number "+machine_number+" \nAre you sure to remove from their ?");
                        if (r == true) {
                            return true;
                        } else {
                            return false;
                        }
                    }else{
                        return true;
                    }
                }
            });

            $('.change-machine-group').editable({
                source: groups,
                success: function(response, newValue) {
                    $.notify(response,'success');
                },
                error: function(response, newValue) {
                    $(".editable-error-block").html('');
                    $.notify(response.responseJSON,'error');
                 },
            });

            $('.change-machine-day_worker').editable({
                source: worker,
                success: function(response, newValue) {
                    $.notify(response,'success');
                    location.reload();
                },
                error: function(response, newValue) {
                    $(".editable-error-block").html('');
                    $.notify(response.responseJSON,'error');
                 },
            });

            $('.change-machine-night_worker').editable({
                source: worker,
                success: function(response, newValue) {
                    $.notify(response,'success');
                    location.reload();
                },
                error: function(response, newValue) {
                    $(".editable-error-block").html('');
                    $.notify(response.responseJSON,'error');
                 },
            });

            $('.change-machine-name').editable({
                success: function(response, newValue) {
                    $.notify(response,'success');
                },
                error: function(response, newValue) {
                    $(".editable-error-block").html('');
                    $.notify(response.responseJSON,'error');
                 },
            });

            $('.change-machine-number').editable({
                success: function(response, newValue) {
                    $.notify(response,'success');
                },
                error: function(response, newValue) {
                    $(".editable-error-block").html('');
                    $.notify(response.responseJSON,'error');
                 },
            });

            $('#chk_all').change(function(event) {
                var _chk = $(this).is(':checked');
                console.log(_chk);
                $('.select-chk').prop('checked',_chk);
            });

            /* $('a.change-machine-day_worker,a.change-machine-night_worker').click(function(){
                assignWorker = $(this).data('value');
                console.log(assignWorker);
            }); */
            $('#save_buzzer_setting').click(function(event) {
                var _chk = $('.select-chk:checked').length;
                if(_chk==0){
                    $.notify('Please select at least one machine','error');
                    event.preventDefault();
                }
            });

        });
    </script>
@endsection
