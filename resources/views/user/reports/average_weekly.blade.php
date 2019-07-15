@extends('layouts.user.index')
@section('page_title','Average WeeklyReport')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">Average Report</h5>
            </div>
                <hr>
            {{ Form::model($search,array('route' => 'reports.average_weekly','id'=>'submit_report')) }}
            <div class="row">                
                <div class="col-md-2">
                    <div class="form-group">
                        {{Form::label('from_date','Date')}}
                        {{Form::text('from_date',null,['class'=>'form-control date-picker'])}}
                    </div>
                </div>
                @if(hasAccess('worker_account'))
                <div class="col-md-2">
                    <div class="form-group">
                        {{Form::label('worker_list','Worker')}}
                        {{Form::select('worker_list',$worker_list,null,['class'=>'form-control'])}}
                    </div>
                </div>
                @endif
                @if(hasAccess('machine_group'))
                <div class="col-md-2">
                    <div class="form-group">
                        {{Form::label('group_id','Group')}}
                        {{Form::select('group_id',$group_list,null,['id'=>'groups_list','class'=>'form-control'])}}
                    </div>
                </div>
                @endif
                <div class="col-md-2">
                    <div class="form-group">
                        {{Form::label('machine_no','Machine No')}}
                        {{Form::select('machine_no',$machine_list,null,['id'=>'machines_list','class'=>'form-control'])}}
                    </div>
                </div>                
            </div>
            <div class="row">
                <div class="col-md-12">
                    {{ Form::button('Search',['type'=>'submit','data-target'=>'_self','class'=>'btn hor-grd btn-grd-info btn-round search_report']) }}
                    {{ Form::button('Export',['type'=>'submit','data-target'=>'_blank','target'=>'_blank','formaction'=>route('reports.average_weekly.export'),'class'=>'btn hor-grd btn-grd-warning btn-round search_report']) }}
                    <a href="{{ route('reports.average_weekly',['reset'=>1]) }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Reset</a>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
    <!-- Page header end -->

    <!-- Page body start -->
    <div class="page-body">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header p-b-5">
                    </div>
                    <div class="card-block">
                        @php
                            echo $report_html;
                        @endphp
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
        
    <script type="text/javascript">        
        jQuery(document).ready(function($) { 
            $("#machines_list").prepend("<option value='all' selected='selected'>All</option>");
            $(".search_report").click(function(event) {
                var from_date = $('#from_date').val();
                if (from_date=='') {
                    $.notify('Please select date','error');
                    return false;
                }                
                var target = $(this).data('target');
                $("#submit_report").attr('target', target);
            });
        });
    </script>
@endsection
