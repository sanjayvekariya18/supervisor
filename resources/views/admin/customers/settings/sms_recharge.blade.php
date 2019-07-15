@php 
    use Illuminate\Support\Str;
@endphp

@extends('layouts.admin.index')
@section('page_title','General Settings')
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
                        <div class="bs-example">
                            
                            <div class="row">
                                <div class="width-100">
                                    @include('admin.customers.settings.nav_tabs', ['current_tab' => $current_tab,'customer_id'=>$customer->id])
                                    <!-- Tab panes -->
                                    <div class="tab-content card-block">
                                        {{ Form::model($customer,array('route' => ['admin.customers.settings.sms_recharge',$customer->id],'id'=>'frm_save')) }}
                                        <div class="tab-pane active" id="general_settings" role="tabpanel">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="text-info">Recharge Customer's SMS Account</label>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                {{Form::label('transaction_type')}}<b class="err-asterisk"></b>
                                                                {{Form::select('transaction_type',transaction_type(),null,['class'=>'form-control'])}}
                                                                @if ($errors->has('transaction_type'))
                                                                    <p class="error">{{ $errors->first('transaction_type') }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                {{Form::label('sms_volume','SMS Volume')}}<b class="err-asterisk"></b>
                                                                {{Form::number('sms_volume',null,['class'=>'form-control'])}}
                                                                @if ($errors->has('sms_volume'))
                                                                    <p class="error">{{ $errors->first('sms_volume') }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                {{Form::label('note')}}<b class="err-asterisk"></b>
                                                                {{Form::text('note',null,['class'=>'form-control'])}}
                                                                @if ($errors->has('note'))
                                                                    <p class="error">{{ $errors->first('note') }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <label class="text-info">SMS Recharge History</label>
                                                    <label class="text-info pull-right">Current Balance : 
                                                        @if ($customer->sms_balance > 0)
                                                            <strong class="text-green">{{ $customer->sms_balance }}</strong>
                                                        @else
                                                            <strong class="text-danger">{{ $customer->sms_balance }}</strong>
                                                        @endif
                                                    </label>
                                                    <div class="row">
                                                        <div class="table-responsive sms-recharge">
                                                            <table class="table table-border">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Transaction Type</th>
                                                                        <th>SMS Volume</th>
                                                                        <th>Note</th>
                                                                        <th>Activity By</th>
                                                                        <th>Activity Time</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @if (!empty($recharge_history->count()))
                                                                        @foreach ($recharge_history as $key => $history)
                                                                            <tr>
                                                                                <td>
                                                                                    @if ($history->transaction_type=='credit')
                                                                                        <span class="text-green">Credited</span>
                                                                                    @else
                                                                                        <span class="text-red f-w-700">Debited</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $history->sms_volume }}</td>
                                                                                <td>
                                                                                    <span data-toggle="tooltip" data-placement="top" data-original-title="{{ $history->note }}">
                                                                                        {{ Str::limit($history->note,25) }}
                                                                                    </span>
                                                                                </td>
                                                                                <td>
                                                                                    @if ($history->admin)
                                                                                        <span data-toggle="tooltip" data-placement="top" data-original-title="{{ $history->admin->first_name . ' ' . $history->admin->last_name }}">
                                                                                            {{ Str::limit($history->admin->first_name . ' ' . $history->admin->last_name,19) }}
                                                                                        </span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ format_date($history->updated_at) }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td class="text-center text-danger" colspan="6">No Records Found</td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
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
    {!! JsValidator::formRequest('App\Http\Requests\SmsRechargeHistoryRequest','#frm_save'); !!}
@endsection