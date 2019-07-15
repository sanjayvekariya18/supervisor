@extends('layouts.login.index')

@section('page_title','Login')
@section('content')
    
    <section class="login p-fixed d-flex text-center bg-primary common-img-bg">
        <!-- Container-fluid starts -->
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <!-- Authentication card start -->
                    <div class="login-card card-block auth-body mr-auto ml-auto">

                        <form method="POST" action="{{ route('login') }}"  class="md-float-material">
                            @csrf
                            <div class="text-center">
                                {{ HTML::image('images/logo.png', 'Logo',['style'=>'height: 70px !important;']) }}
                            </div>
                            <div class="auth-box">
                                <div class="row m-b-20">
                                    <div class="col-md-12">
                                        <h3 class="text-left txt-primary">Sign In</h3>
                                    </div>
                                </div>
                                <hr/>
                            
                                <div class="input-group">
                                    <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" required autofocus placeholder="Username">

                                    @if ($errors->has('username'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                    @endif
                                    <span class="md-line"></span>
                                </div>


                                <div class="input-group">
                                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="Password">

                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif

                                    <span class="md-line"></span>
                                </div>
                                
                                <div class="row m-t-30">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Sign in</button>
                                    </div>
                                </div>
                                <div class="row text-left">
                                    <div class="col-12">                                        
                                        <div class="forgot-phone text-right f-right">
                                            <a style="cursor: pointer" data-toggle="modal" data-target="#reset-password" class="text-right f-w-600 text-inverse"> Forgot Password?</a>
                                        </div>
                                    </div>
                                </div>
                                <hr/>
                                <div class="row">
                                    <div class="col-md-10">
                                        {{-- <p class="text-inverse text-left m-b-0">Thank you and enjoy our website.</p> --}}
                                        <p class="text-inverse text-left"><b>Innovating the Future</b></p>
                                    </div>
                                    <div class="col-md-2">
                                        {{-- {{ HTML::image('images/auth/Logo-small-bottom.png', 'Small Logo') }} --}}
                                    </div>
                                </div>

                            </div>
                        </form>
                        <!-- end of form -->
                    </div>
                    <!-- Authentication card end -->
                </div>
                {{-- <div class="col-md-6"></div> --}}
                <!-- end of col-sm-12 -->
            </div>
            <!-- end of row -->
        </div>
        <!-- end of container-fluid -->
        <!-- Reset Password start -->        
    </section>
    <div id="reset-password" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="login-card card-block login-card-modal" style="background:white"> 
                <form class="md-float-material" method="POST" action="{{ route('login.forgot_password') }}">
                @csrf
                    <div class="auth-box">
                        <div class="row m-b-0">
                            <div class="col-md-12">
                                <h3 class="text-left">Recover your Password</h3>
                            </div>
                        </div>
                        <div class="input-group">
                            <input type="text" name="mobile" class="form-control" placeholder="Your Mobile Number">
                            <span class="md-line"></span>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center">Reset Password</button>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-md-10">
                                <p class="text-inverse text-left m-b-0">We send password to your register number.</p>
                            </div>                            
                        </div>
                    </div>
                </form>
                <!-- end of form -->
            </div>
        </div>
    </div>    
@endsection