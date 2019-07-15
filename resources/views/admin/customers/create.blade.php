@extends('layouts.admin.index')
@section('page_title','Create a new Customer')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">
                    @if ($action=='create')
                        Create a new Customer
                        @php 
                            $disabled='';
                            $form_route = ['admin.customers.add'];
                            $submit_text = 'Create';
                        @endphp
                    @else
                        Update Customer
                        @php 
                            $disabled='readonly';
                            $form_route=['admin.customers.update',$user->id];
                            $submit_text = 'Update';
                        @endphp
                    @endif
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
                            {{ Form::model($user,array('route' => $form_route,'id'=>'frm_customer')) }}
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {{Form::label('id','Customer ID')}}<b class="err-asterisk"></b>
                                        {{Form::text('id',null,['class'=>'form-control form-control-uppercase',$disabled])}}
                                        @if ($errors->has('id'))
                                            <p class="error">{{ $errors->first('id') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Model Version</label><b class="err-asterisk"></b>
                                        <select name="permission_id" id="permission_id" class="form-control">
                                            <option value="">Select</option>
                                            @foreach($permissions as $permission)
                                                @if($permission->id == $user->permission_id)
                                                    <option value="{{$permission->id}}" selected>{{$permission->name}}</option>
                                                @else
                                                    <option value="{{$permission->id}}">{{$permission->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @if ($errors->has('permission_id'))
                                            <p class="error">{{ $errors->first('permission_id') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {{Form::label('username','Username')}}<b class="err-asterisk"></b>
                                        {{Form::text('username',null,['class'=>'form-control',$disabled])}}
                                        @if ($errors->has('username'))
                                            <p class="error">{{ $errors->first('username') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {{Form::label('password_disp','Password')}}<b class="err-asterisk"></b>
                                        {{Form::text('password_disp',null,['class'=>'form-control'])}}
                                        @if ($errors->has('password_disp'))
                                            <p class="error">{{ $errors->first('password_disp') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{Form::label('company_name','Company Name')}}<b class="err-asterisk"></b>
                                        {{Form::text('company_name',null,['class'=>'form-control'])}}
                                        @if ($errors->has('company_name'))
                                            <p class="error">{{ $errors->first('company_name') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {{Form::label('first_name','First Name')}}<b class="err-asterisk"></b>
                                        {{Form::text('first_name',null,['class'=>'form-control'])}}
                                        @if ($errors->has('first_name'))
                                            <p class="error">{{ $errors->first('first_name') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {{Form::label('last_name','Last Name')}}
                                        {{Form::text('last_name',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {{Form::label('contact_number_1','Contact Number 1')}}<b class="err-asterisk"></b>
                                        {{Form::text('contact_number_1',null,['class'=>'form-control'])}}
                                        @if ($errors->has('contact_number_1'))
                                            <p class="error">{{ $errors->first('contact_number_1') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {{Form::label('contact_number_2','Contact Number 2')}}
                                        {{Form::text('contact_number_2',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-3">
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
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {{Form::label('address_2','Address Line 2')}}
                                        {{Form::text('address_2',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::submit($submit_text,['class'=>'btn hor-grd btn-grd-info btn-round']) }}
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
 {!! JsValidator::formRequest('App\Http\Requests\UserRequest','#frm_customer'); !!}

@endsection