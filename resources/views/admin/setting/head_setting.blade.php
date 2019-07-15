@extends('layouts.admin.index')
@section('page_title','General Settings')
@section('content')

    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">
                    Head Settings
                </h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ url('admin/setting/head_setting') }}" class="text-danger"><strong>Back to List</strong></a>
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
                                    <form action="{{url('admin/setting/head_setting')}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="min_head">Minimum Head</label><b class="err-asterisk"></b>
                                                    <input class="form-control" name="head[min_head]" value="{{$head->min_head}}" type="number" id="min_head">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="">Display Type</label><b class="err-asterisk"></b>
                                                <div class="form-radio">
                                                    <div class="radio radio-inline">
                                                        <label>
                                                            <input class="form-control" type="radio" name="head[type]" value="number" {{$head->type == "number" ? "checked" : ""}}>
                                                            <i class="helper"></i>Number
                                                        </label>
                                                    </div>
                                                    <div class="radio radio-inline">
                                                        <label>
                                                            <input class="form-control" type="radio" name="head[type]" value="on_off" {{$head->type == "on_off" ? "checked" : ""}}>
                                                            <i class="helper"></i>On Off
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{ Form::submit("Save changes",['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                        <a href="{{ route('admin.customers.list') }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Back</a>
                                    </form>
                                </div>                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
@endsection