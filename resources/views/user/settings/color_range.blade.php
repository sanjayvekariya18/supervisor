@extends('layouts.user.index')
@section('page_title','Color Range')
@section('content')

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
                                <!-- Nav tabs -->
                                @include('user.settings.nav_tabs', ['current_tab' => $current_tab])
                                <!-- Tab panes -->
                                <div class="tab-content card-block">
                                    {{ Form::model($data,array('route' => ['settings.color.range'],'id'=>'frm_color_range')) }}
                                    <div class="tab-pane {{ $current_tab=='color_range' ? 'active' : '' }}" id="color_range" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('','From Stitches')}}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('','To Stitches')}}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('','Color')}}
                                                </div>
                                            </div>
                                        </div>
                                        <div id="color_range_rows">
                                            
                                            @forelse ($data as $color_range)
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {{Form::number('from_stitches[]',$color_range->from_stitches,['class'=>'form-control from_stitches','readonly'])}}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {{Form::number('to_stitches[]',$color_range->to_stitches,['class'=>'form-control to_stitches'])}}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <select class="form-control" name="color_code[]">
                                                                @foreach ($range_colors as $color_code => $color_name)
                                                                    <option style="color:{{$color_code}}" {{ $color_range->color_code == $color_code ? 'selected' : '' }} value="{{$color_code}}">{{$color_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {{Form::number('from_stitches[]',1,['class'=>'form-control from_stitches','readonly'])}}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            {{Form::number('to_stitches[]',100,['class'=>'form-control to_stitches'])}}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <select class="form-control" name="color_code[]">
                                                                @foreach ($range_colors as $color_code => $color_name)
                                                                    <option style="color:{{$color_code}}" value="{{$color_code}}">{{$color_name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforelse

                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <a id="add_more_range" href="javascript:void(0)" class="text-primary"><i class="fa fa-plus"></i> Add more range</a>
                                                    &nbsp;
                                                    <a id="remove_last_range" href="javascript:void(0)" class="text-danger"><i class="fa fa-trash"></i> Remove last range</a>
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
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $("#add_more_range").click(function(event) {
            if(check_range()){

                var last_to_stitches = parseInt($('#color_range_rows').find('.to_stitches').last().val());

                var _html = $('#color_range_rows').find('.row').last().html();
                $("#color_range_rows").append('<div class="row">' + _html + '</div>');

                $('#color_range_rows').find('.from_stitches').last().val(last_to_stitches);
                last_to_stitches += 1;
                $('#color_range_rows').find('.to_stitches').last().val(last_to_stitches);
                enable_ctl();

            }
        });

        $("#frm_color_range").submit(function(event) {
            if (check_range()) {
                return true;
            }else{
                return false;
            }
        });

        $('#remove_last_range').confirm({
            title: 'Heads-up!',
            content: 'Are you sure want to remove last range?',
            buttons: {
                confirm:{
                    btnClass: 'btn-warning',
                    action: function () {
                        var total_rows = $('#color_range_rows').find('.row').length;
                        if (total_rows > 1) {
                            $('#color_range_rows').find('.row').last().remove();
                            enable_ctl();
                        }else{
                            $.alert('<span class="text-danger">At least one range required</span>');
                        }
                    }
                },
                cancel: function () {
                    
                }
            }
        });

    });

    function enable_ctl() {
        $('#color_range_rows').find('.to_stitches').prop('readonly',true).last().prop('readonly',false);
        var total_rows = $('#color_range_rows').find('.row').length;
        if (total_rows <= 1) {
            $('#remove_last_range').hide();
        }else{
            $('#remove_last_range').show();
        }
    }

    function check_range() {
        var last_from_stitches = parseInt($('#color_range_rows').find('.from_stitches').last().val());
        var last_to_stitches = parseInt($('#color_range_rows').find('.to_stitches').last().val());

        // Check if to_stitches is more then zero
        if (isNaN(last_to_stitches) || last_to_stitches <= 0) {
            $.notify('Please enter a valid number');
            $('#color_range_rows').find('.to_stitches').last().focus();
            return false;
        }

        // Check if both are same or not
        if (last_from_stitches >= last_to_stitches) {
            $.notify('To Stitches must be greater than From Stitches');
            $('#color_range_rows').find('.to_stitches').last().focus();
            return false;
        }

        return true;

    }

    enable_ctl();
</script>

@endsection