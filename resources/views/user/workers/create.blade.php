@extends('layouts.user.index')
@section('page_title','Worker')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">
                    @if ($action=='create')
                        Create a new Worker
                        @php 
                            $disabled='';
                            $form_route = ['workers.add'];
                            $submit_text = 'Create';
                            $next_worker_id = $next_worker_id;
                        @endphp
                    @else
                        Update Worker
                        @php 
                            $disabled='readonly';
                            $form_route=['workers.update',$request->id];
                            $submit_text = 'Update';
                            $next_worker_id = null;
                        @endphp
                    @endif
                </h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ route('workers.list') }}" class="text-danger"><strong>Back to List</strong></a>
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
                            {{ Form::model($request,array('route' => $form_route,'id'=>'frm_worker')) }}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{Form::label('worker_id','Worker ID')}}<b class="err-asterisk"></b>
                                        {{Form::text('worker_id',$next_worker_id,['class'=>'form-control','readonly' => true])}}
                                        @if ($errors->has('worker_id'))
                                            <p class="error">{{ $errors->first('worker_id') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{Form::label('first_name','Worker Name')}}<b class="err-asterisk"></b>
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
                                        {{Form::label('aadhar_card_number','Aadhar Card Number')}}
                                        {{Form::text('aadhar_card_number',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{Form::label('reference_by','Reference By')}}
                                        {{Form::text('reference_by',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                {{-- <div class="col-md-4">
                                    <div class="form-group">
                                        {{Form::label('shift','Shift')}}
                                        {{Form::select('shift',shifts(),null,['class'=>'form-control'])}}
                                    </div>
                                </div> --}}
                                {{-- <div class="col-md-4">
                                    <div class="form-group">
                                        {{Form::label('machine_number','Machine No')}}
                                        {{Form::select('machine_number',$machines_list,null,['class'=>'form-control'])}}
                                    </div>
                                </div> --}}
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {{Form::label('salary','Salary',['id'=>'per_day_salary'])}}
                                        {{Form::text('salary',null,['class'=>'form-control'])}}
                                        @if ($errors->has('salary'))
                                            <p class="error">{{ $errors->first('salary') }}</p>
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
                                        {{-- {{Form::label('assigned_machines','List of all Not assigned Machines, Please select of them to assign to Worker')}}<b class="err-asterisk"></b> --}}
                                        {{-- <div class="border-checkbox-section"> --}}

                                            {{-- @forelse ($machines_list as $key => $machine_details) --}}
                                                {{-- @if ($machine_details['worker_id']==$worker_id AND $worker_id != '0') --}}
                                                    {{-- @php ($_checked = 'checked') --}}
                                                    {{-- @php ($checked_cls = 'primary') --}}
                                                {{-- @else --}}
                                                    {{-- @php ($_checked = '') --}}
                                                    {{-- @php ($checked_cls = 'danger') --}}
                                                {{-- @endif --}}
                                                {{-- <div class="border-checkbox-group border-checkbox-group-{{$checked_cls}} col-md-2"> --}}
                                                    {{-- <input class="border-checkbox" {{ $_checked }} name="assigned_machines[]" type="checkbox" value="{{ $machine_details['id'] }}" id="machine_{{ $machine_details['id'] }}"> --}}
                                                    {{-- <label class="border-checkbox-label" for="machine_{{ $machine_details['id'] }}">{{ $machine_details['machine_number'] .' ('. $machine_details['machine_name'].')' }}</label> --}}
                                                {{-- </div> --}}
                                            {{-- @empty --}}
                                                {{-- <p class="text-warning">All Machines are assigned to other groups</p> --}}
                                            {{-- @endforelse --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::submit($submit_text,['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('workers.list') }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Back</a>
                                    
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
    {!! JsValidator::formRequest('App\Http\Requests\WorkerRequest','#frm_worker'); !!}
    
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#salary').on('change keyup', function () {
                var total_salary = $(this).val();
                var no_of_days = moment().daysInMonth();
                var per_day_salary = Math.round(total_salary / no_of_days);

                if (per_day_salary > 0) {
                    $("#per_day_salary").html("Salary (Per Day - <i class='fa fa-rupee'></i> "+ per_day_salary +")");
                }else{
                    $("#per_day_salary").html("Salary");
                }

            });
            $('#salary').trigger('change');
        });    
    </script>
@endsection