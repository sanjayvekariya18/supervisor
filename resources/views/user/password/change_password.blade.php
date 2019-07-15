@extends('layouts.user.index')
@section('page_title','Change Password')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">Change Password</h5>
            </div>
        </div>
    </div>
    <!-- Page header end -->

    <!-- Page body start -->
    <div class="page-body">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-header p-b-5">
                        <form id="change_password" action="{{url('password/change_password')}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('oldPassword','Old Password')}}
                                        {{Form::text('oldPassword',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('newPassword','New Password')}}
                                        {{Form::text('newPassword',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('cnfPassword','Confirm Password')}}
                                        {{Form::text('cnfPassword',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-8 p-t-25">
                                    {{ Form::button('Update',['class'=>'btn hor-grd btn-grd-info btn-round update']) }}
                                    <a href="{{ route('password.change_password',['reset'=>1]) }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Reset</a>
                                </div>
                            </div>
                        {{ Form::close() }}
                        <div class="card-header-right">
                            <i class="fa fa-window-maximize full-card"></i>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
        
    <script type="text/javascript">
        $(".update").on("click",function(e){            
            if($("#newPassword").val() != $("#cnfPassword").val()){
                alert("Conform Password Not Match");
            }else{
               $("#change_password").submit();
            }
        });        
    </script>
    
@endsection
