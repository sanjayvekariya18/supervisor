@extends('layouts.user.index')
@section('page_title','List of all Workers')
@section('content')
    
    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">List of all Workers</h5>
            </div>
            <div class="page-header-breadcrumb">
                <a href="{{ route('workers.add') }}" class="text-danger"><strong>Create a new Worker</strong></a>
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
                        {{ Form::model($worker_search,array('route' => 'workers.list')) }}
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {{Form::label('worker_id','Worker ID')}}
                                        {{Form::text('worker_id',null,['class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-8 p-t-25">
                                    {{ Form::submit('Search',['class'=>'btn hor-grd btn-grd-info btn-round']) }}
                                    <a href="{{ route('workers.list',['reset'=>1]) }}" class="btn hor-grd btn-grd-inverse btn-round hover-white">Reset</a>
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
                                        <th>@sortablelink('worker_id','Worker ID')</th>
                                        <th>@sortablelink('first_name','Name')</th>
                                        <th>Contact No.</th>
                                        <th>Shift</th>
                                        <th>M. No</th>
                                        <th>Salary</th>
                                        <th>Per Day</th>
                                        {{-- <th>Status</th> --}}
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($workers as $worker)
                                        <tr>
                                            <th>{{ $worker->worker_id }}</th>
                                            <td>{{ $worker->first_name . ' ' . $worker->last_name}}</td>
                                            <td>{{ $worker->contact_number_1 }}</td>
                                            <td>
                                                @if ($worker->id == $worker->day_worker_id)
                                                    Day
                                                @elseif($worker->id == $worker->night_worker_id)
                                                    Night
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if ($worker->machine_number)
                                                    <label class="label label-primary font-per-100">{{$worker->machine_number}}</label>
                                                @else
                                                    <label class="label label-danger">No Machines Assigned</label>
                                                @endif

                                            </td>
                                            <td>{{ $worker->salary }}</td>
                                            <td>
                                                <?php 
                                                    $total_days = date('t');
                                                    echo $total_days_salary = round($worker->salary / $total_days);
                                                ?>
                                            </td>
                                            {{-- <td>
                                                @if ($worker->status_id==1)
                                                    <a href="{{ route('workers.status.change',[$worker->id,0]) }}" class="worker-inactive" data-status="Inactivate">
                                                        <span class="label label-success font-per-100" data-toggle="tooltip" data-placement="top" data-original-title="Click here to Inactivate">Active</span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('workers.status.change',[$worker->id,1]) }}" class="worker-active" data-status="Activate">
                                                        <span class="label label-danger font-per-100" data-toggle="tooltip" data-placement="top" data-original-title="Click here to Activate">Inactive</span>
                                                    </a>
                                                @endif
                                            </td> --}}
                                            <td>
                                                <a href="{{ route('workers.update',$worker->id) }}" data-toggle="tooltip" data-placement="top" data-original-title="Edit Worker"><i class="fa fa-lg fa-edit text-primary"></i></a>
                                                <a href="{{ route('workers.update',$worker->id) }}" data-toggle="tooltip" data-placement="top" data-original-title="Edit Worker"><i class="fa fa-lg fa-eye text-primary"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {!! $workers->appends(\Request::except('page'))->render() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body end -->
        
    <script type="text/javascript">
        $('a.worker-active').confirm({
            title: 'Heads-up!',
            content: 'Are you sure want to <strong class="text-green">Activate</strong> this Worker?',
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

        $('a.worker-inactive').confirm({
            title: 'Heads-up!',
            content: ''+
            '<form>' +
            '<div class="form-group">' +
            '<label>Are you sure want to <strong class="text-red">Inactivate</strong> this Worker?</label>' +
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
