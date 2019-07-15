@extends('layouts.admin.index')
@section('page_title','Reports Settings')
@section('content')
@php 
    use Illuminate\Support\Str;
@endphp

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
                            
                            <div class="row">
                                <div class="width-100">
                                    @include('admin.customers.settings.nav_tabs', ['current_tab' => $current_tab,'customer_id'=>$customer->id])
                                    <!-- Tab panes -->
                                    <div class="tab-content card-block">
                                        {{ Form::model($customer,array('route' => ['admin.customers.settings.reports_settings',$customer->id],'id'=>'frm_customer')) }}
                                        <div class="tab-pane active" id="reports_settings" role="tabpanel">
                                            @foreach ($reports_settings as $report_name => $columns_setting)
                                                <div class="row">
                                                        

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <h5>Columns</h5>
                                                                <em>Hide and show in Reports and on Dashboard</em>
                                                            </div>
                                                        </div>

                                                        @foreach ($columns_setting as $key_name => $key_value)
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    @if ($key_value != '0')
                                                                        @php ($_checked = 'checked')
                                                                    @else
                                                                        @php ($_checked = '')
                                                                    @endif

                                                                    <div class="border-checkbox-section">
                                                                        <div class="border-checkbox-group border-checkbox-group-primary col-md-2">
                                                                            <input class="border-checkbox" {{ $_checked }} name="report[{{$report_name}}][{{$key_name}}]" value="1" type="checkbox" id="{{$report_name}}_{{ $key_name }}">
                                                                            <label class="border-checkbox-label" for="{{$report_name}}_{{ $key_name }}">{{ Str::title($key_name) }}</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        
                                                </div>
                                            @endforeach
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

@endsection