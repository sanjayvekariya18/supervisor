@extends('layouts.admin.index')
@section('page_title','Profile')
@section('content')
    
    <!-- Page-body start -->
    <div class="page-body m-t-40" >
        <div class="col-sm-12">
            <div class="content social-timeline">
                <!-- Row Starts -->
                <div class="row">
                    <div class="col-md-12">
                        <!-- Social wallpaper start -->
                        <div class="social-wallpaper">
                            <img src="/images/bg-img1.jpg" class="img-fluid width-100" alt="" />
                            {{-- <div class="profile-hvr"> --}}
                                {{-- <i class="icofont icofont-ui-edit p-r-10"></i> --}}
                                {{-- <i class="icofont icofont-ui-delete"></i> --}}
                            {{-- </div> --}}
                        </div>
                        <!-- Social wallpaper end -->
                    </div>
                </div>
                <!-- Row end -->
                <!-- Row Starts -->
                <div class="row">
                    @include('admin.admin.profile_box')
                    <div class="col-xl-9 col-lg-8 col-md-8 col-xs-12 ">
                        @include('admin.admin.nav', ['current_tab' => $current_tab])
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <!-- About tab start -->
                            <div class="tab-pane active" id="about">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-header-text">Basic Information</h5>
                                            </div>
                                            <div class="card-block">
                                                <div id="edit-info" class="row">
                                                    <div class="col-lg-12 col-md-12">
                                                        {{ Form::model([],array('route' => ['admin.profile.change.password'],'id'=>'frm_save')) }}
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{Form::label('old_password')}}<b class="err-asterisk"></b>
                                                                        {{Form::password('old_password',['class'=>'form-control form-control-capitalize'])}}
                                                                        @if ($errors->has('old_password'))
                                                                            <p class="error">{{ $errors->first('old_password') }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{Form::label('new_password')}}<b class="err-asterisk"></b>
                                                                        {{Form::password('new_password',['class'=>'form-control form-control-capitalize'])}}
                                                                        @if ($errors->has('new_password'))
                                                                            <p class="error">{{ $errors->first('new_password') }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{Form::label('confirm_password')}}<b class="err-asterisk"></b>
                                                                        {{Form::password('confirm_password',['class'=>'form-control form-control-capitalize'])}}
                                                                        @if ($errors->has('confirm_password'))
                                                                            <p class="error">{{ $errors->first('confirm_password') }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    {{ Form::submit('Update',['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                                                </div>
                                                            </div>
                                                        {{ Form::close() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- About tab end -->
                        </div>
                        <!-- Row end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page-body end -->
@endsection
