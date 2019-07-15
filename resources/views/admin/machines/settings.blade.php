@extends('layouts.admin.index')
@section('page_title','Welcome')
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
    <div class="page-body">

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-right"><i class="fa fa-window-maximize full-card"></i></div>
                    </div>
                    <div class="card-block">
                        <div class="bs-example grid-layout">
                            {{ Form::model($customer,array('route' => ['admin.customers.settings.general_settings',$customer->id],'id'=>'frm_customer')) }}
                            <div class="row">
                                <div class="width-100">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs md-tabs " role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#general_settings" role="tab"><i class="fa fa-lg fa-cogs"></i> General Settings</a>
                                            <div class="slide"></div>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#sms_recharge" role="tab"><i class="fa fa-lg fa-envelope"></i> SMS Recharge</a>
                                            <div class="slide"></div>
                                        </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content card-block">
                                        <div class="tab-pane active" id="general_settings" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {{Form::label('sms_notification_status','SMS Notification')}}
                                                        {{Form::checkbox('sms_notification_status',1,$customer->sms_notification_status,['id'=>'sms_notification_status','class'=>'js-primary'])}}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {{Form::label('whatsapp_notification_status','WhatsApp Notification')}}
                                                        {{Form::checkbox('whatsapp_notification_status',1,$customer->whatsapp_notification_status,['id'=>'whatsapp_notification_status','class'=>'js-primary'])}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="sms_recharge" role="tabpanel">
                                            In Progress...
                                        </div>
                                    </div>
                                </div>

                                
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::submit("Save changes",['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('admin.customers.list') }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Back</a>
                                    
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
{!! JsValidator::formRequest('App\Http\Requests\UserRequest','#frm_customer'); !!}
@endsection