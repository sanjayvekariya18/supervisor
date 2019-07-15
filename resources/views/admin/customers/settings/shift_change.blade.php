@extends('layouts.admin.index')
@section('page_title','Shift Change')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">
                    Customer Settings : <b class="text-danger">{{ $customer->first_name . ' ' . $customer->last_name }}</b>
                </h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ route('admin.customers.list') }}" class="text-danger"><strong>Back to List</strong></a>
            </div>
        </div>
    </div>
    <!-- Page header end -->

    <!-- Page body start -->
    <div class="page-body m-t-40">

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
                                    @include('admin.customers.settings.nav_tabs', ['current_tab' => $current_tab,'customer_id'=>$customer->id])
                                    <!-- Tab panes -->
                                    <div class="tab-content card-block">
                                        {{ Form::model([],array('route' => ['admin.customers.settings.shift.change',$customer->id],'id'=>'frm_color_range')) }}
                                        <div class="tab-pane {{ $current_tab=='shift_change' ? 'active' : '' }}" id="shift_change" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {{Form::label('','Day Shift starts at')}}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {{Form::select('day_shift',day_shifts(),$day_shift,['class'=>'form-control'])}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {{Form::label('','Night Shift starts at')}}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {{Form::select('night_shift',night_shifts(),$night_shift,['class'=>'form-control'])}}
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