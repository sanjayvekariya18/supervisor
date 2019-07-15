@extends('layouts.user.index')
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
                            <img src="../images/bg-img1.jpg" class="img-fluid width-100" alt="" />
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
                    @include('user.customers.profile_box')
                    <div class="col-xl-9 col-lg-8 col-md-8 col-xs-12 ">
                        @include('user.customers.nav', ['current_tab' => $current_tab])
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
                                                        {{ Form::model($customer,array('route' => ['customers.profile.about',$customer->id],'id'=>'frm_customer')) }}
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        {{Form::label('company_name','Company Name')}}<b class="err-asterisk"></b>
                                                                        {{Form::text('company_name',null,['class'=>'form-control'])}}
                                                                        @if ($errors->has('company_name'))
                                                                            <p class="error">{{ $errors->first('company_name') }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        {{Form::label('first_name','First Name')}}<b class="err-asterisk"></b>
                                                                        {{Form::text('first_name',null,['class'=>'form-control form-control-capitalize'])}}
                                                                        @if ($errors->has('first_name'))
                                                                            <p class="error">{{ $errors->first('first_name') }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        {{Form::label('last_name','Last Name')}}
                                                                        {{Form::text('last_name',null,['class'=>'form-control form-control-capitalize'])}}
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        {{Form::label('contact_number_1','Contact Number 1')}}<b class="err-asterisk"></b>
                                                                        {{Form::text('contact_number_1',null,['class'=>'form-control'])}}
                                                                        @if ($errors->has('contact_number_1'))
                                                                            <p class="error">{{ $errors->first('contact_number_1') }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        {{Form::label('contact_number_2','Contact Number 2')}}
                                                                        {{Form::text('contact_number_2',null,['class'=>'form-control'])}}
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        {{Form::label('email','Email')}}
                                                                        {{Form::text('email',null,['class'=>'form-control'])}}
                                                                        @if ($errors->has('email'))
                                                                            <p class="error">{{ $errors->first('email') }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        {{Form::label('sms_notification_numbers','SMS Notification Numbers by Comma separated | NOTE : SMS will be consumed for each number (Max 20 Numbers)')}}
                                                                        {{Form::text('sms_notification_numbers',null,['class'=>'form-control'])}}
                                                                        @if ($errors->has('sms_notification_numbers'))
                                                                            <p class="error">{{ $errors->first('sms_notification_numbers') }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        {{Form::label('whatsapp_notification_numbers','WhatsApp Notification Numbers by Comma separated (Max 20 Numbers)')}}
                                                                        {{Form::text('whatsapp_notification_numbers',null,['class'=>'form-control'])}}
                                                                        @if ($errors->has('whatsapp_notification_numbers'))
                                                                            <p class="error">{{ $errors->first('whatsapp_notification_numbers') }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{Form::label('address_1','Address Line 1')}}
                                                                        {{Form::text('address_1',null,['class'=>'form-control'])}}
                                                                        @if ($errors->has('address_1'))
                                                                            <p class="error">{{ $errors->first('address_1') }}</p>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        {{Form::label('address_2','Address Line 2')}}
                                                                        {{Form::text('address_2',null,['class'=>'form-control'])}}
                                                                        @if ($errors->has('address_2'))
                                                                            <p class="error">{{ $errors->first('address_2') }}</p>
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
