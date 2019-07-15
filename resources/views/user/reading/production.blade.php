@extends('layouts.user.index')
@section('page_title','Manual Reading')
@section('content')
    <style type="text/css">
        .table td, .table th{
            padding: 0.35rem;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">Manual Reading</h5>
            </div>
                <hr>
            {{ Form::model($machine_search,array('route' => 'reading.production','id'=>'submit_report')) }}
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        {{Form::label('reading_date','Date')}}
                        {{Form::text('reading_date',isset($machine_search['reading_date'])?$machine_search['reading_date']:date('d-m-Y'),['class'=>'form-control date-picker'])}}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {{ Form::button('Search',['type'=>'submit','data-target'=>'_self','class'=>'btn hor-grd btn-grd-info btn-round search_report']) }}
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
        var search_data;
        jQuery(document).ready(function($) {
            $(".search_report").click(function(event) {
                var reading_date = $('#reading_date').val();
                if (reading_date == "") {
                    $.notify('Please select date','error');
                    return false;
                }
                var target = $(this).data('target');
                $("#submit_report").attr('target', target);
            });
        });
    </script>
    @include('elements.report')
@endsection
