<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 10]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
          <![endif]-->
        <!-- Meta -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content="Gradient Able Bootstrap admin template made using Bootstrap 4 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
        <meta name="keywords" content="Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
        <meta name="author" content="codedthemes" />

        <title>@yield('page_title'){{ ' | ' . env('APP_NAME') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600" rel="stylesheet" type="text/css">

        @include('layouts.common.css')
        @include('layouts.common.js')
        

    </head>
    <body>
        
        @include('layouts.common.pre-loader')
        @include('layouts.common.flash-messages')

        <div id="pcoded" class="pcoded">
            <div class="pcoded-container">

                @include('layouts.admin.top_nav')

                <div class="pcoded-main-container">
                
                    @include('layouts.admin.top_menu')
                
                    <div class="pcoded-wrapper">
                        <div class="pcoded-content">
                            <div class="pcoded-inner-content">
                                <div class="main-body">
                                    <div class="page-wrapper">
                                        
                                        @yield('content')
                                        
                                    </div>
                
                                    {{-- @include('layouts.admin.style_selector') --}}
                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Warning Section Starts -->
        <!-- Older IE warning message -->
        <!--[if lt IE 10]>
        <div class="ie-warning">
            <h1>Warning!!</h1>
            <p>You are using an outdated version of Internet Explorer, please upgrade <br/>to any of the following web browsers to access this website.</p>
            <div class="iew-container">
                <ul class="iew-download">
                    <li>
                        <a href="http://www.google.com/chrome/">
                            <img src="../files/assets/images/browser/chrome.png" alt="Chrome">
                            <div>Chrome</div>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.mozilla.org/en-US/firefox/new/">
                            <img src="../files/assets/images/browser/firefox.png" alt="Firefox">
                            <div>Firefox</div>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.opera.com">
                            <img src="../files/assets/images/browser/opera.png" alt="Opera">
                            <div>Opera</div>
                        </a>
                    </li>
                    <li>
                        <a href="https://www.apple.com/safari/">
                            <img src="../files/assets/images/browser/safari.png" alt="Safari">
                            <div>Safari</div>
                        </a>
                    </li>
                    <li>
                        <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                            <img src="../files/assets/images/browser/ie.png" alt="">
                            <div>IE (9 & above)</div>
                        </a>
                    </li>
                </ul>
            </div>
            <p>Sorry for the inconvenience!</p>
        </div>
        <![endif]-->
        <!-- Warning Section Ends -->
        
        <script type="text/javascript" src="{{ URL::asset('js/script.js') }}"></script>

        <script type="text/javascript">
            // Multiple swithces
            var elem = Array.prototype.slice.call(document.querySelectorAll('.js-primary'));
            elem.forEach(function(checkbox) {
                var switchery = new Switchery(checkbox, { color: '#4099ff', jackColor: '#fff' });
            });

            $(document).ready(function(){
                $('#user_id').on('change',function(){
                    var user_id = $(this).val();
                    if(user_id == ""){
                        $('#machine_id').children().not(':first').remove();
                        return false;
                    }
                    $('#machine_id').children().not(':first').remove();
                    $.ajax({
                        type: 'GET',
                        dataType:'json',
                        url : '{{url("admin/setting/getMachines")}}/'+user_id,
                        success:function(response){
                            
                            $.each(response,function(k,v){
                                $('#machine_id').append("<option value='"+v.id+"'>"+v.machine_number+"</option>");
                            });
                        }
                    });
                });
            });
        </script>
    </body>
</html>
