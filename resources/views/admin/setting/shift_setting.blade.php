@extends('layouts.admin.index')
@section('page_title','General Settings')
@section('content')

    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">
                    Shift Settings
                </h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ url('admin/setting/shift_setting') }}" class="text-danger"><strong>Back to List</strong></a>
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
                                    <form id="shiftForm" action="{{url('admin/setting/shift_setting')}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="min_head">Customers</label>
                                                    <select class="form-control" id="user_id" name="user_id">
                                                        <option value="">Select</option>
                                                        @foreach ($users as $user)
                                                            <option value="{{$user->id}}">{{$user->company_name}}</option>
                                                        @endforeach
                                                    </select>
                                                    <p class="text-danger">{{$errors->first('user_id')}}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="machine_id">Machines</label>
                                                    <select class="form-control" name="machine_id" id="machine_id">
                                                        <option value="">All</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="shift">Shift</label>
                                                    <input type="text" name="shift" id="shift" class="form-control" />
                                                    <p class="text-danger">{{$errors->first('shift')}}</p>
                                                </div>
                                            </div>
                                        </div>
                                        {{ Form::submit("Save changes",['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                        <a href="{{ url('admin') }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Back</a>
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