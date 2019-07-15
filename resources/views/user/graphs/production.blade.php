@extends('layouts.user.index')
@section('page_title','Production Report')
@section('content')
    <?php $p = auth()->user()->permission;  ?>
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
                <h5 class="m-b-5">Production Report</h5>
            </div>
                <hr>
            {{ Form::model($machine_search,array('route' => 'graphs.production','id'=>'submit_report')) }}
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        {{Form::label('report_types','Report Type')}}
                        <select class="form-control" id="report_types" name="report_types">
                            <option value="5_min_diff">5 Minutes Diff</option>
                        </select>
                    </div>
                </div>
                @if($p->shift)
                <div class="col-md-2" id="machine_shift_div">
                    <div class="form-group">
                        {{Form::label('shift')}}
                        {{Form::select('shift',shifts(),null,['class'=>'form-control'])}}
                    </div>
                </div>
                @endif
                @if($p->worker_account)
                <div class="col-md-2">
                    <div class="form-group">
                        {{Form::label('worker_list','Worker')}}
                        {{Form::select('worker_list',$worker_list,null,['class'=>'form-control'])}}
                    </div>
                </div>
                @endif
                @if($p->machine_group)
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
                <div class="col-md-2">
                    <div class="form-group">
                        {{Form::label('from_date','Date')}}
                        {{Form::text('from_date',null,['class'=>'form-control date-picker'])}}
                    </div>
                </div>
                <div class="col-md-2" id="to_date_div">
                    <div class="form-group">
                        {{Form::label('to_date','To Date')}}
                        {{Form::text('to_date',null,['class'=>'form-control date-picker'])}}
                        <span class="text-danger">
                            @if($errors->has('to_date'))
                                {{$errors->first('to_date')}}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="interval">Time Interval</label>
                        <select class="form-control" id="interval" name="interval">
                            <?php for($index=5;$index <=30;$index+=5){ ?>
                                <option value="{!! $index !!}">{!! $index !!} Minutes</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    {{ Form::button('Search',['type'=>'submit','data-target'=>'_self','class'=>'btn hor-grd btn-grd-info btn-round search_report']) }}
                    {{ Form::button('Export',['type'=>'submit','data-target'=>'_blank','target'=>'_blank','formaction'=>route('graphs.production.export'),'class'=>'btn hor-grd btn-grd-warning btn-round search_report']) }}
                    <a href="{{ route('graphs.production',['reset'=>1]) }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Reset</a>
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
            $("#shift option[value='0']").remove();
            $(".search_report").click(function(event) {
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                if (from_date=='') {
                    $.notify('Please select date','error');
                    return false;
                }

                var report_type = $("#report_types").val();
                if (report_type != '5_min_diff') {
                    if (to_date=='') {
                        $.notify('Please select to date','error');
                        return false;
                    }
                }

                var target = $(this).data('target');
                $("#submit_report").attr('target', target);

            });

            $("#report_types").change(function(event) {
                var report_type = $(this).val();

                $("#machines_list  option[value='all']").remove();
                if (report_type =='5_min_diff') {
                    $("#machine_shift_div").show();
                    $("#to_date_div").hide();
                }else{
                    $("#machines_list").prepend("<option value='all' selected='selected'>All</option>");
                    $("#machine_shift_div").hide();
                    $("#to_date_div").show();
                }
                
                search_data = {!! json_encode($machine_search) !!};

                if (search_data == null || typeof search_data.from_date == 'undefined') {

                    if (report_type=='5_min_diff') {
                        var prev_date = moment().format("DD-MM-YYYY");
                        $("#from_date").val(prev_date);
                    }else{
                        var prev_date = moment().subtract(1, "days").format("DD-MM-YYYY");
                        $("#from_date").val(prev_date);
                        $("#to_date").val(prev_date);
                    }

                }
                
            });

            $("#report_types").trigger('change');
        });
    </script>

    @include('elements.report')

@endsection
