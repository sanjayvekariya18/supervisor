@extends('layouts.user.index')
@section('page_title','List of all disconnected Machines')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Page header start -->
    <div class="page-header card m-t-40">
        <div class="card-block caption-breadcrumb">
            <div class="breadcrumb-header">
                <h5 class="m-b-5">List of all disconnected Machines</h5>
            </div>
        </div>
    </div>
    <!-- Page header end -->

    <!-- Page body start -->
    <div class="page-body">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-lg-12">
                <div class="card">
                    <div class="card-block">
                        
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Machine Number</th>
                                        <th>Shift</th>
                                        <th>Last Connected</th>
                                    </tr>
                                </thead>
                                <tbody id="disconnected_list">
                                    @forelse ($machines as $machine)
                                        <tr>
                                            <td>{{$machine->machine_number}}</td> 
                                            <td>{{($machine->shift == 1) ? "DAY" : "NIGHT"}}</td> 
                                            <td>{{$machine->stop_since_hrs}} Ago</td> 
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">Currently There is no any disconnected Machine.</td> 
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
