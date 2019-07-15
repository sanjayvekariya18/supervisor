@extends('layouts.admin.index')
@section('page_title','List of all Customers')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">List of all Customers</h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ route('admin.customers.add') }}" class="text-danger"><strong>Create a new Customer</strong></a>
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
                        {{ Form::model($customer_search,array('route' => 'admin.customers.list')) }}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('id','Customer ID')}}
                                        {{Form::text('id',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-8 p-t-25">
                                    {{ Form::submit('Search',['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('admin.customers.list',['reset'=>1]) }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Reset</a>
                                </div>

                            </div>
                        {{ Form::close() }}
                        <div class="card-header-right">
                            <i class="fa fa-window-maximize full-card"></i>
                        </div>
                    </div>
                    <div class="card-block">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>@sortablelink('id','Customer ID')</th>
                                        <th>@sortablelink('username')</th>
                                        <th>@sortablelink('company_name','Company Name')</th>
                                        <th>@sortablelink('first_name','Name')</th>
                                        <th>Contact No.</th>
                                        <th>@sortablelink('sms_balance','SMS Balance')</th>
                                        <th>Status</th>
                                        <th>Install Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $customer)
                                        <tr>
                                            <th>{{ $customer->id }}</th>
                                            <td>{{ $customer->username }}</td>
                                            <td>{{ $customer->company_name }}</td>
                                            <td>{{ $customer->first_name . ' ' . $customer->last_name}}</td>
                                            <td>{{ $customer->contact_number_1 }}</td>
                                            <td>
                                                @if ($customer->sms_balance > 0)
                                                    <strong class="text-green">{{ $customer->sms_balance }}</strong>
                                                @else
                                                    <strong class="text-danger">{{ $customer->sms_balance }}</strong>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($customer->status_id==1)
                                                    <a href="{{ route('admin.customers.status.change',[$customer->id,0]) }}" class="customer-inactive" data-status="Inactivate">
                                                        <span class="label label-success font-per-100" data-toggle="tooltip" data-placement="top" data-original-title="Click here to Inactivate">Active</span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('admin.customers.status.change',[$customer->id,1]) }}" class="customer-active" data-status="Activate">
                                                        <span class="label label-danger font-per-100" data-toggle="tooltip" data-placement="top" data-original-title="Click here to Activate">Inactive</span>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ date('d M Y',strtotime($customer->created_at)) }}</td>
                                            <td>
                                                <a href="{{ route('admin.customers.update',$customer->id) }}" data-toggle="tooltip" data-placement="top" data-original-title="Edit Customer">
                                                    <i class="fa fa-lg fa-edit text-primary"></i>
                                                </a>
                                                <a href="{{ route('admin.customers.settings.general_settings',$customer->id) }}" data-toggle="tooltip" data-placement="top" data-original-title="Customer Settings">
                                                    <i class="fa fa-lg fa-cog text-warning"></i>
                                                </a>
                                                <a href="#" onclick="window.open('{{ \URL::temporarySignedRoute('customers.login', now()->addMinutes(30),['user' => $customer->id]) }}','newwindow','fullscreen,scrollbars');return false;" data-toggle="tooltip" data-placement="top" data-original-title="Customer Login">
                                                    <i class="fa fa-lg fa-lock text-danger"></i>
                                                </a>
                                                <a class="customer-delete" href="javascript:void(0)" data-url="{{ route('admin.customers.delete',$customer->id) }}">
                                                    <i class="fa fa-lg fa-trash text-danger"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {!! $customers->appends(\Request::except('page'))->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
        
    <script type="text/javascript">
    $('a.customer-delete').click(function(){
        var url = $(this).data('url');
        console.log(url);
        $.ajax({
            type:"POST",
            data:{_token : $("input[name='_token']").val()},
            url:"{{url('admin/customers/sendOTP')}}",
            dataType:'json',
            success:function(response){
                if(response.status){
                    console.log(response);
                    swal({
                        title: "Enter OTP (One Time Password)",
                        text: "Your One Time Password has been sent to your mobile phone. Please enter the number below.",
                        type: "input",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        confirmButtonText:"Validate OTP",
                        inputPlaceholder: "Enter OTP here"
                    }, function (inputValue) {
                        if (inputValue === false) return false;
                        if (inputValue === "") {
                            swal.showInputError("OTP must required.");
                            return false;
                        }
                         $.ajax({
                            type:"POST",
                            url:url,
                            data:{_token : $("input[name='_token']").val(),otp:inputValue},
                            dataType:'json',
                            success:function(response){
                                if(response.status){
                                    swal({
                                        title: "Customer deleted successfully.",
                                        text: "",
                                        type: "success"
                                    }, function() {
                                        location.reload();
                                    });
                                }else{
                                    alert("Invalid OTP! Please enter correct OTP");
                                    $('.showSweetAlert').find("input:text").val("");
                                }    
                            }
                        });
                    });
                }
            }
        });
        /* vat url = $(this).data('url');
        swal({
			title: "Enter OTP (One Time Password)",
			text: "Your One Time Password has been sent to your mobile phone. Please enter the number below.",
			type: "input",
			showCancelButton: true,
			closeOnConfirm: false,
			inputPlaceholder: "Enter OTP here"
		}, function (inputValue) {
			if (inputValue === false) return false;
			if (inputValue === "") {
				swal.showInputError("You need to write something!");
				return false
			}
			swal("Nice!", "You wrote: " + inputValue, "success");
		}); */
    });
        $('a.customer-active').confirm({
            title: 'Heads-up!',
            content: 'Are you sure want to <strong class="text-green">Activate</strong> this Customer?',
            buttons: {
                confirm:{
                    btnClass: 'btn-warning',
                    action: function () {
                        location.href = this.$target.attr('href');
                    }
                },
                cancel: function () {
                    
                }
            }
        });

        $('a.customer-inactive').confirm({
            title: 'Heads-up!',
            content: ''+
            '<form>' +
            '<div class="form-group">' +
            '<label>Are you sure want to <strong class="text-red">Inactivate</strong> this Customer?</label>' +
            '<input type="text" placeholder="Enter reason (Max 100 Characters)" id="inactivate_reason" class="form-control" required />' +
            '</div>' +
            '</form>',
            buttons: {
                confirm:{
                    btnClass: 'btn-warning',
                    action: function () {
                        var inactivate_reason = this.$content.find('#inactivate_reason').val().trim();
                        if(!inactivate_reason){
                            $.alert('Please provide a valid reason');
                            return false;
                        }else if(inactivate_reason.length > 100){
                            $.alert('Max 100 characters are allowed');
                            return false;
                        }
                        location.href = this.$target.attr('href') + "/" + inactivate_reason;
                    }
                },
                cancel: function () {
                    
                }
            }
        });




    </script>
@endsection
